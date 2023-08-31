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
	$session = new SNMP(SNMP::VERSION_2C, $row['IP_ADDRESS'], $row['RO']);
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
	$pon_type = $row['PON_TYPE'];
	$hgu = $row['HGU'];
	
 	$catv_input_id = $row['SLOT_ID'] * 10000000 + $row['PORT_ID'] * 100000 + $row['PON_ONU_ID'] * 1000 + 160;	
	$rf = $row['RF'];
	$sn = $row["SN"];
	$ip_address = $row["IP_ADDRESS"];
	$customers_obj = new customers();
	$big_onu_id = $customers_obj->type2id($row['SLOT_ID'], $row['PORT_ID'], $row['PON_ONU_ID']);
		
	
	if ($row['PON_TYPE'] == "GPON") {
		
		if ($row['PON_ONU_ID'] < 100) {
			$index_2 = 10000000 * $row['SLOT_ID'] + 100000 * $row['PORT_ID'] + 1000 * $row['PON_ONU_ID'] + 1;
		}else{
			$index_2 = (3<<28)+(10000000 * $row['SLOT_ID'] + 100000 * $row['PORT_ID'] + 1000 * ($row['PON_ONU_ID']%100) + 1);
		}
		$catv_input_id = $index_2;
	}
	if ($row['PON_TYPE'] == "EPON") {
		$index_2 = $row['SLOT_ID'] * 10000000 + $row['PORT_ID'] * 100000 + $row['PON_ONU_ID'];
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
	
	
	if(isset($row['IP_ADDRESS'])) { 
		if ($ip_address_state[$ip_address] == "up") {
			$status = "0";
			//GET STATUS via SNMP
			snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
			$status_oid = $snmp_obj->get_pon_oid("onu_status_oid", $row['PON_TYPE']) . "." . $big_onu_id;
			$session = new SNMP(SNMP::VERSION_2C, $row['IP_ADDRESS'], $row['RO']);
			$status = $session->get($status_oid);				
			if ($status == "1") {		
				echo $sn . "\n";
				$octets_in_ethernet = $snmp_obj->get_pon_oid("uni_octets_in_ethernet_oid", $row['PON_TYPE']) . ".";
				$octets_out_ethernet = $snmp_obj->get_pon_oid("uni_octets_out_ethernet_oid", $row['PON_TYPE']) . ".";
				//Onu Power
				$recv_power_oid = $snmp_obj->get_pon_oid("onu_recv_power_oid", $row['PON_TYPE']) . "." . $index_2;
				$send_power_oid = $snmp_obj->get_pon_oid("onu_send_power_oid", $row['PON_TYPE']) . "." . $index_2;
				//OLT RX Power
				$olt_rx_power_oid = $snmp_obj->get_pon_oid("olt_rx_power_oid", $row['PON_TYPE']) . "." . $big_onu_id;
				// RF Power 
				$rf_input_power_oid = $snmp_obj->get_pon_oid("onu_rf_rx_power_oid", $row['PON_TYPE']) . "." . $catv_input_id;
				$session = new SNMP(SNMP::VERSION_2C, $row['IP_ADDRESS'], $row['RO']);
				$olt_rx_power = $session->get($olt_rx_power_oid);
				$olt_rx_power = round($olt_rx_power/10,4);

				if ($row['PON_TYPE'] == "GPON") {
					$recv_power = $session->get($recv_power_oid);
					if ($recv_power > 32767)
						$recv_power = $recv_power - 65535 - 1;
					$recv_power = round(($recv_power-15000)/500,2);
				//	$send_power = $session->get($send_power_oid);
				//	$send_power = round(($send_power-15000)/500,2);
				}
				if ($row['PON_TYPE'] == "EPON") {
					$recv_power = $session->get($recv_power_oid);
					$recv_power = round(10*log10($recv_power/10000),2);
				//	$send_power = $session->get($send_power_oid);
				//	$send_power = round(10*log10($send_power/10000),2);
				}
  
			
				if ($rf == "Yes") {
					$rf_input_power = $session->get($rf_input_power_oid);
					if ($row['PON_TYPE'] == "EPON")
						$rf_input_power = round($rf_input_power/10,4);
				} else {
					$rf_input_power = "0";
				}
				$ret = rrd_update($rrd_power, array("N:$recv_power:$olt_rx_power:$rf_input_power"));
				echo $recv_power . " " .$olt_rx_power . " " . $rf_input_power . "\n" ;
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
				if ($hgu !== "Yes") {
					for ($i=1; $i <= $row['PORTS']; $i++) {
						$ethernet_id = $row['SLOT_ID'] * 10000000 + $row['PORT_ID'] * 100000 + $row['PON_ONU_ID'] * 1000 + $i;
						$octets_ethernet = dirname(__FILE__) . "/rrd/" . $sn . "_" . $i . ".rrd";
						if(!is_file($octets_ethernet)) {
							$opts = array( "--step", "300", "--start", "0",
							   "DS:input:DERIVE:1800:0:U",
							   "DS:output:DERIVE:1800:0:U",
							   "RRA:AVERAGE:0.5:1:600",
							   "RRA:AVERAGE:0.5:6:700",
							   "RRA:AVERAGE:0.5:24:775",
							   "RRA:AVERAGE:0.5:288:797",
							   "RRA:MAX:0.5:1:600",
							   "RRA:MAX:0.5:6:700",
							   "RRA:MAX:0.5:24:775",
							   "RRA:MAX:0.5:288:797"
							);
							$ret = rrd_create($octets_ethernet, $opts);

							if( $ret == 0 )
							{
								$err = rrd_error();
								return $err;
							}	
						}
						
						
						$octets_in_ethernet_id = $octets_in_ethernet . $ethernet_id;
						$octets_out_ethernet_id = $octets_out_ethernet . $ethernet_id;
						$octets_in_ethernet_val = $session->get($octets_in_ethernet_id);
						$octets_out_ethernet_val = $session->get($octets_out_ethernet_id);
						$ret = rrd_update($octets_ethernet, array("N:$octets_in_ethernet_val:$octets_out_ethernet_val"));
						if( $ret == 0 )
						{
							$err = rrd_error();
							echo "ERROR occurred: $err\n";
						}
					}
				}
			} 
		}
	}
}

// UPDATE OLT GRAPHS

try {
	$result = $db->query("SELECT OLT.ID, OLT.NAME as OLT_NAME, MODEL, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE from OLT LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID");
} catch (PDOException $e) {
	echo "Connection Failed:" . $e->getMessage() . "\n";
	exit;
}

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$ip_address = $row['IP_ADDRESS'];
	$olt_name = $row['OLT_NAME'];
	$ro = $row['RO'];
	$rw = $row['RW'];
	$olt_type = $row['TYPE'];

	
	if ($ip_address_state[$ip_address] == "up") {
		$ethernet_port_info = array(); 
		$dot3StatsIndex = $snmp_obj->get_pon_oid("dot3StatsIndex", "OLT");
		$session = new SNMP(SNMP::VERSION_2C, $row['IP_ADDRESS'], $row['RO'], 5000000);
		$output = $session->walk($dot3StatsIndex);
		foreach ($output as $oid => $index) {
			$rrd_name = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $index . "_traffic.rrd";
			$session = new SNMP(SNMP::VERSION_2C, $row['IP_ADDRESS'], $row['RO']);
			$ifHCInOctets = $session->get($snmp_obj->get_pon_oid("ifHCInOctets", "OLT"). "." . $index);
			$ifHCOutOctets = $session->get($snmp_obj->get_pon_oid("ifHCOutOctets", "OLT"). "." . $index);
			if(!is_file($rrd_name)){
				$opts = array( "--step", "300", "--start", "0",
				   "DS:input:DERIVE:1800:0:U",
				   "DS:output:DERIVE:1800:0:U",
				   "RRA:AVERAGE:0.5:1:600",
				   "RRA:AVERAGE:0.5:6:700",
				   "RRA:AVERAGE:0.5:24:775",
				   "RRA:AVERAGE:0.5:288:797",
				   "RRA:MAX:0.5:1:600",
				   "RRA:MAX:0.5:6:700",
				   "RRA:MAX:0.5:24:775",
				   "RRA:MAX:0.5:288:797"
				);
				$ret = rrd_create($rrd_name, $opts);

				if( $ret == 0 )
				{
					$err = rrd_error();
					return $err;
				}
			}
			$ret = rrd_update($rrd_name, array("N:$ifHCInOctets:$ifHCOutOctets"));
			if( $ret == 0 ){
				$err = rrd_error();
				echo "ERROR occurred: $err\n";
			}
		}
		$olt_temp_oid = $snmp_obj->get_pon_oid("olt_temp_oid", "OLT");
		$olt_cpu_oid = $snmp_obj->get_pon_oid("olt_cpu_oid", "OLT");
		$rrd_name_temp = dirname(__FILE__) . "/rrd/" . $ip_address . "_temp.rrd";
		$rrd_name_cpu = dirname(__FILE__) . "/rrd/" . $ip_address . "_cpu.rrd";
		$session = new SNMP(SNMP::VERSION_2C, $row['IP_ADDRESS'], $row['RO']);
		$olt_temp = $session->get($olt_temp_oid);
		if(!is_file($rrd_name_temp)){
			$opts = array( "--step", "300", "--start", "0",
			   "DS:temp:GAUGE:1800:0:100",
			   "RRA:AVERAGE:0.5:1:600",
			   "RRA:AVERAGE:0.5:6:700",
			   "RRA:AVERAGE:0.5:24:775",
			   "RRA:AVERAGE:0.5:288:797",
			   "RRA:MAX:0.5:1:600",
			   "RRA:MAX:0.5:6:700",
			   "RRA:MAX:0.5:24:775",
			   "RRA:MAX:0.5:288:797"
			);

			$ret = rrd_create($rrd_name_temp, $opts);

			if( $ret == 0 )
			{
				$err = rrd_error();
				return $err;
			}
		}

		$ret = rrd_update($rrd_name_temp, array("N:$olt_temp"));
		if( $ret == 0 )
		{
			$err = rrd_error();
				echo "ERROR occurred: $err\n";
		}
		
		$opts = array( "--step", "300", "--start", "0");
		snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
		snmp_set_quick_print(TRUE);
		snmp_set_enum_print(TRUE);
		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		$session = new SNMP(SNMP::VERSION_1, $row['IP_ADDRESS'], $row['RO']);
		$cpus = $session->walk($olt_cpu_oid);
		$olt_cpu = "";
		foreach ($cpus as $cpu_oid => $cpu) {
			$slot = str_replace($olt_cpu_oid, '', substr($cpu_oid, 0, -1));
			$slot = str_replace('.','',$slot);
			array_push($opts, "DS:cpu$slot:GAUGE:1800:0:100");
			$olt_cpu = $olt_cpu . ":" . $cpu;
		}
		array_push($opts,
			   "RRA:AVERAGE:0.5:1:600",
			   "RRA:AVERAGE:0.5:6:700",
			   "RRA:AVERAGE:0.5:24:775",
			   "RRA:AVERAGE:0.5:288:797",
			   "RRA:MAX:0.5:1:600",
			   "RRA:MAX:0.5:6:700",
			   "RRA:MAX:0.5:24:775",
			   "RRA:MAX:0.5:288:797"
		);
		if(!is_file($rrd_name_cpu)){
			$ret = rrd_create($rrd_name_cpu, $opts);
			if( $ret == 0 )
			{
				$err = rrd_error();
				return $err;
			}
		}
		//$boza = array("$olt_cpu");
		//print_r($boza)	;
		$ret = rrd_update($rrd_name_cpu, array("N$olt_cpu"));
		if( $ret == 0 )
		{
			$err = rrd_error();
				echo "ERROR occurred: $err\n";
		}
	}
}
// UPDATE PON PORTS GRAPHS
try {
	$result = $db->query("SELECT PON.ID, PON.SLOT_ID, PON.PORT_ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO from PON LEFT JOIN OLT on PON.OLT=OLT.ID");
} catch (PDOException $e) {
	echo "Connection Failed:" . $e->getMessage() . "\n";
	exit;
}
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$ip_address = $row['IP_ADDRESS'];
	if ($ip_address_state[$ip_address] == "up") {
		$pon_obj = new pon();
		$port = $pon_obj->type2ponid($row['SLOT_ID'],$row['PORT_ID']);	
		$rrd_name = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $port . "_traffic.rrd";
		$rrd_unicast = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $port . "_unicast.rrd";
		$rrd_broadcast = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $port . "_broadcast.rrd";
		$rrd_multicast = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $port . "_multicast.rrd";
		$ifHCInOctets = $snmp_obj->get_pon_oid("ifHCInOctets", "OLT") . "." . $port;
		$ifHCOutOctets = $snmp_obj->get_pon_oid("ifHCOutOctets", "OLT") . "." . $port;
		//Unicast
		$ifHCInUcastPkts = $snmp_obj->get_pon_oid("ifHCInUcastPkts", "OLT") . "." . $port;
		$ifHCOutUcastPkts = $snmp_obj->get_pon_oid("ifHCOutUcastPkts", "OLT") . "." . $port;
		//Broadcast
		$ifHCInBroadcastPkts = $snmp_obj->get_pon_oid("ifHCInBroadcastPkts", "OLT") . "." . $port;
		$ifHCOutBroadcastPkts = $snmp_obj->get_pon_oid("ifHCOutBroadcastPkts", "OLT") . "." . $port;
		//Multicast
		$ifHCInMulticastPkts = $snmp_obj->get_pon_oid("ifHCInMulticastPkts", "OLT") . "." . $port;
		$ifHCOutMulticastPkts = $snmp_obj->get_pon_oid("ifHCOutMulticastPkts", "OLT") . "." . $port;
		$opts = array("--step", "300", "--start", "0",
		"DS:input:DERIVE:1800:0:U",
		"DS:output:DERIVE:1800:0:U",
		"RRA:AVERAGE:0.5:1:600",
		"RRA:AVERAGE:0.5:6:700",
		"RRA:AVERAGE:0.5:24:775",
		"RRA:AVERAGE:0.5:288:797",
		"RRA:MAX:0.5:1:600",
		"RRA:MAX:0.5:6:700",
		"RRA:MAX:0.5:24:775",
		"RRA:MAX:0.5:288:797"
		);
		
		$opts_packets = array("--step", "300", "--start", "0",
		"DS:input:DERIVE:1800:0:U",
		"DS:output:DERIVE:1800:0:U",
		"RRA:AVERAGE:0.5:1:600",
		"RRA:AVERAGE:0.5:6:700",
		"RRA:AVERAGE:0.5:24:775",
		"RRA:AVERAGE:0.5:288:797",
		"RRA:MAX:0.5:1:600",
		"RRA:MAX:0.5:6:700",
		"RRA:MAX:0.5:24:775",
		"RRA:MAX:0.5:288:797"
		);
		if(!is_file($rrd_name)){
			$ret = rrd_create($rrd_name, $opts);
			if( $ret == 0 ){
				$err = rrd_error();
				return $err;
			}	
		}
		if(!is_file($rrd_unicast)){
			$ret = rrd_create($rrd_unicast, $opts_packets);
			if( $ret == 0 ){
				$err = rrd_error();
				return $err;
			}	
		}
		if(!is_file($rrd_broadcast)){
			$ret = rrd_create($rrd_broadcast, $opts_packets);
			if( $ret == 0 ){
				$err = rrd_error();
				return $err;
			}	
		}
		if(!is_file($rrd_multicast)){
			$ret = rrd_create($rrd_multicast, $opts_packets);
			if( $ret == 0 ){
				$err = rrd_error();
				return $err;
			}	
		}
		
		
		$session = new SNMP(SNMP::VERSION_2C, $row['IP_ADDRESS'], $row['RO']);
		$total_input_traffic = $session->get($ifHCInOctets);
		$total_output_traffic = $session->get($ifHCOutOctets);
		$ret = rrd_update($rrd_name, array("N:$total_input_traffic:$total_output_traffic"));
		$unicast_in = $session->get($ifHCInUcastPkts);
		$unicast_out = $session->get($ifHCOutUcastPkts);
		$ret = rrd_update($rrd_unicast, array("N:$unicast_in:$unicast_out"));
		if( $ret == 0 )
		{
			$err = rrd_error();
			echo "ERROR occurred: $err\n";
		}

		$broadcast_in = $session->get($ifHCInBroadcastPkts);
		$broadcast_out = $session->get($ifHCOutBroadcastPkts);
		$ret = rrd_update($rrd_broadcast, array("N:$broadcast_in:$broadcast_out"));
		if( $ret == 0 )
		{
			$err = rrd_error();
			echo "ERROR occurred: $err\n";
		}
		
		$multicast_in = $session->get($ifHCInMulticastPkts);
		$multicast_out = $session->get($ifHCOutMulticastPkts);
		$ret = rrd_update($rrd_multicast, array("N:$multicast_in:$multicast_out"));
		if( $ret == 0 )
		{
			$err = rrd_error();
			echo "ERROR occurred: $err\n";
		}
	}
}


?>
