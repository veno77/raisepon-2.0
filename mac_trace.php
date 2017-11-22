<?php
include ("common.php");
include("dbconnect.php");
include ("navigation.php");

print "<center><form action=\"mac_trace.php\" method=\"post\">";
print "<table><tr>";
$macErr = "";
$onu_id = $mac_address =  "";
print "<td>MAC*:</td><td><input type=\"text\" name=\"mac_address\"  maxlength=\"17\" size=\"17\" value=\"" . $mac_address ."\"></td>";
print "<td><input type='submit' name='SUBMIT' value='TRACE'></td>";
if($macErr != "") 
	print "<td style=\"color:red\">" . $macErr . "</td>";
print "</tr></table>";
print "</form><BR>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (empty($_POST["mac_address"])) {
	    $macErr = "MAC Address is required";
	} else {
	    $mac_address = test_input($_POST["mac_address"]);
		if(!preg_match('/([a-fA-F0-9]{2}[:|\-]?){6}/', $mac_address))
			$macErr = "Mac Address Format is like AA:BB:CC:DD:EE:FF";
	}
	$separator = array(':', '-', '.');
	$stripped_mac = str_replace($separator, '', $mac_address);
	$mac_array = str_split($stripped_mac,2);
	$mac_address = '';
	foreach ($mac_array as $mac) {
		$mac = hexdec($mac);
		$mac_address = $mac_address . "." . $mac;
	}
	print $mac_address;
	//SNMP 
	try {
		$result = $db->query("SELECT OLT.NAME, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, RO, RW, TYPE from OLT LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID");
	} catch (PDOException $e) {
		echo "Connection Failed:" . $e->getMessage() . "\n";
		exit;
	}
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$ip_address = $row{'IP_ADDRESS'};
		$status_oid = "1.3.6.1.2.1.1.3.0";
		
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'}, 1000000);
		$status = $session->get($status_oid);
		if ($status) {
			print "<h2>OLT: " . $row{'NAME'} . "::" . $ip_address . "</h2>";
			$oid = "1.3.6.1.4.1.8886.18.5.2.2.1.8" . $mac_address ;
			$oid_2 = "1.3.6.1.4.1.8886.18.5.2.2.1.2" . $mac_address ;
			$oid_3 = "1.3.6.1.4.1.8886.18.5.2.2.1.3" . $mac_address ;
			$oid_4 = "1.3.6.1.4.1.8886.18.5.2.2.1.4" . $mac_address ;
			$oid_5 = "1.3.6.1.4.1.8886.18.5.2.2.1.5" . $mac_address ;
			$session = new SNMP(SNMP::VERSION_2C, $ip_address, $row{'RW'}, 1000000);
	        $session->set($oid, 'i', '4');
			if ($session->getError())
				exit(var_dump($session->getError()));
			snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	        $session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
	        $port_id = $session->get($oid_2);
			if ($port_id == '0') {
				print "Not Found";
			} else {
				$port_type = $session->get($oid_3);
				if ($port_type == '2') {
					$port_name = "GE";
				}
				if ($port_type == '3')
					$port_name = "PON";
				if ($port_type == '4')
	                		$port_name = "TRUNK";
				$port_id = decbin($port_id);
				$slot = bindec(substr($port_id, 0, -6));
				$port = bindec(substr($port_id, -6));
				$mac_addr_type = $session->get($oid_5);
				if ($mac_addr_type == '2')
					$mac_addr_type = "dynamic";
				if ($mac_addr_type == '3')
	                		$mac_addr_type = "static";
				print "<table>";	
				print "<tr><td>1. Tracing OLT</td><td>:: Found specified MAC from " . $port_name . " " . $slot . "/" . $port . ", " . $mac_addr_type . "</td></tr>";
				if ($port_type == '3') {
					$onu_id = $session->get($oid_4);
					print "<tr><td>2. Tracing PON " . $slot . "/" . $port . "</td><td>:: Found specified MAC from ONU " . $onu_id . "</td></tr>";	
			//		$oid_6 = "1.3.6.1.4.1.8886.18.3.6.14.5.1.2." . $slot . str_pad($port, 2, '0', STR_PAD_LEFT) . str_pad($onu_id, 5, '0', STR_PAD_LEFT) . $mac_address;
			//		$onu_port_id = $session->get($oid_6);
			//		print "<tr><td>3. Tracing ONU " . $slot . "/" . $port . "/" . $onu_id . "</td><td>:: Found specified MAC on ONU_Ethernet_Port: " . $onu_port_id . "</td></tr>";	
				}

			}
			
			$session = new SNMP(SNMP::VERSION_2C, $ip_address, $row{'RW'}, 1000000);
	        $session->set($oid, 'i', '6');
		}
	}
}


?>
