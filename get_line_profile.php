<?php
include ("dbconnect.php");

$get = $_GET['choice'];
try {
	$result2 = $db->query("SELECT LINE_PROFILE.NAME as LINE_PROFILE_NAME, LINE_PROFILE.ID as ID, LINE_PROFILE.LINE_PROFILE_ID from LINE_PROFILE where OLT='$get' order by LINE_PROFILE_ID");
} catch (PDOException $e) {
	echo "Connection Failed:" . $e->getMessage() . "\n";
	exit;
}

while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
	echo "<option value=\"" . $row2{'ID'} ."\">" . $row2{'LINE_PROFILE_NAME'} ." === ". $row2{'LINE_PROFILE_ID'} ."</option>";
}

?>
