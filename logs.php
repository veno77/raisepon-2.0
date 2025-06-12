<?php
include ("header.php");
include ("common.php");
include ("dbconnect.php");
include ("navigation.php");
$page = $_SERVER['PHP_SELF'];
$sec = "15";
header("Refresh: $sec; url=$page");


print "<h2><center>Logs from OLTs<center></h2>";

$my_file = "/var/log/raisepon/raisepon.log";
$lines = file($my_file);
$lines=str_replace("^M","",$lines);
$ii = "0";
for ($i = count($lines) - 1; $i >= 0; $i--) {
//	if (!preg_match("/gpon-onu-remote/",$lines[$i]))
		echo $lines[$i] . '<br/>';
	$ii++;
	if ($ii > 100)
	exit();
}



?>
