<?php
include_once("dbconnect.php");
include_once("classes/snmp_class.php");
include_once("classes/customers_class.php");
include_once("classes/pon_class.php");

$snmp_obj = new snmp_oid();

$ip_address_state = array();

try {
	$conn = db_connect::getInstance();
	$result = $conn->db->query("SELECT ID, NAME, MODEL, INET_NTOA(IP_ADDRESS) as IP_ADDRESS, RO, RW from OLT");
} catch (PDOException $e) {
	$error = "Connection Failed:" . $e->getMessage() . "\n";
	return $error;
}
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$ip_address = $row["IP_ADDRESS"];
	$olt_status_oid = $snmp_obj->get_pon_oid("olt_status_oid", "OLT");
	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
	$olt_status = $session->get($olt_status_oid);
	if (!empty($olt_status)) {
		$ip_address_state[$ip_address] = "up";
	}else {
		$ip_address_state[$ip_address] = "down";
	}
}


try {
	$result = $db->query("SELECT CUSTOMERS.ID as ID, CUSTOMERS.NAME, CUSTOMERS.ADDRESS, SN, SERVICE_PROFILE.PORTS, SERVICE_PROFILE.HGU, SERVICE_PROFILE.RF, OLT.NAME as OLT_NAME, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO as RO, OLT_MODEL.TYPE, PON.NAME as PON_NAME, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE, PON_ONU_ID from CUSTOMERS LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN SERVICE_PROFILE on SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID");
} catch (PDOException $e) {
	echo "Connection Failed:" . $e->getMessage() . "\n";
	exit;
}
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	
	
	$id = $row['ID'];
	$pon_type = $row{'PON_TYPE'};
	$hgu = $row{'HGU'};
	
 	$catv_input_id = $row{'SLOT_ID'} * 10000000 + $row{'PORT_ID'} * 100000 + $row{'PON_ONU_ID'} * 1000 + 160;	
	$rf = $row{'RF'};
	$sn = $row["SN"];
	$ip_address = $row["IP_ADDRESS"];
	$customers_obj = new customers();
	$big_onu_id = $customers_obj->type2id($row{'SLOT_ID'}, $row{'PORT_ID'}, $row{'PON_ONU_ID'});
		
	
	if ($row{'PON_TYPE'} == "GPON") {
		
		if ($row{'PON_ONU_ID'} < 100) {
			$index_2 = 10000000 * $row{'SLOT_ID'} + 100000 * $row{'PORT_ID'} + 1000 * $row{'PON_ONU_ID'} + 1;
		}else{
			$index_2 = (3<<28)+(10000000 * $row{'SLOT_ID'} + 100000 * $row{'PORT_ID'} + 1000 * ($row{'PON_ONU_ID'}%100) + 1);
		}
		$catv_input_id = $index_2;
	}
	if ($row{'PON_TYPE'} == "EPON") {
		$index_2 = $row{'SLOT_ID'} * 10000000 + $row{'PORT_ID'} * 100000 + $row{'PON_ONU_ID'};
	}
	$olt_ip_address = $row["IP_ADDRESS"];	
	$error = "0";
	$rrd_power = dirname(__FILE__) . "/rrd/" . $sn . "_power.rrd";
	if(!is_file($rrd_power)) {
		$customers_obj = new customers();
		$customers_obj->setSn($sn);
		$error = $customers_obj->onu_power_rrd();
		if (!empty($error)) {
			return $error;
		}
	}
	
	
	if(isset($row{'IP_ADDRESS'})) { 
		if ($ip_address_state[$ip_address] == "up") {
			$status = "0";
			//GET STATUS via SNMP
			snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
			$status_oid = $snmp_obj->get_pon_oid("onu_status_oid", $row{'PON_TYPE'}) . "." . $big_onu_id;
			$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
			$status = $session->get($status_oid);				
			if ($status == "1") {		
				echo $sn . "\n";
				//Onu Power
				$recv_power_oid = $snmp_obj->get_pon_oid("onu_recv_power_oid", $row{'PON_TYPE'}) . "." . $index_2;
				$send_power_oid = $snmp_obj->get_pon_oid("onu_send_power_oid", $row{'PON_TYPE'}) . "." . $index_2;
				//OLT RX Power
				$olt_rx_power_oid = $snmp_obj->get_pon_oid("olt_rx_power_oid", $row{'PON_TYPE'}) . "." . $big_onu_id;
				// RF Power 
				$rf_input_power_oid = $snmp_obj->get_pon_oid("onu_rf_rx_power_oid", $row{'PON_TYPE'}) . "." . $catv_input_id;
				$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
				$olt_rx_power = $session->get($olt_rx_power_oid);
				$olt_rx_power = round($olt_rx_power/10,4);
				if ($row{'PON_TYPE'} == "GPON") {
					$recv_power = $session->get($recv_power_oid);
					if ($recv_power > 32767)
						$recv_power = $recv_power - 65535 - 1;
					$recv_power = round(($recv_power-15000)/500,2);
					$send_power = $session->get($send_power_oid);
					$send_power = round(($send_power-15000)/500,2);
				}
				if ($row{'PON_TYPE'} == "EPON") {
					$recv_power = $session->get($recv_power_oid);
					$recv_power = round(10*log10($recv_power/10000),2);
					$send_power = $session->get($send_power_oid);
					$send_power = round(10*log10($send_power/10000),2);
				}
  
			
				if ($rf == "Yes") {
					$rf_input_power = $session->get($rf_input_power_oid);
					if ($row{'PON_TYPE'} == "EPON")
						$rf_input_power = round($rf_input_power/10,4);
					$ret = rrd_update($rrd_power, array("N:$recv_power:$send_power:$olt_rx_power:$rf_input_power"));
				} else {
					$ret = rrd_update($rrd_power, array("N:$recv_power:$send_power:$olt_rx_power:0"));
				}
				echo $recv_power . " " . $send_power ." " .$olt_rx_power . "\n" ;
				if( $ret == 0 )
				{
					$err = rrd_error();
					echo "ERROR occurred: $err\n";
				}
				try {
					$result2 = $db->query("SELECT count(*) from ONU_RX_POWER where CUSTOMERS_ID = '$id'");
				} catch (PDOException $e) {
					$error = "Connection Failed:" . $e->getMessage() . "\n";
					return $error;
				}
				$count = $result2->fetchColumn(); 
				if ($count > "0" ) {
					try {
						$result2 = $db->query("UPDATE ONU_RX_POWER SET RX_POWER = '$recv_power' where CUSTOMERS_ID = '$id'");
					} catch (PDOException $e) {
						$error = "Connection Failed:" . $e->getMessage() . "\n";
						return $error;	
					}	 
				} else {
					try {
						$result2 = $db->query("INSERT INTO ONU_RX_POWER (CUSTOMERS_ID, RX_POWER) VALUES ('$id', '$recv_power')");
					} catch (PDOException $e) {
						$error = "Connection Failed:" . $e->getMessage() . "\n";
						return $error;	
					}	 
				}
			} 
		}
	}
}




?>
