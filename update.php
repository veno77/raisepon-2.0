<?php
include ("common.php");
include ("dbconnect.php");

$check_list = $new_pon_id = $new_olt = $state =  "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($_POST["olt_port"]) {
		$new_olt = test_input($_POST["olt_port"]);
	}
	if ($_POST["pon_port"]) {
		$new_pon_id = test_input($_POST["pon_port"]);
	}
	if ($_POST["check_list"]) {
		$check_list = $_POST["check_list"];
	}
	if ($new_olt !== '' && $new_pon_id !== '' && $check_list !== '') {
		foreach($check_list as $customer_id) {	
			try {
				$result = $db->query("SELECT CUSTOMERS.ID, SN, PON_ONU_ID, CUSTOMERS.ONU_MODEL, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, CUSTOMERS.LINE_PROFILE, CUSTOMERS.SERVICE_PROFILE, OLT.ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RW as RW, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, ONU.ID, ONU.PORTS, LINE_PROFILE.LINE_PROFILE_ID, SERVICE_PROFILE.SERVICE_PROFILE_ID from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN ONU on CUSTOMERS.ONU_MODEL=ONU.ID LEFT JOIN LINE_PROFILE on CUSTOMERS.LINE_PROFILE=LINE_PROFILE.ID LEFT JOIN SERVICE_PROFILE on CUSTOMERS.SERVICE_PROFILE=SERVICE_PROFILE.ID  where CUSTOMERS.ID = '$customer_id'");
			} catch (PDOException $e) {
				echo "Connection Failed:" . $e->getMessage() . "\n";
				exit;
			}
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				$olt = $row["OLT"];
				$old_olt_ip_address = $row["IP_ADDRESS"];
				$olt_rw = $row["RW"];
				$port_id = $row["PORT_ID"];
				$old_pon_onu_id = $row["PON_ONU_ID"];
				$sn = $row["SN"];
				$slot_id = $row["SLOT_ID"];
				$pon_id = $row["PON_ID"];
				$line_profile = $row["LINE_PROFILE_ID"];
				$service_profile = $row["SERVICE_PROFILE_ID"];
				$ports = $row["PORTS"];
			}
			if ($olt == $new_olt && $pon_id == $new_pon_id) {
				exit("Same OLT and PON PORT");
			}else{
				// FIND FREE ONU_ID
				try {
					$result = $db->query("SELECT PON_ONU_ID from CUSTOMERS where OLT='$new_olt' and PON_PORT='$new_pon_id'");
				} catch (PDOException $e) {
					echo "Connection Failed:" . $e->getMessage() . "\n";
					exit;
				}
				$arr2 = array();
				while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
					array_push($arr2, $row{'PON_ONU_ID'});
				}
				$arr1 = range(1,128);
				$arr3 = array_diff($arr1,$arr2);
				$pon_onu_id = array_values($arr3)[0];
				//UPDATE ONU
				try {
					$result = $db->query("UPDATE CUSTOMERS SET OLT = '$new_olt', PON_PORT = '$new_pon_id', PON_ONU_ID = '$pon_onu_id' where ID = '$customer_id'");
				} catch (PDOException $e) {
					echo "Connection Failed:" . $e->getMessage() . "\n";
        			exit;
				}
				//DELETE ONU from OLD OLT/INTERFACE
				$old_big_onu_id = type2id($slot_id, $port_id, $old_pon_onu_id);
				$destroy_oid = "iso.3.6.1.4.1.8886.18.3.1.3.1.1.19." . $old_big_onu_id;
				$session = new SNMP(SNMP::VERSION_2C, $old_olt_ip_address, $olt_rw);
				$session->set($destroy_oid,'i', '6');
				if ($session->getError())
					var_dump($session->getError());
				//CREATE NEW ONU
				try {
					$result = $db->query("SELECT INET_NTOA(IP_ADDRESS) as IP_ADDRESS, RW, OLT_MODEL.TYPE from OLT LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID where OLT.ID='$new_olt'");
				} catch (PDOException $e) {
					echo "Connection Failed:" . $e->getMessage() . "\n";
					exit;
				}
        			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
					$olt_ip_address = $row["IP_ADDRESS"];
					$olt_rw = $row["RW"];
					$type = $row["TYPE"];
				}
				try {
					$result = $db->query("SELECT * from PON where ID='$new_pon_id'");
				} catch (PDOException $e) {
					echo "Connection Failed:" . $e->getMessage() . "\n";
					exit;
				}
				while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
					$port_id = $row["PORT_ID"];
					$slot_id = $row["SLOT_ID"];
				}
                                $big_onu_id = type2id($slot_id, $port_id, $pon_onu_id);
				$sn_oid = "iso.3.6.1.4.1.8886.18.3.1.3.1.1.2." . $big_onu_id;
				$line_profile_id = "iso.3.6.1.4.1.8886.18.3.1.3.1.1.6." . $big_onu_id;
				$svr_profile_id = "iso.3.6.1.4.1.8886.18.3.1.3.1.1.8." . $big_onu_id;
				$row_status = "iso.3.6.1.4.1.8886.18.3.1.3.1.1.19." . $big_onu_id;

				//EXECUTE SNMPSET TO ADD ONU
				$session = new SNMP(SNMP::VERSION_2C, $olt_ip_address, $olt_rw);
				$session->set(array($sn_oid, $line_profile_id, $svr_profile_id, $row_status), array('s', 'i', 'i', 'i'), array($sn, $line_profile, $service_profile, '4'));
				if ($session->getError())
					exit(var_dump($session->getError()));


				//RENAME RRD FILES
				$rrd_name = array("traffic", "unicast", "broadcast", "multicast", "power");
				foreach ($rrd_name as $rrd) {
				$old_rrd_file = dirname(__FILE__) . "/rrd/" . $old_olt_ip_address . "_" . $old_big_onu_id . "_" . $rrd . ".rrd";
				$new_rrd_file = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_" . $rrd . ".rrd";
				rename($old_rrd_file, $new_rrd_file);
				}
				for ($i=1; $i <= $ports; $i++) {
					$old_rrd_file = dirname(__FILE__) . "/rrd/" . $old_olt_ip_address . "_" . $old_big_onu_id . "_ethernet_" . $i . ".rrd";
					$new_rrd_file = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_ethernet_" . $i . ".rrd";
					rename($old_rrd_file, $new_rrd_file);
                                }

				$old_rrd_stale_files = dirname(__FILE__) . "/rrd/" . $old_olt_ip_address . "_" . $old_big_onu_id . "_*.*";
				array_map('unlink', glob($old_rrd_stale_files));
				print "Customer " . $customer_id . " Updated!!!<BR>";
				sleep(1);
			}
		}
	}
}

?>
