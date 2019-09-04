<?php
include_once("dbconnect.php");
include_once("classes/snmp_class.php");
include_once("classes/customers_class.php");
include_once("classes/pon_class.php");

$snmpbulkwalk = "/usr/local/bin/snmpbulkwalk";
if(!is_file($snmpbulkwalk)) {
	$snmpbulkwalk = "/usr/bin/snmpbulkwalk";
}


$snmp_obj = new snmp_oid();

$ip_address_state = array();

$traffic_in_oid = $snmp_obj->get_pon_oid("ifHCInOctets", "OLT") ;
$traffic_out_oid = $snmp_obj->get_pon_oid("ifHCOutOctets", "OLT") ;
//Unicast
$unicast_in_oid = $snmp_obj->get_pon_oid("ifHCInUcastPkts", "OLT") ;
$unicast_out_oid = $snmp_obj->get_pon_oid("ifHCOutUcastPkts", "OLT") ;
//Broadcast
$broadcast_in_oid = $snmp_obj->get_pon_oid("ifHCInBroadcastPkts", "OLT") ;
$broadcast_out_oid = $snmp_obj->get_pon_oid("ifHCOutBroadcastPkts", "OLT");
//Multicast
$multicast_in_oid = $snmp_obj->get_pon_oid("ifHCInMulticastPkts", "OLT") ;
$multicast_out_oid = $snmp_obj->get_pon_oid("ifHCOutMulticastPkts", "OLT") ;



try {
	$conn = db_connect::getInstance();
	$result = $conn->db->query("SELECT OLT.ID, OLT.NAME, MODEL, INET_NTOA(IP_ADDRESS) as IP_ADDRESS, RO, RW, OLT_MODEL.TYPE as TYPE from OLT LEFT JOIN OLT_MODEL ON OLT.MODEL=OLT_MODEL.ID");
} catch (PDOException $e) {
	$error = "Connection Failed:" . $e->getMessage() . "\n";
	return $error;
}
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	
	$onu_status_oid = $snmp_obj->get_pon_oid("onu_status_oid", $row{'TYPE'});
	$octets_in_ethernet_oid = $snmp_obj->get_pon_oid("uni_octets_in_ethernet_oid", $row{'TYPE'}) ;
	$octets_out_ethernet_oid = $snmp_obj->get_pon_oid("uni_octets_out_ethernet_oid", $row{'TYPE'}) ;

	$olt = $row["ID"];
	$ip_address = $row["IP_ADDRESS"];
	$olt_status_oid = $snmp_obj->get_pon_oid("olt_status_oid", "OLT");
	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	$session = new SNMP(SNMP::VERSION_2C, $ip_address, $row{'RO'});
	$olt_status = $session->get($olt_status_oid);
	$customers_obj = new customers();
	if ($olt_status) {
		$ip_address_state[$ip_address] = "up";
		exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $octets_in_ethernet_oid", $output , $return_var);
		$octets_in_ethernet = array();
		foreach($output as $line) {
			if (strpos($line, $octets_in_ethernet_oid) !== false) {
				$line = str_replace("." . $octets_in_ethernet_oid . ".", "", $line);
				$line = explode(" ", $line);
				$octets_in_ethernet[$line[0]] = $line[1];
			}
		}
		exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $octets_out_ethernet_oid", $output , $return_var);
		$octets_out_ethernet = array();
		foreach($output as $line) {
			if (strpos($line, $octets_out_ethernet_oid) !== false) {
				$line = str_replace("." . $octets_out_ethernet_oid . ".", "", $line);
				$line = explode(" ", $line);
				$octets_out_ethernet[$line[0]] = $line[1];
			}
		}			
		exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $onu_status_oid", $output , $return_var);
		$onu_status = array();
		foreach($output as $line) {
			if (strpos($line, $onu_status_oid) !== false) {
				$line = str_replace("." . $onu_status_oid . ".", "", $line);
				$line = explode(" ", $line);
				$onu_status[$line[0]] = $line[1];
			}
		}
		if ($row{'TYPE'} == "EPON") {
			exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $traffic_in_oid", $output , $return_var);
			$traffic_in = array();
			foreach($output as $line) {
				if (strpos($line, $traffic_in_oid) !== false) {
					$line = str_replace("." . $traffic_in_oid . ".", "", $line);
					$line = explode(" ", $line);
					$traffic_in[$line[0]] = $line[1];
				}
			}
			exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $traffic_out_oid", $output , $return_var);
			$traffic_out = array();
			foreach($output as $line) {
				if (strpos($line, $traffic_out_oid) !== false) {
					$line = str_replace("." . $traffic_out_oid . ".", "", $line);
					$line = explode(" ", $line);
					$traffic_out[$line[0]] = $line[1];
				}
			}
			exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $unicast_in_oid", $output , $return_var);
			$unicast_in = array();
			foreach($output as $line) {
				if (strpos($line, $unicast_in_oid) !== false) {
					$line = str_replace("." . $unicast_in_oid . ".", "", $line);
					$line = explode(" ", $line);
					$unicast_in[$line[0]] = $line[1];
				}
			}
			exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $unicast_out_oid", $output , $return_var);
			$unicast_out = array();
			foreach($output as $line) {
				if (strpos($line, $unicast_out_oid) !== false) {
					$line = str_replace("." . $unicast_out_oid . ".", "", $line);
					$line = explode(" ", $line);
					$unicast_out[$line[0]] = $line[1];
				}
			}
			exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $broadcast_in_oid", $output , $return_var);
			$broadcast_in = array();
			foreach($output as $line) {
				if (strpos($line, $broadcast_in_oid) !== false) {
					$line = str_replace("." . $broadcast_in_oid . ".", "", $line);
					$line = explode(" ", $line);
					$broadcast_in[$line[0]] = $line[1];
				}
			}
			exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $broadcast_out_oid", $output , $return_var);
			$broadcast_out = array();
			foreach($output as $line) {
				if (strpos($line, $broadcast_out_oid) !== false) {
					$line = str_replace("." . $broadcast_out_oid . ".", "", $line);
					$line = explode(" ", $line);
					$broadcast_out[$line[0]] = $line[1];
				}
			}
				exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $multicast_in_oid", $output , $return_var);
			$multicast_in = array();
			foreach($output as $line) {
				if (strpos($line, $multicast_in_oid) !== false) {
					$line = str_replace("." . $multicast_in_oid . ".", "", $line);
					$line = explode(" ", $line);
					$multicast_in[$line[0]] = $line[1];
				}
			}
			exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $multicast_out_oid", $output , $return_var);
			$multicast_out = array();
			foreach($output as $line) {
				if (strpos($line, $multicast_out_oid) !== false) {
					$line = str_replace("." . $multicast_out_oid . ".", "", $line);
					$line = explode(" ", $line);
					$multicast_out[$line[0]] = $line[1];
				}
			}
		}
		try {
			$result2 = $db->query("SELECT CUSTOMERS.ID as ID, CUSTOMERS.NAME, CUSTOMERS.ADDRESS, SN, SERVICE_PROFILE.PORTS, SERVICE_PROFILE.HGU, SERVICE_PROFILE.RF, OLT.NAME as OLT_NAME, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO as RO, OLT_MODEL.TYPE, PON.NAME as PON_NAME, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE, PON_ONU_ID from CUSTOMERS LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN SERVICE_PROFILE on SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where CUSTOMERS.OLT='$olt'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
			$hgu = $row2{'HGU'};
			$sn = $row2["SN"];
			$customers_obj = new customers();
			$big_onu_id = $customers_obj->type2id($row2{'SLOT_ID'}, $row2{'PORT_ID'}, $row2{'PON_ONU_ID'});
			$olt_ip_address = $row2["IP_ADDRESS"];	
			$rrd_traffic = dirname(__FILE__) . "/rrd/" . $sn . "_traffic.rrd"; 
			$error = "0";
			if(!is_file($rrd_traffic))
				$error = "1";
			$rrd_unicast = dirname(__FILE__) . "/rrd/" . $sn . "_unicast.rrd";
			if(!is_file($rrd_unicast))
				$error = "1";
			$rrd_broadcast = dirname(__FILE__) . "/rrd/" . $sn . "_broadcast.rrd";
			if(!is_file($rrd_broadcast))
				$error = "1";
			$rrd_multicast = dirname(__FILE__) . "/rrd/" . $sn . "_multicast.rrd";
			if(!is_file($rrd_multicast))
				$error = "1";
			if ($error == "1") {
				$customers_obj = new customers();
				$customers_obj->setSn($sn);
				$error = $customers_obj->onu_traffic_rrd();
				if (!empty($error)) {
					return $error;
				}
			}
			
			$total_input_traffic = 0;
			$total_output_traffic = 0;
			$multicast_in = 0;
			$multicast_out = 0;
			
			if ($onu_status[$big_onu_id] == "1") {
				echo $sn . "\n";
				if ($row2{'PON_TYPE'} == "EPON") {
					$ret = rrd_update($rrd_traffic, array("N:$traffic_in[$big_onu_id]:$traffic_out[$big_onu_id]"));
					if( $ret == 0 )
					{
						$err = rrd_error();
						echo "ERROR occurred: $err\n";
					}
				
					$ret = rrd_update($rrd_unicast, array("N:$unicast_in[$big_onu_id]:$unicast_out[$big_onu_id]"));
					if( $ret == 0 )
					{
						$err = rrd_error();
						echo "ERROR occurred: $err\n";
					}
					
					$ret = rrd_update($rrd_broadcast, array("N:$broadcast_in[$big_onu_id]:$broadcast_out[$big_onu_id]"));
					if( $ret == 0 )
					{
						$err = rrd_error();
						echo "ERROR occurred: $err\n";
					}

					$ret = rrd_update($rrd_multicast, array("N:$multicast_in[$big_onu_id]:$multicast_out[$big_onu_id]"));
					if( $ret == 0 )
					{
						$err = rrd_error();
						echo "ERROR occurred: $err\n";
					}		
				}
				if ($hgu !== "Yes") {
					for ($i=1; $i <= $row2{'PORTS'}; $i++) {
						// $ethernet_id = $row2{'SLOT_ID'} * 10000000 + $row2{'PORT_ID'} * 100000 + $row2{'PON_ONU_ID'} * 1000 + $i;
						if ($row2{'PON_ONU_ID'} < 100) {
							$ethernet_id = 10000000 * $row2{'SLOT_ID'} + 100000 * $row2{'PORT_ID'} + 1000 * $row2{'PON_ONU_ID'} + $i;
						}else{
							$ethernet_id = (3<<28)+(10000000 * $row2{'SLOT_ID'} + 100000 * $row2{'PORT_ID'} + 1000 * ($row2{'PON_ONU_ID'}%100) + $i);
						}
						$octets_ethernet = dirname(__FILE__) . "/rrd/" . $sn . "_" . $i . ".rrd";
						if(!is_file($octets_ethernet)) {
							$opts = array( "--step", "300", "--start", "0",
							   "DS:input:DERIVE:600:0:U",
							   "DS:output:DERIVE:600:0:U",
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
						
						$octets_in_ethernet_val	= $octets_in_ethernet[$ethernet_id];
						$octets_out_ethernet_val = $octets_out_ethernet[$ethernet_id];
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
	}else {
		$ip_address_state[$ip_address] = "down";
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
		$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
		$output = $session->walk($dot3StatsIndex);
		foreach ($output as $oid => $index) {
			$rrd_name = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $index . "_traffic.rrd";
			$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
			$ifHCInOctets = $session->get($snmp_obj->get_pon_oid("ifHCInOctets", "OLT"). "." . $index);
			$ifHCOutOctets = $session->get($snmp_obj->get_pon_oid("ifHCOutOctets", "OLT"). "." . $index);
			if(!is_file($rrd_name)){
				$opts = array( "--step", "300", "--start", "0",
				   "DS:input:DERIVE:600:0:U",
				   "DS:output:DERIVE:600:0:U",
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
	$ip_address = $row{'IP_ADDRESS'};
	if ($ip_address_state[$ip_address] == "up") {
		$pon_obj = new pon();
		$port = $pon_obj->type2ponid($row{'SLOT_ID'},$row{'PORT_ID'});	
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
		"DS:input:DERIVE:600:0:U",
		"DS:output:DERIVE:600:0:U",
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
		"DS:input:DERIVE:600:0:U",
		"DS:output:DERIVE:600:0:U",
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
		
		
		$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
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
