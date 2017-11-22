<?php
include ("common.php");
include ("dbconnect.php");
navigation();

$nameErr = $oltErr = $slot_idErr = $pon_idErr = "";
$name = $olt = $ip_address = $ro = "";
$array = array();
$array2 = array();

print "<center><form action=\"parse.php\" method=\"post\">OLT*:<select id=\"select-olt\" name=\"olt\">";
try {
$result = $db->query("SELECT * from OLT");
} catch (PDOException $e) {
        echo "Connection Failed:" . $e->getMessage() . "\n";
        exit;
}

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        if($olt == $row{'ID'}) {
        print "<option value=\"" . $row{'ID'} ."\" selected>" . $row{'NAME'} . "</option>";
        } else {
        print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
        }
}


print "</select>";
print "<input type='submit' name='SUBMIT' value='GET'></center><br>";
print "</form>";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (empty($_POST["olt"])) {
	$oltErr = "OLT is required";
	} else {
	$olt = test_input($_POST["olt"]);
	}
	if ($_POST["SUBMIT"]) {
        $submit = test_input($_POST["SUBMIT"]);
        }

	try {
	$result = $db->query("SELECT NAME, INET_NTOA(IP_ADDRESS) as IP_ADDRESS, RO, RW from OLT where ID='$olt'");
	} catch (PDOException $e) {
		echo "Connection Failed:" . $e->getMessage() . "\n";
		exit;
	}
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$pon_name = $row['NAME'];
		$ip_address = $row['IP_ADDRESS'];
		$ro = $row['RO'];
		$rw = $row['RW'];
		}


        $onuid_oid = "iso.3.6.1.4.1.8886.18.2.1.3.1.1.1";
	$first_oid = "iso.3.6.1.4.1.8886.18.2.1.3.1.1.2.";
        $second_oid = "iso.3.6.1.4.1.8886.18.2.1.3.1.1.3.";
        $forth_oid = "iso.3.6.1.4.1.8886.18.2.1.3.1.1.10.";

        snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
        $session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro, 900000, 3);
	$walk_id = $session->walk($onuid_oid);
	print "<center><table border=1 cellpadding=1 cellspacing=1><tr align=center style=font-weight:bold><td>PON_ID</td><td>ONU_ID</td><td>MAC</td><td>ONU_TYPE</td><td>State</td></tr>";
	foreach ($walk_id as $onu_id) {
		$mac_address_oid = $first_oid . $onu_id;
		$device_type_oid = $second_oid . $onu_id;
		$state_oid = $forth_oid . $onu_id;
        	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	        $session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro, 900000, 3);
                $device_type = $session->get($device_type_oid);
		try {
                	$result = $db->query("SELECT ID from ONU where DTYPE = '$device_type'");
                } catch (PDOException $e) {
               		echo "Connection Failed:" . $e->getMessage() . "\n";
                        exit;
           	}
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$device_type = $row['ID'];	
		}

		array_push($array, $device_type);
                $state = $session->get($state_oid);
		array_push($array, $state);
		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
	        $session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro, 900000, 3);
                $mac_address = $session->get($mac_address_oid);
		$mac_address = str_replace('Hex-STRING: ', '', $mac_address);
		$mac_address = str_replace(' ', '', $mac_address);
		array_push($array, $mac_address);
		preg_match('/^(\d)(\d{2})\d{3}(\d{2})/', $onu_id, $match);
		$slot_id = $match['1'];
		$port_id = preg_replace('/^0/', '',$match['2']);
		try {
                	$result = $db->query("SELECT ID from PON where SLOT_ID = '$slot_id' AND PORT_ID = '$port_id' AND OLT= '$olt'");
                } catch (PDOException $e) {
                        echo "Connection Failed:" . $e->getMessage() . "\n";
                        exit;
                }
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $pon_id = $row['ID'];
                }

		array_push($array, $pon_id);
		$pon_onu_id =  preg_replace('/^0/', '',$match['3']);
		array_push($array, $pon_onu_id);
		print "<tr align=center><td>" . $pon_id .  "</td><td>" . $pon_onu_id . "</td><td>" . $mac_address . "</td><td>" . $device_type . "</td><td>" . $state . "</td></tr>";
	
		array_push($array, $onu_id);	
		array_push($array2, $array);	
		$array = array();
	}
        print "</table>";	
	print "<BR><BR>";	
//	print_r($array2);

	
	print "<center><form action=\"parse.php\" method=\"post\">";
        print "<input type=\"hidden\" name=\"olt\" value=\"". $olt ."\">";
	print "<input type='submit' name='SUBMIT' value='ADD'></center><br>";
	if ($submit == "ADD") {


		foreach($array2 as $array1) {
			try {
			        $result = $db->query("SELECT MAC_ADDRESS from CUSTOMERS where MAC_ADDRESS = x'$array1[2]'");
 			} catch (PDOException $e) {
				echo "Connection Failed:" . $e->getMessage() . "\n";
				exit;
           		}
        		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        			$mac_address_match = $row["MAC_ADDRESS"] ;
			}

			if ($mac_address_match) {
				 print "ONU " . $array1[2] . " DUPLICATE MAC_ADDRESS<BR>";
//				 print_r($array1);
			} else {
				try {
       					$result = $db->query("INSERT INTO CUSTOMERS ( ONU_MODEL, OLT, PON_PORT, PON_ONU_ID, MAC_ADDRESS, STATE) VALUES ('$array1[0]', '$olt', '$array1[3]', '$array1[4]', x'$array1[2]', '$array1[1]')");
				} catch (PDOException $e) {
					echo "Connection Failed:" . $e->getMessage() . "\n";
					exit;
				}
				//CREATE RRD
				$traffic = array("traffic", "unicast", "broadcast", "multicast");
				foreach ($traffic as $tr) {
					$rrd_name = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $array1[5] . "_" . $tr . ".rrd";
					$opts = array( "--step", "300", "--start", 0,
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
						echo "$err";
					}
				}


				// POWER RRD
           			$rrd_name = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $array1[5] . "_power.rrd";
                		$opts = array( "--step", "300", "--start", 0,
					   "DS:input:GAUGE:600:U:U",
					   "DS:output:GAUGE:600:U:U",
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
					echo "$err";
				}

				print "ONU " . $array1[2] . "Added Succesfully! <BR>";
			}
        		
		}
	}
}
?>
