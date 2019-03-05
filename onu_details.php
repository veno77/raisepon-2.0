<?php
include ("header.php");
include ("common.php");
include ("dbconnect.php");
include ("navigation.php");


if ($_GET) {
	$customer_id = $_GET['id'];
	if (!preg_match('/^[0-9]*$/', $customer_id)) {
		print "that sux";
		exit;
	} else {
		try {
			$result = $db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME as NAME, SN, PON_ONU_ID, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, CUSTOMERS.SERVICE, OLT.ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.NAME as OLT_NAME, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, SERVICES.ID from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID where CUSTOMERS.ID = '$customer_id'");
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
		$ro = $row['RO'];
		$rw = $row['RW'];
		$olt_type = $row['TYPE'];
		$name = $row['NAME'];
		$sn = $row['SN'];
	}
}




print "<div class=\"container\"><div class=\"text-center\"><div class=\"page-header\"><h2>" . $olt_name . " >> " . $name . " " . $slot_id . "/" . $port_id . "/" . $pon_onu_id . " SN::" . $sn . " Statistics</h2></div></div>";
print "<div class=\"row justify-content-md-center\"><div class=\"col-md-4 \">";
print "<nav id=\"navbar2\" class=\"navbar\"><ul class=\"nav nav-tabs\">";

print "<li class=\"nav-item active\"><a onClick=\"getPage('". $customer_id . "', 'info');\">INFO</a></li>";	
print "<li class=\"nav-item\"><a onClick=\"getPage('". $customer_id . "', 'ports');\">PORTS</a></li>";	
print "<li class=\"nav-item\"><a onClick=\"getPage('". $customer_id . "', 'graphs');\">GRAPHS</a></li>";	
print "<li class=\"nav-item\"><a onClick=\"getPage('". $customer_id . "', 'history');\">HISTORY</a></li>";	
print "</ul></nav>";
print "</div></div>";
print "<br>";


print "<div id=\"output\">";
print "<body onload=\"getPage('". $customer_id . "', 'info');\">";
print "</div></div>";

 
?>

