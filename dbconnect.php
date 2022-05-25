<?php
$mysql_user = "raisepon";
$mysql_pass = "r41sepon";
global $db;
try {
	$db = new PDO('mysql:host=localhost;dbname=raisepon-dev;charset=utf8', $mysql_user, $mysql_pass);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo 'Connection Failed: ' . $e->getMessage();
	exit;
}
?>
