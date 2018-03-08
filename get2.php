<?php
include ("dbconnect.php");

$get = $_GET['choice'];
try {
	$result2 = $db->query("SELECT ID, NAME, SLOT_ID, PORT_ID from PON where OLT='$get' order by SLOT_ID, PORT_ID");
} catch (PDOException $e) {
	echo "Connection Failed:" . $e->getMessage() . "\n";
	exit;
}

while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
	echo "<option value=\"" . $row2{'ID'} ."\">" . $row2{'NAME'} ." === ". $row2{'SLOT_ID'} ."/". $row2{'PORT_ID'} ."</option>";
}

?>
