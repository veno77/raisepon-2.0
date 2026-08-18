<?php
include_once("dbconnect.php");
include_once("classes/snmp_class.php");
include_once("classes/customers_class.php");
include_once("classes/pon_class.php");

$snmp_obj = new snmp_oid();
$customers_obj = new customers();
$pon_obj = new pon();

$base_rrd = __DIR__ . "/rrd/";

$ip_address_state = array();

$snmpget = "/usr/local/bin/snmpget";
if(!is_file($snmpget)) {
	$snmpget = "/usr/bin/snmpget";
}

$insert_list = [];
$update_list = [];

$oid_cache = [];

function get_oid_cached($name, $type) {
    global $snmp_obj, $oid_cache;

    $key = $type . "_" . $name;

    if (!isset($oid_cache[$key])) {
        $oid_cache[$key] = $snmp_obj->get_pon_oid($name, $type);
    }

    return $oid_cache[$key];
}

/*
 * NOTE: session reuse (caching one SNMP object per host/community and
 * calling ->get()/->walk() on it many times) was tested and found to be
 * unreliable on this system - even a freshly created session object only
 * reliably survives a single get()/walk() call before subsequent calls
 * fail with a misleading "Could not open snmp connection" error, even
 * though the host is reachable and answers fine via manual snmpget.
 *
 * To avoid that, we open a brand-new SNMP session for every single
 * get()/walk() call. This mirrors the one pattern that has proven 100%
 * reliable in testing (the original OLT status-check loop, which always
 * created a fresh SNMP object per call).
 */

function new_snmp_session($ip, $community) {
    $session = new SNMP(SNMP::VERSION_2C, $ip, $community, 2000000, 2);
    $session->valueretrieval = SNMP_VALUE_PLAIN;
    $session->oid_output_format = SNMP_OID_OUTPUT_NUMERIC;
    $session->quick_print = true;
    $session->enum_print = true;
    return $session;
}

function snmp_get_safe($ip, $community, $oid) {
    $session = new_snmp_session($ip, $community);
    $value = @$session->get($oid);

    if ($value === false) {
        error_log("SNMP get failed for $ip oid=$oid");
    }

    return $value;
}

// Batched get for multiple OIDs in one round-trip (used for ethernet octets)
function snmp_get_multi_safe($ip, $community, array $oids) {
    $session = new_snmp_session($ip, $community);
    $values = @$session->get($oids);

    if (!is_array($values)) {
        error_log("SNMP multi-get failed for $ip oids=" . implode(",", $oids));
        return array_fill(0, count($oids), false);
    }

    return array_values($values);
}

function snmp_walk_safe($ip, $community, $oid) {
    $session = new_snmp_session($ip, $community);
    $value = @$session->walk($oid);

    if (!is_array($value)) {
        error_log("SNMP walk failed for $ip oid=$oid");
    }

    return $value;
}

try {
	$conn = db_connect::getInstance();
	$result = $conn->db->query("SELECT ID, NAME, MODEL, INET_NTOA(IP_ADDRESS) as IP_ADDRESS, RO, RW from OLT");
} catch (PDOException $e) {
	$error = "Connection Failed:" . $e->getMessage() . "\n";
	return $error;
}
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$ip_address = $row["IP_ADDRESS"];
	$olt_status_oid = get_oid_cached("olt_status_oid", "OLT");
	$olt_status = snmp_get_safe($row['IP_ADDRESS'], $row['RO'], $olt_status_oid);
	if (!empty($olt_status)) {
		$ip_address_state[$ip_address] = "up";
	}else {
		$ip_address_state[$ip_address] = "down";
	}
}

$rx_map = [];
try {
	$res = $db->query("SELECT CUSTOMERS_ID, RX_POWER FROM ONU_RX_POWER");
} catch (PDOException $e) {
	echo "Connection Failed:" . $e->getMessage() . "\n";
	exit;
}

foreach ($res as $r) {
    $rx_map[$r['CUSTOMERS_ID']] = $r['RX_POWER'];
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

	$big_onu_id = null; // reset every iteration to avoid stale values leaking across rows

	$catv_input_id = $row['SLOT_ID'] * 10000000 + $row['PORT_ID'] * 100000 + $row['PON_ONU_ID'] * 1000 + 160;
	$rf = $row['RF'];
	$sn = $row["SN"];
	$ip_address = $row["IP_ADDRESS"];

	// Skip customers with no PON port assigned - nothing valid to poll
	if (empty($row['PON_TYPE']) || $row['SLOT_ID'] === null) {
		continue;
	}

	$big_onu_id = $customers_obj->type2id($row['SLOT_ID'], $row['PORT_ID'], $row['PON_ONU_ID']);

	if ($row['PON_TYPE'] == "GPON") {

		if ($row['PON_ONU_ID'] < 100) {
			$index_2 = 10000000 * $row['SLOT_ID'] + 100000 * $row['PORT_ID'] + 1000 * $row['PON_ONU_ID'] + 1;
		}else{
			$index_2 = (3<<28)+(10000000 * $row['SLOT_ID'] + 100000 * $row['PORT_ID'] + 1000 * ($row['PON_ONU_ID']%100) + 1);
		}
		$catv_input_id = $index_2;
	}
	$total_input_traffic = 0;
	$total_output_traffic = 0;
	if ($row['PON_TYPE'] == "XGSPON") {
		$interface_pon = "010";
		$slot = str_pad(decbin($row['SLOT_ID']),5, "0", STR_PAD_LEFT);
		$pon_port = str_pad(decbin($row['PORT_ID']), 6, "0", STR_PAD_LEFT);
		$onu_id = str_pad(decbin($row['PON_ONU_ID']), 10, "0", STR_PAD_LEFT);
		$onu_port_id = str_pad(decbin("1"), 8, "0", STR_PAD_LEFT);
		$index_2 = bindec($interface_pon . $slot . $pon_port . $onu_id . $onu_port_id);
		$catv_input_id = $snmp_obj->RmtOnuIntId($row['SLOT_ID'], $row['PORT_ID'], $row['PON_ONU_ID'], "1");
		$traffic_id = $row['SLOT_ID'] * 10000000 + $row['PORT_ID'] * 100000 + $row['PON_ONU_ID'];
		$traffic_in_oid = get_oid_cached("gponRmtOnuDevTxOctV", "XGSPON") . "." . $traffic_id;
		$traffic_out_oid = get_oid_cached("gponRmtOnuDevRxOctV", "XGSPON") . "." . $traffic_id;
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
	$rrd_traffic = dirname(__FILE__) . "/rrd/" . $sn . "_traffic.rrd";
	if (!is_file($rrd_traffic)) {
		$customers_obj = new customers();
		$customers_obj->setSn($sn);
		$error = $customers_obj->onu_traffic_rrd();
		if (!empty($error)) {
			return $error;
		}
	}


	if(isset($row['IP_ADDRESS'])) {
		if ($ip_address_state[$ip_address] == "up") {
			$status = "0";
			//GET STATUS via SNMP
			$status_oid = get_oid_cached("onu_status_oid", $row['PON_TYPE']) . "." . $big_onu_id;
			$status = snmp_get_safe($row['IP_ADDRESS'], $row['RO'], $status_oid);
			if ($status == "1") {
				echo $sn . "\n";
				$octets_in_ethernet = get_oid_cached("uni_octets_in_ethernet_oid", $row['PON_TYPE']) . ".";
				$octets_out_ethernet = get_oid_cached("uni_octets_out_ethernet_oid", $row['PON_TYPE']) . ".";
				//Onu Power
				$recv_power_oid = get_oid_cached("onu_recv_power_oid", $row['PON_TYPE']) . "." . $index_2;
				$send_power_oid = get_oid_cached("onu_send_power_oid", $row['PON_TYPE']) . "." . $index_2;
				//OLT RX Power
				$olt_rx_power_oid = get_oid_cached("olt_rx_power_oid", $row['PON_TYPE']) . "." . $big_onu_id;
				// RF Power
				$rf_input_power_oid = get_oid_cached("onu_rf_rx_power_oid", $row['PON_TYPE']) . "." . $catv_input_id;

				$olt_rx_power = snmp_get_safe($row['IP_ADDRESS'], $row['RO'], $olt_rx_power_oid);
				$olt_rx_power = round($olt_rx_power/10,4);
				if ($olt_rx_power > -30) {
					$recv_power = snmp_get_safe($row['IP_ADDRESS'], $row['RO'], $recv_power_oid);
					if ($row['PON_TYPE'] == "GPON" || $row['PON_TYPE'] == "XGSPON" ) {
						if ($recv_power > 32767)
							$recv_power = $recv_power - 65535 - 1;
						$recv_power = round(($recv_power-15000)/500,2);
					}
					if ($row['PON_TYPE'] == "EPON") {
						$recv_power = round(10*log10($recv_power/10000),2);
					}


					if ($rf == "Yes") {
						$rf_input_power = snmp_get_safe($row['IP_ADDRESS'], $row['RO'], $rf_input_power_oid);
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

					// update arrays with recv_power
					if (array_key_exists($id, $rx_map)) {
							$update_list[] = [$id, $recv_power];
						} else {
							$insert_list[] = [$id, $recv_power];
						}

					//
					if ($hgu !== "Yes") {
						for ($i=1; $i <= $row['PORTS']; $i++) {
							$ethernet_id = $row['SLOT_ID'] * 10000000 + $row['PORT_ID'] * 100000 + $row['PON_ONU_ID'] * 1000 + $i;
							if ($row['PON_TYPE'] == "XGSPON")
								$ethernet_id = $snmp_obj->RmtOnuIntId($row['SLOT_ID'], $row['PORT_ID'], $row['PON_ONU_ID'], $i);
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
							$octets = snmp_get_multi_safe($row['IP_ADDRESS'], $row['RO'], [$octets_in_ethernet_id, $octets_out_ethernet_id]);
							$in  = $octets[0] ?? 0;
							$out = $octets[1] ?? 0;
							$ret = rrd_update($octets_ethernet, array("N:$in:$out"));
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
}
// Update exisiting recv_power values

$db->beginTransaction();

$stmt = $db->prepare("
    UPDATE ONU_RX_POWER 
    SET RX_POWER = ? 
    WHERE CUSTOMERS_ID = ?
");

foreach ($update_list as $u) {
    $stmt->execute([$u[1], $u[0]]);
}

$db->commit();
// Update new recv_power values
$db->beginTransaction();

$stmt = $db->prepare("
    INSERT INTO ONU_RX_POWER (CUSTOMERS_ID, RX_POWER)
    VALUES (?, ?)
");

foreach ($insert_list as $i) {
    $stmt->execute([$i[0], $i[1]]);
}

$db->commit();


// UPDATE OLT GRAPHS

try {
	$result = $db->query("SELECT OLT.ID, OLT.NAME as OLT_NAME, MODEL, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE as TYPE from OLT LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID");
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

		$ifHCInOctetsOID  = get_oid_cached("ifHCInOctets", "OLT");
		$ifHCOutOctetsOID = get_oid_cached("ifHCOutOctets", "OLT");

		$inOctets  = snmp_walk_safe($ip_address, $row['RO'], $ifHCInOctetsOID);
		$outOctets = snmp_walk_safe($ip_address, $row['RO'], $ifHCOutOctetsOID);

		// If SNMP fails → skip safely (prevents fatal errors)
		if (!is_array($inOctets) || !is_array($outOctets)) {
			error_log("SNMP walk failed for OLT: $ip_address");
			continue;
		}
		$portStats = [];

		foreach ($inOctets as $oid => $value) {
			$index = (int)substr(strrchr($oid, '.'), 1);
			$portStats[$index]['in'] = $value;
		}

		foreach ($outOctets as $oid => $value) {
			$index = (int)substr(strrchr($oid, '.'), 1);
			$portStats[$index]['out'] = $value;
		}

		foreach ($portStats as $index => $stats) {
			$in  = $stats['in']  ?? 0;
			$out = $stats['out'] ?? 0;

			$rrd_name = __DIR__ . "/rrd/" . $ip_address . "_" . $index . "_traffic.rrd";

			if (!is_file($rrd_name)) {
				$opts = [
					"--step", "300",
					"--start", "0",
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
				];

				$ret = rrd_create($rrd_name, $opts);

				if ($ret == 0) {
					echo "RRD create error: " . rrd_error() . "\n";
					continue;
				}
			}

			$ret = rrd_update($rrd_name, ["N:$in:$out"]);

			if ($ret == 0) {
				echo "RRD update error: " . rrd_error() . "\n";
			}
		}
		if ($olt_type == "XGSPON") {
			$olt_temp_oid = get_oid_cached("olt_temp_oid", "XGSPON_OLT");
			$olt_cpu_oid = get_oid_cached("olt_cpu_oid", "XGSPON_OLT");
		}else{
			$olt_temp_oid = get_oid_cached("olt_temp_oid", "OLT");
			$olt_cpu_oid = get_oid_cached("olt_cpu_oid", "OLT");
		}
		$rrd_name_temp = dirname(__FILE__) . "/rrd/" . $ip_address . "_temp.rrd";
		$rrd_name_cpu = dirname(__FILE__) . "/rrd/" . $ip_address . "_cpu.rrd";
		$olt_temp = snmp_get_safe($row['IP_ADDRESS'], $row['RO'], $olt_temp_oid);
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

		// NOTE: original code switched valueretrieval globally to
		// SNMP_VALUE_LIBRARY for this walk (to get typed values with
		// units, e.g. "50" vs "50 percent"). Since sessions are now
		// created fresh per call, this is set directly on the session
		// object instead of via the removed global snmp_set_* calls.
		$cpu_session = new SNMP(SNMP::VERSION_2C, $row['IP_ADDRESS'], $row['RO'], 2000000, 2);
		$cpu_session->oid_output_format = SNMP_OID_OUTPUT_NUMERIC;
		$cpu_session->quick_print = true;
		$cpu_session->enum_print = true;
		$cpu_session->valueretrieval = SNMP_VALUE_LIBRARY;
		$cpus = @$cpu_session->walk($olt_cpu_oid);

		if (!is_array($cpus)) {
			error_log("SNMP CPU walk failed for {$row['IP_ADDRESS']}");
			$cpus = [];
		}

		$olt_cpu = "";
		foreach ($cpus as $cpu_oid => $cpu) {
			$slot = str_replace($olt_cpu_oid, '', substr($cpu_oid, 0, -1));
			$slot = str_replace('.','',$slot);
			array_push($opts, "DS:cpu$slot:GAUGE:1800:0:100");
			$olt_cpu = $cpu/100;
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
		$ret = rrd_update($rrd_name_cpu, array("N:$olt_cpu"));
		if( $ret == 0 )
		{
			$err = rrd_error();
			echo "ERROR occurred: $err\n";
		}
	}
}
// UPDATE PON PORTS GRAPHS


try {
	$result = $db->query("SELECT PON.ID, PON.SLOT_ID, PON.PORT_ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, CARDS_MODEL.PON_TYPE, OLT.RO from PON LEFT JOIN OLT on PON.OLT=OLT.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID");
} catch (PDOException $e) {
	echo "Connection Failed:" . $e->getMessage() . "\n";
	exit;
}


while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$ip_address = $row['IP_ADDRESS'];
	if ($ip_address_state[$ip_address] == "up") {
		if ($row['PON_TYPE'] == "XGSPON") {
			$port = $pon_obj->type3ponid($row['SLOT_ID'],$row['PORT_ID']);
		}else{
			$port = $pon_obj->type2ponid($row['SLOT_ID'],$row['PORT_ID']);
		}
		$rrd_name = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $port . "_traffic.rrd";
		$rrd_unicast = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $port . "_unicast.rrd";
		$rrd_broadcast = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $port . "_broadcast.rrd";
		$rrd_multicast = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $port . "_multicast.rrd";
		$ifHCInOctets = get_oid_cached("ifHCInOctets", "OLT") . "." . $port;
		$ifHCOutOctets = get_oid_cached("ifHCOutOctets", "OLT") . "." . $port;
		//Unicast
		$ifHCInUcastPkts = get_oid_cached("ifHCInUcastPkts", "OLT") . "." . $port;
		$ifHCOutUcastPkts = get_oid_cached("ifHCOutUcastPkts", "OLT") . "." . $port;
		//Broadcast
		$ifHCInBroadcastPkts = get_oid_cached("ifHCInBroadcastPkts", "OLT") . "." . $port;
		$ifHCOutBroadcastPkts = get_oid_cached("ifHCOutBroadcastPkts", "OLT") . "." . $port;
		//Multicast
		$ifHCInMulticastPkts = get_oid_cached("ifHCInMulticastPkts", "OLT") . "." . $port;
		$ifHCOutMulticastPkts = get_oid_cached("ifHCOutMulticastPkts", "OLT") . "." . $port;
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
		if ($row['PON_TYPE'] == "XGSPON") {
			$output = [];
			exec("$snmpget -Onq -Ir -v2c -c $row[RO] $row[IP_ADDRESS] $ifHCInOctets", $output , $return_var);
			foreach($output as $line) {
				if (strpos($line, $ifHCInOctets) !== false) {
					$line = str_replace("." . $ifHCInOctets, " ", $line);
					$total_input_traffic = $line;
				}
			}
			$output = [];
			exec("$snmpget -Onq -Ir -v2c -c $row[RO] $row[IP_ADDRESS] $ifHCOutOctets", $output , $return_var);
			foreach($output as $line) {
				if (strpos($line, $ifHCOutOctets) !== false) {
					$line = str_replace("." . $ifHCOutOctets, " ", $line);
					$total_output_traffic = $line;
				}
			}
			$ret = rrd_update($rrd_name, array("N:$total_input_traffic:$total_output_traffic"));
		}else{
			$total_input_traffic = snmp_get_safe($row['IP_ADDRESS'], $row['RO'], $ifHCInOctets);
			$total_output_traffic = snmp_get_safe($row['IP_ADDRESS'], $row['RO'], $ifHCOutOctets);
			$ret = rrd_update($rrd_name, array("N:$total_input_traffic:$total_output_traffic"));

			$unicast_in = snmp_get_safe($row['IP_ADDRESS'], $row['RO'], $ifHCInUcastPkts);
			$unicast_out = snmp_get_safe($row['IP_ADDRESS'], $row['RO'], $ifHCOutUcastPkts);
			$ret = rrd_update($rrd_unicast, array("N:$unicast_in:$unicast_out"));
			if( $ret == 0 )
			{
				$err = rrd_error();
				echo "ERROR occurred: $err\n";
			}

			$broadcast_in = snmp_get_safe($row['IP_ADDRESS'], $row['RO'], $ifHCInBroadcastPkts);
			$broadcast_out = snmp_get_safe($row['IP_ADDRESS'], $row['RO'], $ifHCOutBroadcastPkts);
			$ret = rrd_update($rrd_broadcast, array("N:$broadcast_in:$broadcast_out"));
			if( $ret == 0 )
			{
				$err = rrd_error();
				echo "ERROR occurred: $err\n";
			}

			$multicast_in = snmp_get_safe($row['IP_ADDRESS'], $row['RO'], $ifHCInMulticastPkts);
			$multicast_out = snmp_get_safe($row['IP_ADDRESS'], $row['RO'], $ifHCOutMulticastPkts);
			$ret = rrd_update($rrd_multicast, array("N:$multicast_in:$multicast_out"));
			if( $ret == 0 )
			{
				$err = rrd_error();
				echo "ERROR occurred: $err\n";
			}
		}
	}
}


?>