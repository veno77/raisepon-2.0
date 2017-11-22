<?php
$mysql_user = "root";
$mysql_pass = "blahsux12321";
global $db;
try {
	$db = new PDO('mysql:host=localhost;dbname=gpon;charset=utf8', $mysql_user, $mysql_pass);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo 'Connection Failed: ' . $e->getMessage();
	exit;
}
?>
