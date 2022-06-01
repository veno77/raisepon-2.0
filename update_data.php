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

$sn_list_full= array();
$mysql_dump_full = array();



try {
	$conn = db_connect::getInstance();
	$result = $conn->db->query("SELECT OLT.ID, OLT.NAME, MODEL, INET_NTOA(IP_ADDRESS) as IP_ADDRESS, RO, RW, OLT_MODEL.TYPE as TYPE from OLT LEFT JOIN OLT_MODEL ON OLT.MODEL=OLT_MODEL.ID");
} catch (PDOException $e) {
	$error = "Connection Failed:" . $e->getMessage() . "\n";
	return $error;
}
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$mysql_dump = array();
	$sn_list = array();
	//Line Profiles
	$line_profile_oid = $snmp_obj->get_pon_oid("line_profile_oid", $row['TYPE']);
	$service_profile_oid = $snmp_obj->get_pon_oid("service_profile_oid", $row['TYPE']);
	$onu_status_oid = $snmp_obj->get_pon_oid("onu_status_oid", $row['TYPE']);
	$octets_in_ethernet_oid = $snmp_obj->get_pon_oid("uni_octets_in_ethernet_oid", $row['TYPE']) ;
	$octets_out_ethernet_oid = $snmp_obj->get_pon_oid("uni_octets_out_ethernet_oid", $row['TYPE']) ;
	//Description
	$description_oid = $snmp_obj->get_pon_oid("description_oid", $row['TYPE']) ;	
	//SN
	$onu_sn_oid = $snmp_obj->get_pon_oid("onu_sn_oid", $row['TYPE']) ;
	$olt = $row["ID"];
	$ip_address = $row["IP_ADDRESS"];
	$olt_status_oid = $snmp_obj->get_pon_oid("olt_status_oid", "OLT");
	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	$session = new SNMP(SNMP::VERSION_2C, $ip_address, $row['RO']);
	$olt_status = $session->get($olt_status_oid);
	$customers_obj = new customers();
	if ($olt_status) {
		$ip_address_state[$ip_address] = "up";
		exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $onu_status_oid", $output , $return_var);
		$onu_status = array();
		foreach($output as $line) {
			if (strpos($line, $onu_status_oid) !== false) {
				$line = str_replace("." . $onu_status_oid . ".", "", $line);
				$line = explode(" ", $line);
				$onu_status[$line[0]] = $line[1];
			}
		}
		//collect line_profile ids for each onu
		exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $line_profile_oid", $output , $return_var);
		$line_profiles = array();
		foreach($output as $line) {
			if (strpos($line, $line_profile_oid) !== false) {
				$line = str_replace("." . $line_profile_oid . ".", "", $line);
				$line = explode(" ", $line);
				$line_profiles[$line[0]] = $line[1];
			}
		}
		//collect service_profiles for each onu
		exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $service_profile_oid", $output , $return_var);
		$service_profiles = array();
		foreach($output as $line) {
			if (strpos($line, $service_profile_oid) !== false) {
				$line = str_replace("." . $service_profile_oid . ".", "", $line);
				$line = explode(" ", $line);
				$service_profiles[$line[0]] = $line[1];
			}
		}
		//collect description for each onu
		exec("$snmpbulkwalk -OnQ -Cc -v2c -c $row[RO] $ip_address $description_oid", $output , $return_var);
		$descriptions = array();
		foreach($output as $line) {
			if (strpos($line, $description_oid) !== false) {
				$line = str_replace("." . $description_oid . ".", "", $line);
				$line = explode("=", $line);
				$descriptions[trim($line[0])] = trim(trim($line[1]),"\"");
			}
		}
		unset($output);
		exec("$snmpbulkwalk -Onq -Cc -v2c -c $row[RO] $ip_address $onu_sn_oid", $output , $return_var);
		foreach($output as $line) {
			if (strpos($line, $onu_sn_oid) !== false) {
				$line = str_replace("." . $onu_sn_oid . ".", "", $line);
				$line = explode("\"", $line);
				$key = trim($line[0]," ");
				$line_profile = $line_profiles[$key];
				$status = $onu_status[$key];
				$service_profile = $service_profiles[$key];
				$description = $descriptions[$key];
				if ($line_profile == "65535"){
					$line_profile = "";
				}
				if ($service_profile == "65535")
					$service_profile = "";
				$sn_list[str_replace(" ", "",$line[1])] = array(intval($line[0]), $line_profile, $service_profile, $description);
			}
		}
		$sn_list_full[$olt] = $sn_list;
		try {
			$result2 = $db->query("SELECT CUSTOMERS.ID as ID, CUSTOMERS.NAME, CUSTOMERS.ADDRESS, SN, SERVICE_PROFILE.SERVICE_PROFILE_ID, SERVICE_PROFILE.PORTS, SERVICE_PROFILE.HGU, SERVICE_PROFILE.RF, LINE_PROFILE.LINE_PROFILE_ID, OLT.NAME as OLT_NAME, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO as RO, OLT_MODEL.TYPE, PON.NAME as PON_NAME, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE, PON_ONU_ID from CUSTOMERS LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN SERVICE_PROFILE on SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID LEFT JOIN LINE_PROFILE on SERVICES.LINE_PROFILE_ID=LINE_PROFILE.ID LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where CUSTOMERS.OLT='$olt'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
			$sn = $row2["SN"];
			$customers_obj = new customers();
			$big_onu_id = $customers_obj->type2id($row2['SLOT_ID'], $row2['PORT_ID'], $row2['PON_ONU_ID']);
			$mysql_dump[$row2["SN"]] = array($big_onu_id, $row2["LINE_PROFILE_ID"], $row2["SERVICE_PROFILE_ID"], $row2["NAME"]);
			$olt_ip_address = $row2["IP_ADDRESS"];			
		}
		$mysql_dump_full[$olt] = $mysql_dump;
	}else {
		$ip_address_state[$ip_address] = "down";
	}
}

//print_r($sn_list_full);
//print_r($mysql_dump_full);

function array_diff_assoc_recursive(array $array, array ...$arrays)
{
	$func  = function($array1, $array2) use (&$func){
		$difference = [];
		foreach ($array1 as $key => $value) {
			if (is_array($value)) {
				if (!isset($array2[$key]) || !is_array($array2[$key])) {
					$difference[$key] = $value;
				} else {
					$new_diff = $func($value, $array2[$key]);
					if (!empty($new_diff)) {
						$difference[$key] = $new_diff;
					}
				}
			} else {
				if (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
					$difference[$key] = $value;
				}
			}
		}
		return $difference;
	};
	$diffs = $array;
	foreach ($arrays as $_array) {
		$diffs = $func($diffs, $_array);
	}
	return $diffs;
}

$difference = array_diff_assoc_recursive($sn_list_full, $mysql_dump_full);

print_r($difference);

//exit();

if (!empty($difference)){
	foreach ($difference as $olt_id_key=>$value){
		foreach($value as $sn_key=>$snmp_data){
			$service = "";
			if (!empty($snmp_data[0])){
				try {
					$pon_port_info = $customers_obj->id2type($snmp_data[0]);
					$result = $db->query("SELECT ID from PON where OLT='$olt_id_key' AND SLOT_ID='$pon_port_info[0]' AND PORT_ID='$pon_port_info[1]'");
				} catch (PDOException $e) {
					echo "Connection Failed:" . $e->getMessage() . "\n";
					exit;
				}
				while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
					$pon_port_id = $row["ID"];
				}
			}

			if (!empty($snmp_data[1]) && !empty($snmp_data[2])){
				try {
					$result = $db->query("SELECT SERVICES.ID from SERVICES LEFT JOIN LINE_PROFILE on LINE_PROFILE.ID=SERVICES.LINE_PROFILE_ID LEFT JOIN SERVICE_PROFILE on SERVICE_PROFILE.ID=SERVICES.SERVICE_PROFILE_ID where LINE_PROFILE.LINE_PROFILE_ID='$snmp_data[1]' AND SERVICE_PROFILE.SERVICE_PROFILE_ID='$snmp_data[2]'");
				} catch (PDOException $e) {
					echo "Connection Failed:" . $e->getMessage() . "\n";
					exit;
				}
				while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
					$service = $row["ID"];
				}
			}	
			if(!empty($mysql_dump_full[$olt_id_key][$sn_key][0])){
				if (!empty($service)) {
					try {
						$conn = db_connect::getInstance();
						$result = $conn->db->query("UPDATE CUSTOMERS SET SERVICE = '$service' where SN = '$sn_key'");
					} catch (PDOException $e) {
						$error = "Connection Failed:" . $e->getMessage() . "\n";
						exit($error);	
					}
				}
				if(!empty($snmp_data[0])){
					try {
						$conn = db_connect::getInstance();
						$result = $conn->db->query("UPDATE CUSTOMERS SET OLT = '$olt_id_key', PON_PORT = '$pon_port_id', PON_ONU_ID = '$pon_port_info[2]' where SN = '$sn_key'");
					} catch (PDOException $e) {
						$error = "Connection Failed:" . $e->getMessage() . "\n";
						return $error;	
					}
				}
				if(!empty($snmp_data[3])){
					try {
						$conn = db_connect::getInstance();
						$result = $conn->db->query("UPDATE CUSTOMERS SET NAME = '$snmp_data[3]' where SN = '$sn_key'");
					} catch (PDOException $e) {
						$error = "Connection Failed:" . $e->getMessage() . "\n";
						return $error;	
					}
				}
			}else{
				if(empty($snmp_data[3]))
					$snmp_data[3] = $sn_key;
				if (!empty($service)) {
					try {
						$conn = db_connect::getInstance();
						$result = $conn->db->query("INSERT INTO CUSTOMERS (NAME, ADDRESS, EGN, OLT, PON_PORT, PON_ONU_ID, SN, SERVICE) VALUES ('$snmp_data[3]', '', NULL, '$olt_id_key', '$pon_port_id', '$pon_port_info[2]', '$sn_key', '$service')");
					} catch (PDOException $e) {
						$error = "Connection Failed:" . $e->getMessage() . "\n";
						echo $error;
					}
				}else{
					try {
						$conn = db_connect::getInstance();
						$result = $conn->db->query("INSERT INTO CUSTOMERS (NAME, ADDRESS, EGN, OLT, PON_PORT, PON_ONU_ID, SN) VALUES ('$snmp_data[3]', '', NULL, '$olt_id_key', '$pon_port_id', '$pon_port_info[2]', '$sn_key')");
					} catch (PDOException $e) {
						$error = "Connection Failed:" . $e->getMessage() . "\n";
						echo $error;
					}
				}				
			}
		}
	}
}




?>
