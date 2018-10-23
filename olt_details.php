<?php
include ("header.php");
include ("common.php");
include ("dbconnect.php");
include ("navigation.php");


if ($_GET) {
	$olt_id = $_GET['id'];
	if (!preg_match('/^[0-9]*$/', $olt_id)) {
		print "that sux";
		exit;
	} else {
		try {
			$result = $db->query("SELECT OLT.ID, OLT.NAME as OLT_NAME, MODEL, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE from OLT LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID where OLT.ID = '$olt_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
	}
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$ip_address = $row['IP_ADDRESS'];
		$olt_name = $row['OLT_NAME'];
		$ro = $row['RO'];
		$rw = $row['RW'];
		$olt_type = $row['TYPE'];

	}
}




print "<div class=\"container\"><div class=\"text-center\"><div class=\"page-header\"><h2>" . $olt_name . " >> " . $ip_address . "  Statistics</h2></div></div>";
print "<div class=\"row justify-content-md-center\"><div class=\"col-md-4 \">";
print "<nav id=\"navbar2\" class=\"navbar\"><ul class=\"nav nav-tabs\">";

print "<li class=\"nav-item active\"><a onClick=\"getOltPage('". $olt_id . "', 'info');\">INFO</a></li>";	
print "<li class=\"nav-item\"><a onClick=\"getOltPage('". $olt_id . "', 'ports');\">PORTS</a></li>";	
print "<li class=\"nav-item\"><a onClick=\"getOltPage('". $olt_id . "', 'graphs');\">GRAPHS</a></li>";	
print "</ul></nav>";
print "</div></div>";
print "<br>";


print "<div id=\"output\">";
print "<body onload=\"getOltPage('". $olt_id . "', 'info');\">";
print "</div></div>";

 
?>

