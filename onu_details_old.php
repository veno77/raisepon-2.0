<?php
include ("common.php");
include ("dbconnect.php");
navigation();

$port_admin = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {


	if ($_POST["customer_id"]) {
		$customer_id = test_input($_POST["customer_id"]);
	}
	try {
		$result = $db->query("SELECT CUSTOMERS.NAME as NAME, CUSTOMERS.ID, LPAD(HEX(CUSTOMERS.MAC_ADDRESS), 12, '0') as MAC_ADDRESS, PON_ONU_ID, CUSTOMERS.ONU_MODEL, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, CUSTOMERS.STATE as STATE, CUSTOMERS.SVR_TEMPLATE as SVR_TEMPLATE, OLT.ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.NAME as OLT_NAME, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE as TYPE, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, ONU.ID, ONU.PORTS as ONU_PORTS, ONU.RF as RF, ONU.PSE as PSE from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN ONU on CUSTOMERS.ONU_MODEL=ONU.ID where CUSTOMERS.ID = '$customer_id'");
	} catch (PDOException $e) {
		echo "Connection Failed:" . $e->getMessage() . "\n";
		exit;
	}
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$name = $row['NAME'];
		$ip_address = $row['IP_ADDRESS'];
		$port_id = $row['PORT_ID'];
		$slot_id = $row['SLOT_ID'];
		$pon_onu_id = $row['PON_ONU_ID'];
		$olt_name = $row['OLT_NAME'];
		$onu_ports = $row['ONU_PORTS'];
		$ro = $row['RO'];
		$rw = $row['RW'];
		$rf = $row['RF'];
		$pse = $row['PSE'];
		$olt_type = $row["TYPE"];
	}
	if ($_POST["rf_status"]) {
		$rf_status = "1";	
	} else {
		if ($olt_type == "1")
			$rf_status = "0";
		if ($olt_type == "2")
			$rf_status = "2";
	}
	if ($_POST["SUBMIT"] == "SET") {
	if ($rf == "1") {
		$index = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + 162;
		$rf_status_oid = "1.3.6.1.4.1.8886.18.2.6.21.3.1.2." . $index;
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$session = new SNMP(SNMP::VERSION_2C, $ip_address, $rw);
		$rf_status = $session->set($rf_status_oid, 'i', $rf_status);
		if ($session->getError())
				exit(var_dump($session->getError()));      
		print "<center><font color=green>RF Status Set Succesfully</font></center>";
		}
	
	}

	
	
	if ($_POST["SUBMIT"] == "Reboot") {
		$index = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id;
		$reboot_oid = "1.3.6.1.4.1.8886.18.2.6.1.3.1.1." . $index;
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$session = new SNMP(SNMP::VERSION_2C, $ip_address, $rw);
		$reboot = $session->set($reboot_oid, 'i', '1');
		if ($session->getError())
			exit(var_dump($session->getError()));      
        print "<center>Onu Rebooted Succesfully</center>";
		
	}
	
}




if ($_GET) {
	$customer_id = $_GET['id'];
	if (!preg_match('/^[0-9]*$/', $customer_id)) {
		print "that sux";
		exit;
	} else {
		try {
			$result = $db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME as NAME, LPAD(HEX(CUSTOMERS.MAC_ADDRESS), 12, '0') as MAC_ADDRESS, PON_ONU_ID, CUSTOMERS.ONU_MODEL, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, CUSTOMERS.STATE as STATE, CUSTOMERS.SVR_TEMPLATE as SVR_TEMPLATE, OLT.ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.NAME as OLT_NAME, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, ONU.ID, ONU.PORTS as ONU_PORTS, ONU.RF as RF, ONU.PSE as PSE from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN ONU on CUSTOMERS.ONU_MODEL=ONU.ID where CUSTOMERS.ID = '$customer_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
	}
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$ip_address = $row['IP_ADDRESS'];
		$port_id = $row['PORT_ID'];
		$slot_id = $row['SLOT_ID'];
		$pon_onu_id = $row['PON_ONU_ID'];
		$olt_name = $row['OLT_NAME'];
		$onu_ports = $row['ONU_PORTS'];
		$ro = $row['RO'];
		$rw = $row['RW'];
		$rf = $row['RF'];
		$pse = $row['PSE'];
		$olt_type = $row['TYPE'];
		$name = $row['NAME'];
	}
}
print "<center><h2>OLT " . $olt_name . " >> " . $name . " " . $slot_id . "/" . $port_id . "/" . $pon_onu_id . " Statistics</center></h2>";
print "<center><table border=1 cellpadding=1 cellspacing=1><tr align=center style=font-weight:bold><td>UNI</td><td>Admin</td><td>Link</td><td>Flow Control</td><td>Speed/Duplex</td><td>Auto-Neg</td></tr>";
$index = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000;
for ($i = 1; $i <= $onu_ports ; $i++) {
	$gindex = $index + $i;
	$port_link_oid = "1.3.6.1.4.1.8886.18.2.6.3.1.1.2." . $gindex;
	$port_admin_oid = "1.3.6.1.4.1.8886.18.2.6.3.1.1.3." . $gindex;
	$port_autong_oid = "1.3.6.1.4.1.8886.18.2.6.3.1.1.5." . $gindex;
	$port_flowctrl_oid = "1.3.6.1.4.1.8886.18.2.6.3.1.1.10." . $gindex;
	$port_speed_duplex_oid = "1.3.6.1.4.1.8886.18.2.6.3.2.1.2." . $gindex;
	
	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	$session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro);
	$port_admin = $session->get($port_admin_oid);
	$port_link = $session->get($port_link_oid);
	$port_autong = $session->get($port_autong_oid);
	$port_flowctrl = $session->get($port_flowctrl_oid);
	$port_speed_duplex = $session->get($port_speed_duplex_oid);
	if ($port_admin == '1') {
                $port_admin = "<font color=red>Disabled</font>";
        } else if ($port_admin == '2') {
                $port_admin = "<font color=green>Enabled</font>";
        } else if ($port_admin == '0') {
                $port_admin = "Unknown";
        }
	
	if ($port_link == '1') {
		$port_link = "<font color=red>Down</font>";
	} else if ($port_link == '2') {
		$port_link = "<font color=green>Up</font>";
	} else if ($port_link == '0') {
		$port_link = "Unknown";
	}
	if ($port_autong == '1') {
		$port_autong = "<font color=red>Disabled</font>";
	} else if ($port_autong == '2') {
		$port_autong = "<font color=green>Enabled</font>";
	} else if ($port_autong == '0') {
		$port_autong = "Unknown";
	}
	if ($port_flowctrl == '1') {
		$port_flowctrl = "<font color=red>Disabled</font>";
	} else if ($port_flowctrl == '2') {
		$port_flowctrl = "<font color=green>Enabled</font>";
	} else if ($port_flowctrl == '0') {
		$port_flowctrl = "Unknown";
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
	print "<tr  align=center><td>" . $i . "</td><td>" . $port_admin . "</td><td>" . $port_link .  "</td><td>" . $port_flowctrl . "</td><td>" . $port_speed_duplex . "</td><td>" . $port_autong . "</td></tr>"; 
}
print "</table>";
print "<BR><BR><form action=\"onu_details.php\" method=\"post\">";
print "<input type=\"hidden\" name=\"customer_id\" value=\"". $customer_id ."\">";
print "<center></center>";
if ($rf == "1") {
	$index = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + 162;
	$rf_state_oid = "1.3.6.1.4.1.8886.18.2.6.21.3.1.2." . $index;
	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	$session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro);
	$rf_state = $session->get($rf_state_oid);
	if ($rf_state == "1") {
		$checked = " checked";
		print "RF: <input type=\"checkbox\" name=\"rf_status\" value=\"1\"" . $checked . ">";
	} else {
		print "RF: <input type=\"checkbox\" name=\"rf_status\" value=\"1\">";
	}
	print "<p><input type='submit' name='SUBMIT' value='SET'></p>";

}

print "<p><input type='submit' name='SUBMIT' value='Reboot ONU'></p>";
print "</form>";

$index = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id;
$version_oid = "1.3.6.1.4.1.8886.18.2.6.1.1.1.6." . $index;
$firmware_oid = "1.3.6.1.4.1.8886.18.2.6.1.1.1.7." . $index;
$device_type_oid = "1.3.6.1.4.1.8886.18.2.6.1.1.1.12." . $index;
snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
$session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro);
$device_type = $session->get($device_type_oid);
$version = $session->get($version_oid);
$firmware = $session->get($firmware_oid);
print "<br>";
print "Device Type: " . $device_type . "<BR><br>";
print "Software version: " . $version . "<BR><br>";
print "Firmware version: " . $firmware;


?>

