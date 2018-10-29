<?php
include ("common.php");
include ("dbconnect.php");
include ("classes/snmp_class.php");
	
$mac_address_table = $customer_id = $type = $rf_status = "";
$snmp_obj = new snmp_oid();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST["customer_id"])) {
                $customer_id = test_input($_POST["customer_id"]);
        }
	if ($_POST["type"]) {
                $type = test_input($_POST["type"]);
        }
	if (isset($_POST["rf_val"])) {
                $rf_val = test_input($_POST["rf_val"]);
        }
	if (isset($_POST["uni_val"])) {
                $uni_val = test_input($_POST["uni_val"]);
        }	
	if (isset($_POST["port_num"])) {
                $port_num = test_input($_POST["port_num"]);
        }	
	if ($_POST["type"] == "Reboot") {
		try {
			$result = $db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME as NAME, SN, PON_ONU_ID, CUSTOMERS.SERVICE, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, OLT.ID, INET_NTOA(OLT.IP_ADDRESS)as IP_ADDRESS, OLT.NAME as OLT_NAME, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where CUSTOMERS.ID = '$customer_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ip_address = $row['IP_ADDRESS'];
			$port_id = $row['PORT_ID'];
			$slot_id = $row['SLOT_ID'];
			$pon_onu_id = $row['PON_ONU_ID'];
			$olt_name = $row['OLT_NAME'];
			$ro = $row['RO'];
			$rw = $row['RW'];
			$olt_type = $row['TYPE'];
			$name = $row['NAME'];
			$pon_type = $row['PON_TYPE'];
		}

		$index = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id;
		$reboot_oid = $snmp_obj->get_pon_oid("onu_reboot_oid", $pon_type) . "." . $index;
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$session = new SNMP(SNMP::VERSION_2C, $ip_address, $rw);
		
		$reboot = $session->set($reboot_oid, 'i', '1');
		if ($session->getError())
				return(var_dump($session->getError()));
		echo "<center><div class=\"bg-success  text-white\">Onu Rebooted Succesfully</center></div>";

	}
		
	if ($_POST["type"] == "SET_RF") {
		try {
			$result = $db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME as NAME, SN, PON_ONU_ID, CUSTOMERS.SERVICE, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, OLT.ID, INET_NTOA(OLT.IP_ADDRESS)as IP_ADDRESS, OLT.NAME as OLT_NAME, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where CUSTOMERS.ID = '$customer_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ip_address = $row['IP_ADDRESS'];
			$port_id = $row['PORT_ID'];
			$slot_id = $row['SLOT_ID'];
			$pon_onu_id = $row['PON_ONU_ID'];
			$olt_name = $row['OLT_NAME'];
			$ro = $row['RO'];
			$rw = $row['RW'];
			$olt_type = $row['TYPE'];
			$name = $row['NAME'];
			$pon_type = $row['PON_TYPE'];
		}

		if ($pon_type == "EPON")
		$index_rf = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + 162;						
		if ($pon_type == "GPON")
		$index_rf = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + 1;		
	
		$onu_rf_status_oid = $snmp_obj->get_pon_oid("onu_rf_status_oid", $pon_type) . "." . $index_rf;
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$session = new SNMP(SNMP::VERSION_2C, $ip_address, $rw);
		$set_rf = $session->set($onu_rf_status_oid, 'i', $rf_val);
		if ($session->getError())
				return(var_dump($session->getError()));

		$type = "ports";
	}
	
	if ($_POST["type"] == "SET_UNI") {
		try {
			$result = $db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME as NAME, SN, PON_ONU_ID, CUSTOMERS.SERVICE, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, OLT.ID, INET_NTOA(OLT.IP_ADDRESS)as IP_ADDRESS, OLT.NAME as OLT_NAME, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where CUSTOMERS.ID = '$customer_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ip_address = $row['IP_ADDRESS'];
			$port_id = $row['PORT_ID'];
			$slot_id = $row['SLOT_ID'];
			$pon_onu_id = $row['PON_ONU_ID'];
			$olt_name = $row['OLT_NAME'];
			$ro = $row['RO'];
			$rw = $row['RW'];
			$olt_type = $row['TYPE'];
			$name = $row['NAME'];
			$pon_type = $row['PON_TYPE'];
		}

		if ($pon_type == "EPON")
		$index_uni = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + $port_num;						
		if ($pon_type == "GPON")
		$index_uni = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + $port_num;		
	
		$uni_port_admin_set = $snmp_obj->get_pon_oid("uni_port_admin_set_oid", $pon_type) . "." . $index_uni;
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$session = new SNMP(SNMP::VERSION_2C, $ip_address, $rw);
		$set_uni = $session->set($uni_port_admin_set, 'i', $uni_val);
		if ($session->getError())
				return(var_dump($session->getError()));
			
		$type = "ports";
	}

	if ($type == "info"){
		try {
			$result = $db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME as NAME, SN, PON_ONU_ID, CUSTOMERS.SERVICE, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, OLT.ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.NAME as OLT_NAME, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE, SERVICES.ID, SERVICES.LINE_PROFILE_ID, SERVICES.SERVICE_PROFILE_ID from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID
where CUSTOMERS.ID = '$customer_id'");
                } catch (PDOException $e) {
                        echo "Connection Failed:" . $e->getMessage() . "\n";
                        exit;
                }
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ip_address = $row['IP_ADDRESS'];
			$port_id = $row['PORT_ID'];
			$slot_id = $row['SLOT_ID'];
			$pon_onu_id = $row['PON_ONU_ID'];
			$olt_name = $row['OLT_NAME'];
			$ro = $row['RO'];
			$rw = $row['RW'];
			$pon_type = $row['PON_TYPE'];
			$name = $row['NAME'];
		}
		$index = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id;
		if ($pon_onu_id < 100) {
			$index2 = 10000000 * $slot_id + 100000 * $port_id + 1000 * $pon_onu_id + 1;
		}else{
			$index2 = (3<<28)+(10000000 * $slot_id + 100000 * $port_id + 1000 * ($pon_onu_id%100) + 1);
		}
		$index_type2id = type2id($slot_id, $port_id, $pon_onu_id);
		if ($pon_type == "EPON")
			$index_rf = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + 162;						
		if ($pon_type == "GPON")
			$index_rf = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + 1;		
		//	$version_oid = $snmp_obj->get_pon_oid("onu_version_oid", $row{'PON_TYPE'}) . "." . $index;
		//	$firmware_oid = "1.3.6.1.4.1.8886.18.2.6.1.1.1.7." . $index;
		$onu_last_online_oid = $snmp_obj->get_pon_oid("onu_last_online_oid", $pon_type) . "." . $index_type2id;
		$device_type_oid = $snmp_obj->get_pon_oid("onu_device_type_oid", $pon_type) . "." . $index;
		$hw_revision_oid = $snmp_obj->get_pon_oid("onu_hw_revision_oid", $pon_type) . "." . $index;
		$match_state_oid = $snmp_obj->get_pon_oid("onu_match_state_oid", $pon_type) . "." . $index;
		$onu_sysuptime_oid = $snmp_obj->get_pon_oid("onu_sysuptime_oid", $pon_type) . "." . $index;
		$onu_register_distance_oid = "1.3.6.1.4.1.8886.18.3.1.3.1.1.16." . $index_type2id;
		$line_profile_id_oid = $snmp_obj->get_pon_oid("line_profile_oid", $pon_type) . "." . $index_type2id;
		$line_profile_name_oid = $snmp_obj->get_pon_oid("line_profile_name_oid", $pon_type) . "." . $index_type2id;
		$service_profile_id_oid = $snmp_obj->get_pon_oid("service_profile_oid", $pon_type) . "." . $index_type2id;
		$service_profile_name_oid = $snmp_obj->get_pon_oid("service_profile_name_oid", $pon_type) . "." . $index_type2id;
		$raisecomSWFileVersion1_oid = "1.3.6.1.4.1.8886.1.26.3.1.1.2." . $index . ".0.1" ;
		$raisecomSWFileVersion2_oid = "1.3.6.1.4.1.8886.1.26.3.1.1.2." . $index . ".0.2" ;
		$raisecomSWFileCommit1_oid = "1.3.6.1.4.1.8886.1.26.3.1.1.3." . $index . ".0.1" ;
		$raisecomSWFileCommit2_oid = "1.3.6.1.4.1.8886.1.26.3.1.1.3." . $index . ".0.2" ;
		$raisecomSWFileActivate1_oid = "1.3.6.1.4.1.8886.1.26.3.1.1.4." . $index . ".0.1" ;
		$raisecomSWFileActivate2_oid = "1.3.6.1.4.1.8886.1.26.3.1.1.4." . $index . ".0.2" ;
		$onu_active_state_oid = $snmp_obj->get_pon_oid("onu_active_state_oid", $pon_type) . "." . $index_type2id;
		$olt_rx_power_oid = $snmp_obj->get_pon_oid("olt_rx_power_oid", $pon_type) . "." . $index_type2id;
		$onu_rf_status_oid = $snmp_obj->get_pon_oid("onu_rf_status_oid", $pon_type) . "." . $index_rf;
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro);
		$device_type = $session->get($device_type_oid);
		$device_type = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $device_type);
        $hw_revision = $session->get($hw_revision_oid);
		$match_state = $session->get($match_state_oid);	
		function calc_last_online($last_online){
			$last_online = str_replace('Hex-STRING: ', '', $last_online);
			$loa = explode(' ', $last_online);
			$year = $loa[0] . $loa[1];
			$year = hexdec($year);
			$month = hexdec($loa[2]);
			$day = hexdec($loa[3]);
			$hour = hexdec($loa[4]);
			$hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
			$minute = hexdec($loa[5]);
			$minute = str_pad($minute, 2, '0', STR_PAD_LEFT);	
			$last_online = $year . "-". $month . "-". $day . "  " . $hour . ":" . $minute ;
			return $last_online;
			}
		$olt_rx_power = $session->get($olt_rx_power_oid);
		$olt_rx_power = round($olt_rx_power/10,4) . " dBm";
		/*
		$onu_rf_status = $session->get($onu_rf_status_oid);
		if ($onu_rf_status == "1") {
			$onu_rf_status = "enabled" ;
		}
		if ($onu_rf_status == "2") {
			$onu_rf_status = "disabled" ;
		}
		*/
		if ($pon_type == "GPON") {	
			if ($match_state == "1") {
				$match_state = " match(1) ";
			}elseif ($match_state == "2"){
				$match_state = "mismatch(2)";
			}
			
			$onu_rx_power_oid = $snmp_obj->get_pon_oid("onu_rx_power_oid", $pon_type) . "." . $index2;
			$onu_rx_power = $session->get($onu_rx_power_oid);
			if ($onu_rx_power > 32767)
				$onu_rx_power = $onu_rx_power - 65535 - 1;
			$onu_rx_power = round(($onu_rx_power-15000)/500,2) . " dBm";
			$onu_tx_power_oid = $snmp_obj->get_pon_oid("onu_tx_power_oid", $pon_type) . "." . $index2;
			$onu_tx_power = $session->get($onu_tx_power_oid);
			$onu_tx_power = round(($onu_tx_power-15000)/500,2) . " dBm";
			$onu_pon_temp_value = $snmp_obj->get_pon_oid("onu_pon_temp_oid", $pon_type) . "." . $index2;
			$line_profile_name = $session->get($line_profile_name_oid);
			$line_profile_name = str_replace('Hex-STRING: ', '', $line_profile_name);
			$line_profile_name = str_replace(' ', '', $line_profile_name);
			$service_profile_name = $session->get($service_profile_name_oid);
			$service_profile_name = str_replace('Hex-STRING: ', '', $service_profile_name);
			$service_profile_name = str_replace(' ', '', $service_profile_name);
			$onu_register_distance = $session->get($onu_register_distance_oid);
			$onu_sysuptime = $session->get($onu_sysuptime_oid);
			$onu_sysuptime_days = round($onu_sysuptime/(100*3600*24),0);
			$onu_sysuptime_hours = $onu_sysuptime/(100*3600)%24;
			$onu_sysuptime_minutes = $onu_sysuptime/(100*60)%60;
			$onu_sysuptime = $onu_sysuptime_days . " day(s) " . $onu_sysuptime_hours . " hour(s) " . $onu_sysuptime_minutes . " minutes";
		}
		if ($pon_type == "EPON") {	
			if ($match_state == "1") {
				$match_state = "unknown(1)";
			}elseif ($match_state == "2"){
				$match_state = "match(2)";
			}elseif ($match_state == "3"){
				$match_state = "mismatch(3)";
			}
			$onu_rx_power_oid = $snmp_obj->get_pon_oid("onu_rx_power_oid", $pon_type) . "." . $index;
			$onu_rx_power = $session->get($onu_rx_power_oid);
			$onu_rx_power = round(10*log10($onu_rx_power/10000),2) . " dBm";
			$onu_tx_power_oid = $snmp_obj->get_pon_oid("onu_tx_power_oid", $pon_type) . "." . $index;
			$onu_tx_power = $session->get($onu_tx_power_oid);
			$onu_tx_power = round(10*log10($onu_tx_power/10000),2) . " dBm";			
			$onu_pon_temp_value = $snmp_obj->get_pon_oid("onu_pon_temp_oid", $pon_type) . "." . $index;
			$line_profile_name = $session->get($line_profile_name_oid);
			$line_profile_name = str_replace('STRING: ', '', $line_profile_name);
			$service_profile_name = $session->get($service_profile_name_oid);
			$service_profile_name = str_replace('STRING: ', '', $service_profile_name);
			
		}
  		$onu_active_state = $session->get($onu_active_state_oid);
		if ($onu_active_state == "1") {
			$onu_active_state = "<font color=green>active</font>";
		}elseif ($onu_active_state == "2"){
			$onu_active_state = "<font color=red>suspended</font>";
		}
        $onu_pon_temp_value = $session->get($onu_pon_temp_value);
		$onu_pon_temp_value = round($onu_pon_temp_value/256,1) . "°C";
		$line_profile_id = $session->get($line_profile_id_oid);
		$service_profile_id = $session->get($service_profile_id_oid);
		$raisecomSWFileVersion1 = $session->get($raisecomSWFileVersion1_oid);
		$raisecomSWFileVersion2 = $session->get($raisecomSWFileVersion2_oid);
		$raisecomSWFileCommit1 = $session->get($raisecomSWFileCommit1_oid);
		if ($raisecomSWFileCommit1 == "1") {
			$raisecomSWFileCommit1 = "(committed)";
		}else{
			$raisecomSWFileCommit1 = "";
		}
		$raisecomSWFileCommit2 = $session->get($raisecomSWFileCommit2_oid);
		
  		if ($raisecomSWFileCommit2 == "1") {
			$raisecomSWFileCommit2 = "(committed)";
		}else{
			$raisecomSWFileCommit2 = "";
		}
		$raisecomSWFileActivate1 = $session->get($raisecomSWFileActivate1_oid);
		if ($raisecomSWFileActivate1 == "1") {
				$raisecomSWFileActivate1 = "(active)";
		}else{
				$raisecomSWFileActivate1 = "";
		}
		$raisecomSWFileActivate2 = $session->get($raisecomSWFileActivate2_oid);

		if ($raisecomSWFileActivate2 == "1") {
				$raisecomSWFileActivate2 = "(active)";
		}else{
				$raisecomSWFileActivate2 = "";
		}

		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		$session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro);
		$last_online = calc_last_online($session->get($onu_last_online_oid));
/*
		$serial_number = $session->get($serial_number_oid);
		$serial_number = str_replace('STRING: ', '', $serial_number);
	    $serial_number = str_replace('"', '', $serial_number);
		$version = $session->get($version_oid);
		$firmware = $session->get($firmware_oid);
*/	

		print "<div class=\"table-responsive\"><table class=\"table table-bordered table-condensed table-hover\">";
		print "<tr><th>Device Type:</th><td>" . $device_type . "</td></tr>";
        print "<tr><th>HW Revision:</th><td>" . $hw_revision . "</td></tr>";
		print "<tr><th>SW Version 1:</th><td>" . $raisecomSWFileVersion1 . " " . $raisecomSWFileCommit1 . " " . $raisecomSWFileActivate1 . "</th></tr>";
        print "<tr><th>SW Version 2:</th><td>" . $raisecomSWFileVersion2 . " " . $raisecomSWFileCommit2 . " " . $raisecomSWFileActivate2 . "</th></tr>";
		print "<tr><th>Match State:</th><td>" . $match_state . "</td></tr>";
		print "<tr><th>Line Profile ID:</th><td>" . $line_profile_id . "</td></tr>";
		print "<tr><th>Line Profile Name:</th><td>" . $line_profile_name . "</td></tr>";
		print "<tr><th>Service Profile ID:</th><td>" . $service_profile_id . "</td></tr>";
		print "<tr><th>Service Profile Name:</th><td>" . $service_profile_name . "</td></tr>";
		print "<tr><th>Onu Rx Power:</th><td>" . $onu_rx_power . "</td></tr>";
		print "<tr><th>Onu Tx Power:</th><td>" . $onu_tx_power . "</td></tr>";
		print "<tr><th>OLT Rx Power:</th><td>" . $olt_rx_power . "</td></tr>";
		print "<tr><th>Onu Pon Temperature:</th><td>" . $onu_pon_temp_value . "</td></tr>";
		print "<tr><th>Last Online:</th><td>" . $last_online . "</td></tr>";
		
		// print "<tr><th>RF State:</th><td>" . $onu_rf_status . "</td></tr>";
		if ($pon_type == "GPON") {	
			print "<tr><th>Onu Distance:</th><td>" . $onu_register_distance . " m.</td></tr>";
			print "<tr><th>Onu SysUptime:</th><td>" . $onu_sysuptime . "</td></tr>";
		}
		print "<tr><th>Onu State:</th><td>" . $onu_active_state . "</td></tr>";

//		print "<tr><td>Software version:</td><td>" . $version . "</td></tr>";
//		print "<tr><td>Firmware version:</td><td>" . $firmware . "</td></tr>";
		print "</table></div>";
		print "<div class=\"form-group\"><form class=\"form-horizontal\" action=\"onu_info.php\" method=\"post\">";
		print "<input type=\"hidden\" name=\"customer_id\" value=\"". $customer_id ."\">";
		print "<div class=\"row justify-content-md-center\"><div class=\"col-md-4\">";
		//print "<button class=\"btn btn-info\" type=\"submit\" name='type' value='Reboot'>Reboot</button>";
		print "<button class=\"btn btn-info\" type=\"button\" onClick=\"getPage('" . $customer_id . "', 'Reboot');\">Reboot</button>";
        print "</div></div>";
		print "</form></div>";
	}


        if ($type == "ports"){
		 try {
                        $result = $db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME as NAME, SN, PON_ONU_ID, CUSTOMERS.SERVICE, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, OLT.ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.NAME as OLT_NAME, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE, SERVICES.ID, SERVICES.LINE_PROFILE_ID, SERVICES.SERVICE_PROFILE_ID, SERVICE_PROFILE.PORTS, SERVICE_PROFILE.HGU, SERVICE_PROFILE.RF from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN SERVICE_PROFILE on SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where CUSTOMERS.ID = '$customer_id'");
                } catch (PDOException $e) {
                        echo "Connection Failed:" . $e->getMessage() . "\n";
                        exit;
                }
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ip_address = $row['IP_ADDRESS'];
			$port_id = $row['PORT_ID'];
			$slot_id = $row['SLOT_ID'];
			$pon_onu_id = $row['PON_ONU_ID'];
			$olt_name = $row['OLT_NAME'];
			$ports = $row['PORTS'];
			$ro = $row['RO'];
			$rw = $row['RW'];
			$olt_type = $row['TYPE'];
			$name = $row['NAME'];
			$pon_type = $row['PON_TYPE'];
			$hgu = $row['HGU'];
			$rf = $row['RF'];
		}
		if ($pon_onu_id < 100) {
			$index = 10000000 * $slot_id + 100000 * $port_id + 1000 * $pon_onu_id;
		}else{
			$index = (3<<28)+(10000000 * $slot_id + 100000 * $port_id + 1000 * ($pon_onu_id%100));
		}
		if ($pon_type !== "GPON" || $hgu !== "Yes") {
			?>
			<div class="table-responsive">
				<table class="table table-bordered table-condensed table-hover">
					<thead>
						<tr>
							<th>UNI</th>
							<th>Admin</th>
							<th>Link</th>
							<th>Flow Control</th>
							<th>Speed/Duplex</th>
						<?php if ($pon_type == "GPON") { ?>
							<th>Native Vlan</th>
							<th>DS_Policy_ID</th>
							<th>US_Policy_ID</th>
							<?php } ?>
						<?php if ($pon_type == "EPON") { ?>
							<th>Isolation</th>	
						<?php } ?>
							<th>Enable/Disable</th>
							<th>Do it!</th>
						</tr>  
					</thead>
			<?php
			for ($i = 1; $i <= $ports ; $i++) {
				$gindex = $index + $i;
				$port_link_oid = $snmp_obj->get_pon_oid("uni_port_link_oid", $pon_type) . "." . $gindex;
				$port_admin_oid = $snmp_obj->get_pon_oid("uni_port_admin_oid", $pon_type) . "." . $gindex;
				$port_autong_oid = $snmp_obj->get_pon_oid("uni_port_autong_oid", $pon_type) . "." . $gindex;
				$port_flowctrl_oid = $snmp_obj->get_pon_oid("uni_port_flowctrl_oid", $pon_type) . "." . $gindex;
				$port_speed_duplex_oid = $snmp_obj->get_pon_oid("uni_port_speed_duplex_oid", $pon_type) . "." . $gindex;
			
				if ($pon_type == "GPON") {
					snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
					$session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro);
					$EthPortNativeVlan_oid = $snmp_obj->get_pon_oid("uni_port_nativevlan_oid", $pon_type) . "." . $gindex;
					$rcGponOnuEthPortDSPolicingProfileId_oid = "1.3.6.1.4.1.8886.18.3.6.5.1.1.19." . $gindex;
					$rcGponOnuEthPortUSPolicingProfileId_oid = "1.3.6.1.4.1.8886.18.3.6.5.1.1.18." . $gindex;
					$EthPortNativeVlan = $session->get($EthPortNativeVlan_oid);
					$rcGponOnuEthPortUSPolicingProfileId = $session->get($rcGponOnuEthPortUSPolicingProfileId_oid);
					$rcGponOnuEthPortDSPolicingProfileId = $session->get($rcGponOnuEthPortDSPolicingProfileId_oid);
					$mac_address_perport_oid = "1.3.6.1.4.1.8886.18.3.6.12.1.1.1." . $gindex;
					$mac_address_perport_number_oid = "1.3.6.1.4.1.8886.18.3.6.12.1.1.2." . $gindex;
					$mac_address_perport_number = $session->get($mac_address_perport_number_oid);
					snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
					$session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro);
					$mac_address_perport = $session->get($mac_address_perport_oid);
					$mac_address_perport = str_replace('Hex-STRING: ', '', $mac_address_perport);
					$mac_address_perport = str_replace(' ', '', $mac_address_perport);
					$mac_address_perport = str_replace('"', '', $mac_address_perport);
					$mac_address_perport = str_replace(array("\n\r", "\n", "\r"), '', $mac_address_perport);
					$mac_address_perport = str_split($mac_address_perport, 12);
					$mac_address_boza = "";
					foreach ($mac_address_perport as $mac_address) {
						$mac_address_boza = $mac_address_boza . implode('.', str_split($mac_address,4)) . "<br>";
					}
					$mac_address_table =  $mac_address_table . "<tr  align=center><td>" . $i . "</td><td>" . $mac_address_boza . "</td><td>" . $mac_address_perport_number . "</td></tr>";
				}	
				if ($pon_type == "EPON") {
					snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
					$session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro);
					$port_isolation_oid = "1.3.6.1.4.1.8886.18.2.6.3.2.1.4." . $gindex;
					$port_isolation = $session->get($port_isolation_oid);
				}
				snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
				$session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro);
				$port_admin = $session->get($port_admin_oid);
				$port_link = $session->get($port_link_oid);
				$port_flowctrl = $session->get($port_flowctrl_oid);
				$port_speed_duplex = $session->get($port_speed_duplex_oid);
				

				
				if ($pon_type == "GPON") {
					
					
				
					if ($port_admin == '2') {
						$port_admin = "<font color=red>Disabled</font>";
					} else if ($port_admin == '1') {
						$port_admin = "<font color=green>Enabled</font>";
					} else if ($port_admin == '0') {
						$port_admin = "Unknown";
					}
					
					if ($port_speed_duplex == '0') {
						$port_speed_duplex = "auto";
					} else if ($port_speed_duplex == '2') {
						$port_speed_duplex = "full_100";
					} else if ($port_speed_duplex == '3') {
						$port_speed_duplex = "full_1000";
					} else if ($port_speed_duplex == '4') {
						$port_speed_duplex = "full_auto";
					} else if ($port_speed_duplex == '17') {
						$port_speed_duplex = "half_10";
					} else if ($port_speed_duplex == '18') {
						$port_speed_duplex = "half_100";
					} else if ($port_speed_duplex == '19') {
						$port_speed_duplex = "half_1000";
					} else if ($port_speed_duplex == '32') {
						$port_speed_duplex = "auto_1000";
					}  else if ($port_speed_duplex == '48') {
						$port_speed_duplex = "auto_100";
					}
					
					if ($port_link == '2') {
						$port_link = "<font color=red>Down</font>";
					} else if ($port_link == '1') {
						$port_link = "<font color=green>Up</font>";
					} else if ($port_link == '0') {
						$port_link = "Unknown";
					}
				}
				if ($pon_type == "EPON") {
					if ($port_admin == '1') {
						$port_admin = "<font color=red>Disabled</font>";
					} else if ($port_admin == '2') {
						$port_admin = "<font color=green>Enabled</font>";
					} else if ($port_admin == '0') {
						$port_admin = "Unknown";
					}
					
					if ($port_speed_duplex == '1') {
					$port_speed_duplex = "Unknown";
					} else if ($port_speed_duplex == '2') {
						$port_speed_duplex = "half_10";
					} else if ($port_speed_duplex == '3') {
						$port_speed_duplex = "full_10";
					} else if ($port_speed_duplex == '4') {
					$port_speed_duplex = "half_100";
					} else if ($port_speed_duplex == '5') {
						$port_speed_duplex = "full_100";
					} else if ($port_speed_duplex == '6') {
						$port_speed_duplex = "half_1000";
					} else if ($port_speed_duplex == '7') {
						$port_speed_duplex = "full_1000";
					} else if ($port_speed_duplex == '99') {
						$port_speed_duplex = "illegal";
					}
					
					if ($port_link == '1') {
						$port_link = "<font color=red>Down</font>";
					} else if ($port_link == '2') {
						$port_link = "<font color=green>Up</font>";
					} else if ($port_link == '0') {
						$port_link = "Unknown";
					}
					if ($port_isolation == '1') {
						$port_isolation = "<font color=green>enabled</font>";
					} else if ($port_isolation = '2') {
						$port_isolation = "<font color=red>disabled</font>";
					}	
				}
				
				if ($port_flowctrl == '1') {
					$port_flowctrl = "<font color=red>Disabled</font>";
				} else if ($port_flowctrl == '2') {
					$port_flowctrl = "<font color=green>Enabled</font>";
				} else if ($port_flowctrl == '0') {
					$port_flowctrl = "Unknown";
				}
				
				print "<tr  align=center><td>" . $i . "</td><td>" . $port_admin . "</td><td>" . $port_link .  "</td><td>" . $port_flowctrl . "</td><td>" . $port_speed_duplex . "</td>";
				if ($pon_type == "GPON") 
					print "<td>" . $EthPortNativeVlan . "</td><td>" . $rcGponOnuEthPortDSPolicingProfileId . "</td><td>" . $rcGponOnuEthPortUSPolicingProfileId . "</td>"	;
				if ($pon_type == "EPON")			
					print "<td>" . $port_isolation . "</td>";
			
					?>
					
						<td>
								
									<select class="form-control" id="uni_num_<?php echo $i; ?>" name="uni_num_<?php echo $i; ?>" >								
										<option value="1" >Enable</option>		
										<option value="2" >Disable</option>											
									</select>
								</td>
								<td>
						<?php print	"<button class=\"btn btn-info\" type=\"button\" onClick=\"setUniPortStatus('" . $customer_id . "', '" . $i . "','SET_UNI');\">SET</button>"; ?>
								</td>
			<?php
				print "</tr>";
			}
			
			print "</table></div>";
		}
		if ($rf == "Yes") {
			?>
			<div class="row">
			<div class="form-group">
				<form class="form-horizontal" action="onu_details.php" method="post">
				<?php 		print "<input type=\"hidden\" name=\"customer_id\" value=\"". $customer_id ."\">";
				?>
				<div class="col-md-5">
					<div class="table-responsive">
						<table class="table table-bordered table-condensed table-hover">
							<thead>
								<tr>
									<th>Port</th>
									<th>Admin</th>
									<th>Enable/Disable</th>
									<th>Do it!</th>
								</tr>
							</thead>
			<?php
			snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
			$session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro);
			if ($pon_type == "EPON")
			$index_rf = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + 162;						
			if ($pon_type == "GPON")
			$index_rf = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + 1;						
			$onu_rf_status_oid = $snmp_obj->get_pon_oid("onu_rf_status_oid", $pon_type) . "." . $index_rf;
			$onu_rf_status = $session->get($onu_rf_status_oid);
			if ($onu_rf_status == "1") {
				$onu_rf_status = "<font color=green>Enabled</font>" ;
			}
			if ($onu_rf_status == "2") {
				$onu_rf_status = "<font color=red>Disabled</font>" ;
			}
			print "<tr  align=center><td>RF</td><td>" . $onu_rf_status . "</td>" ; 
			
			?>
			
						
								<td>
								
									<select class="form-control" id="rf_menu" name="rf_menu" >								
										<option value="1" >Enable</option>		
										<option value="2" >Disable</option>											
									</select>
								</td>
								<td>
						<?php print	"<button class=\"btn btn-info\" type=\"button\" onClick=\"getPageRF('" . $customer_id . "', 'SET_RF');\">SET</button>"; ?>
								</td>
							
					</tr>
			<?php
			
			print "</table></form></div></div></div></div>";
		}
		if ($pon_type == "GPON" && $hgu !="Yes") {
			print "<div class=\"row\"><div class=\"col-md-6\"><p>MAC ADDRESS TABLE</p>";
			print "<div class=\"table-responsive\"><table class=\"table table-bordered table-condensed table-hover\"><thead><tr><th>UNI</th><th>MAC_ADDRESS</th><th>Count</th></tr></thead>";
			print $mac_address_table;
			print "</table></div></div>"; 
		}
		
		print "<BR><BR><form action=\"onu_details.php\" method=\"post\">";
		print "<input type=\"hidden\" name=\"customer_id\" value=\"". $customer_id ."\">";
		print "<center></center>";

	}

	if ($type == "graphs"){
		try {
				$result = $db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME as NAME, SN, PON_ONU_ID, CUSTOMERS.SERVICE, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, OLT.ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.NAME as OLT_NAME, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, SERVICES.ID, SERVICES.LINE_PROFILE_ID, SERVICES.SERVICE_PROFILE_ID, SERVICE_PROFILE.PORTS, SERVICE_PROFILE.HGU, SERVICE_PROFILE.RF from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN SERVICE_PROFILE on SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID where CUSTOMERS.ID = '$customer_id'");
			} catch (PDOException $e) {
				echo "Connection Failed:" . $e->getMessage() . "\n";
				exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ports = $row{'PORTS'};
			$customer_name = $row{'NAME'};
			$olt_name = $row{'OLT_NAME'};
			$sn = $row["SN"];
			$big_onu_id = type2id($row{'SLOT_ID'}, $row{'PORT_ID'}, $row{'PON_ONU_ID'});
			$olt_ip_address = $row["IP_ADDRESS"];
			$rf = $row["RF"];
			$hgu = $row["HGU"];
			
			$rrd_name = dirname(__FILE__) . "/rrd/" . $sn . "_traffic.rrd";
			$rrd_power = dirname(__FILE__) . "/rrd/" . $sn . "_power.rrd";

			$opts = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=b/s", "--title=Daily Traffic",
			"DEF:inoctets=$rrd_name:input:AVERAGE",
			"DEF:outoctets=$rrd_name:output:AVERAGE",		
			"CDEF:inbits=inoctets,8,*",
			"CDEF:outbits=outoctets,8,*",
			"AREA:inbits#00FF00:In traffic",
			"LINE1:outbits#0000FF:Out traffic\\r",
			"GPRINT:inbits:MAX:IN Max\: %6.2lf%Sbps",
			"COMMENT:  ",
			"GPRINT:inbits:AVERAGE:Avg\: %6.2lf%Sbps",
			"COMMENT:  ",
			"GPRINT:inbits:LAST:Last\: %6.2lf%Sbps\\r",
			"COMMENT:\\n",
			"GPRINT:outbits:MAX:OUT Max\: %6.2lf%Sbps",
			"COMMENT:  ",
			"GPRINT:outbits:AVERAGE:Avg\: %6.2lf%Sbps",
			"COMMENT:  ",
			"GPRINT:outbits:LAST:Last\: %6.2lf%Sbps\\r"
			);
			$pkts = array("unicast", "broadcast", "multicast");
			foreach ($pkts as $tr) {
				$$tr = dirname(__FILE__) . "/rrd/" . $sn . "_" . $tr . ".rrd";
				${$tr."_opts"} = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=pkts/s", "--title=Daily $tr",
				"DEF:inoctets=${$tr}:input:AVERAGE",
				"DEF:outoctets=${$tr}:output:AVERAGE",
				"AREA:inoctets#00FF00:In",
				"LINE1:outoctets#0000FF:Out\\r",
				"GPRINT:inoctets:MAX:IN Max\: %6.0lf pkts/s",
				"GPRINT:inoctets:AVERAGE:Avg\: %6.0lf pkts/s",
				"GPRINT:inoctets:LAST:Last\: %6.0lf pkts/s\\r",
				"GPRINT:outoctets:MAX:OUT Max\: %6.0lf pkts/s",
				"GPRINT:outoctets:AVERAGE:Avg\: %6.0lf pkts/s",
				"GPRINT:outoctets:LAST:Last\: %6.0lf pkts/s\\r"
				);
			}
			if ($hgu !== "Yes") {
				for ($i=1; $i <= $row{'PORTS'}; $i++) {
					$octets_ethernet = dirname(__FILE__) . "/rrd/" . $sn . "_" . $i . ".rrd";
					${$i."_opts"} = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=b/s", "--title=Daily Traffic UNI $i",
					"DEF:inoctets=$octets_ethernet:input:AVERAGE",
					"DEF:outoctets=$octets_ethernet:output:AVERAGE",
					"CDEF:inbits=inoctets,8,*",
					"CDEF:outbits=outoctets,8,*",
					"AREA:inbits#00FF00:In traffic",
					"LINE1:outbits#0000FF:Out traffic\\r",
					"GPRINT:inbits:MAX:IN Max\: %6.2lf%Sbps",
					"COMMENT:  ",
					"GPRINT:inbits:AVERAGE:Avg\: %6.2lf%Sbps",
					"COMMENT:  ",
					"GPRINT:inbits:LAST:Last\: %6.2lf%Sbps\\r",
					"COMMENT:\\n",
					"GPRINT:outbits:MAX:OUT Max\: %6.2lf%Sbps",
					"COMMENT:  ",
					"GPRINT:outbits:AVERAGE:Avg\: %6.2lf%Sbps",
					"COMMENT:  ",
					"GPRINT:outbits:LAST:Last\: %6.2lf%Sbps\\r"
					);
					${$i."_url"} = $sn . "_" . $i . ".gif";
					${$i."_gif"} = dirname(__FILE__) . "/rrd/" . $sn . "_" . $i . ".gif";
					$ret = rrd_graph(${$i."_gif"}, ${$i."_opts"});
				}
			}
			if ($rf == "Yes") {
				$opts4 = array( "--start", "-1d", "--vertical-label=dBm", "--title=Daily Power",
				"DEF:inoctets=$rrd_power:input:AVERAGE",
				"DEF:outoctets=$rrd_power:output:AVERAGE",
				"DEF:rx_olt=$rrd_power:rxolt:AVERAGE",
				"DEF:rf_in=$rrd_power:rfin:AVERAGE",
				"LINE2:rx_olt#D6213B:RX@OLT",
				"GPRINT:rx_olt:LAST:Last\: %6.2lf dBm\\r",
				"LINE2:outoctets#C6913B:TX@ONU",
				"GPRINT:outoctets:LAST:Last\: %6.2lf dBm\\r",
				"LINE2:inoctets#7FB37C:RX@ONU",
				"GPRINT:inoctets:LAST:Last\: %6.2lf dBm\\r",
				"LINE2:rf_in#FFD87C:RF@ONU",
				"GPRINT:rf_in:LAST:Last\: %6.2lf dBm\\r",
					);
			} else {
				$opts4 = array( "--start", "-1d", "--vertical-label=dBm", "--title=Daily Power",
				"DEF:inoctets=$rrd_power:input:AVERAGE",
				"DEF:outoctets=$rrd_power:output:AVERAGE",
				"DEF:rx_olt=$rrd_power:rxolt:AVERAGE",
				"LINE2:rx_olt#D6213B:RX@OLT",
				"GPRINT:rx_olt:LAST:Last\: %6.2lf dBm\\r",
				"LINE2:outoctets#C6913B:TX@ONU",
				"GPRINT:outoctets:LAST:Last\: %6.2lf dBm\\r",
				"LINE2:inoctets#7FB37C:RX@ONU",
				"GPRINT:inoctets:LAST:Last\: %6.2lf dBm\\r",
				);
			}

			$rrd_traffic_url = $sn . "_traffic.gif";
			$unicast_url =  $sn . "_unicast.gif";
			$broadcast_url =  $sn . "_broadcast.gif";
			$multicast_url =  $sn . "_multicast.gif";
			$rrd_power_url = $sn . "_power.gif";
			$rrd_traffic = dirname(__FILE__) . "/rrd/" . $sn . "_traffic.gif";
			$rrd_power = dirname(__FILE__) . "/rrd/" . $sn . "_power.gif";
			$unicast = dirname(__FILE__) . "/rrd/" . $sn . "_unicast.gif";
			$broadcast = dirname(__FILE__) . "/rrd/" . $sn . "_broadcast.gif";
			$multicast = dirname(__FILE__) . "/rrd/" . $sn . "_multicast.gif";
			$ret = rrd_graph($rrd_traffic, $opts);
			$ret = rrd_graph($rrd_power, $opts4);
			$ret = rrd_graph($unicast, $unicast_opts);
			$ret = rrd_graph($broadcast, $broadcast_opts);
			$ret = rrd_graph($multicast, $multicast_opts);


			if( !is_array($ret) )
			{
				$err = rrd_error();
				echo "rrd_graph() ERROR: $err\n";
			}

		}
		print "<div class=\"text-center\"><div class=\"table-responsive col-lg-11\"><table class=\"table text-center \"><tr>";
		print "<td><p onClick=\"graph('". $customer_id . "', 'traffic');\"><img src=\"rrd/" . $rrd_traffic_url . "\"></img></p></td>";
		$end = "1";
		if ($hgu !== "Yes") {
			for ($i=1; $i <= $ports; $i++) {
				$name = ${$i."_url"};
				print "<td><p onClick=\"graph('". $customer_id . "', '" . $i . "');\"><img src=\"rrd/" . $name . "\"></img></p></td>";
				$end++;
				if ($end == "2") {
					$end = "0";
					print "</tr><tr>";
				}
			}
		}
		print "</tr>";
		print "<tr><td><p onClick=\"graph('". $customer_id . "', 'unicast');\"><img src=\"rrd/" . $unicast_url . "\"></img></p></td>";
		print "<td><p onClick=\"graph('". $customer_id . "', 'broadcast');\"><img src=\"rrd/" . $broadcast_url . "\"></img></p></td></tr>";
		print "<tr><td><p onClick=\"graph('". $customer_id . "', 'multicast');\"><img src=\"rrd/" . $multicast_url . "\"></img></p></td>";
		print "<td><p onClick=\"graph('". $customer_id . "', 'power');\"><img src=\"rrd/" . $rrd_power_url . "\"></img></p></td></tr>";
		print "<table></div></div>";
        }

}
?>

