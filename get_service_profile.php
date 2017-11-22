<?php
include("dbconnect.php");


$get = $_GET['choice'];
$olt = $_GET['olt'];
try {
	$result2 = $db->query("SELECT ONU.ID, SERVICE_PROFILE.NAME as SERVICE_PROFILE_NAME, SERVICE_PROFILE.ID as SP_ID, SERVICE_PROFILE_ID from ONU LEFT JOIN SERVICE_PROFILE on ONU.PORTS=SERVICE_PROFILE.PORTS where ONU.ID='$get' AND SERVICE_PROFILE.OLT = '$olt'");
} catch (PDOException $e) {
	echo "Connection Failed:" . $e->getMessage() . "\n";
	exit;
}
while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
	echo "<option value=\"" . $row2{'SP_ID'} ."\">" . $row2{'SERVICE_PROFILE_NAME'} ." === " . $row2{'SERVICE_PROFILE_ID'} ."</option>";
}

?>
