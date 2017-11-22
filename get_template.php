<?php
include("dbconnect.php");


$get = $_GET['choice'];
$olt = $_GET['olt'];
try {
	$result2 = $db->query("SELECT ONU.ID, SVR_TEMPLATE.NAME, SVR_TEMPLATE.ID as SVR_ID from ONU LEFT JOIN SVR_TEMPLATE on ONU.PORTS=SVR_TEMPLATE.PORTS where ONU.ID='$get' AND SVR_TEMPLATE.OLT = '$olt'");
} catch (PDOException $e) {
	echo "Connection Failed:" . $e->getMessage() . "\n";
	exit;
}
echo "<option value=\"0\">---------</option>";
while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
	echo "<option value=\"" . $row2{'SVR_ID'} ."\">" . $row2{'NAME'} ."</option>";
}

?>
