<?php

include ("common.php");
include ("dbconnect.php");
include ("navigation.php");
if ($user_class < "9")
	exit();
/*** begin our session ***/


/*** set a form token ***/
$form_token = md5( uniqid('auth', true) );

/*** set the session form token ***/
$_SESSION['form_token'] = $form_token;
?>

<html>
<body>
<center>
<h2>User Accounts</h2>
<?php
try {
		$result = $db->query("SELECT ID, USERNAME, TYPE from ACCOUNTS");
	} catch (PDOException $e) {
		echo "Connection Failed:" . $e->getMessage() . "\n";
		exit;
	}
	print "<table border=1 cellspacing=0>";
	print "<tr><td>ID</td><td>Username</td><td>Type</td></tr>";
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		if ($row['TYPE'] == '9')
			$type = "Admin";
		if ($row['TYPE'] == '6')
			$type = "Operator";
		if ($row['TYPE'] == '3')
			$type = "Visitor";
		print "<tr><td><a href=\"accounts.php?edit=1&id=".$row{'ID'}."\">" . $row['ID'] . "</a></td><td>" . $row['USERNAME'] . "</td><td>" . $type . "</td></tr>";
	}
	print "</table>";

if ($_GET) {
	$account_id = $_GET['id'];
    if (!preg_match('/^[0-9]*$/', $account_id)) {
		print "that sux";
		exit;
	} else {
		try {
			$result = $db->query("SELECT USERNAME, TYPE from ACCOUNTS where ID='$account_id'");
        } catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
        }
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$username = $row["USERNAME"];
			$type = $row["TYPE"];
		}
		
		
	}
}


?>






<form action="adduser_submit.php" method="post">
<p>
<table>
<tr><td>Username:</td>
<td>
<?php
if(isset($username)) {
	print "<input type=\"hidden\" name=\"username\" value=\"". $username ."\">";
	print  $username ;
} else {
	print "<input type=\"text\" id=\"username\" name=\"username\" value=\"\" maxlength=\"20\" />";	
}
?>
</td></tr>
</p>
<p>
<tr><td>Password:</td>
<td><input type="password" id="password" name="password" value="" maxlength="20" /></td>
</p>
</tr></table>
<p>
<?php
if (isset($account_id)) 
	print "<input type=\"hidden\" name=\"account_id\" value=\"". $account_id ."\">";
print "<select id=\"select-role\" name=\"type\">";
print "<option value=\"\">---</option>";
if ($type == "9") {
	print "<option value=\"9\" selected>Admin</option>";
}else{
	print "<option value=\"9\">Admin</option>";
}
if ($type == "6") {
	print "<option value=\"6\" selected>Operator</option>";
}else{
	print "<option value=\"6\">Operator</option>";
}
if ($type == "3") {
	print "<option value=\"3\" selected>Visitor</option>";
}else{
	print "<option value=\"3\">Visitor</option>";
}
print "</select>";
?>
</p>
<p>
<input type="hidden" name="form_token" value="<?php echo $form_token; ?>" />
<?php
if (isset($_GET["edit"])) {
	$edit = $_GET["edit"];
}else{
	$edit = "";
}
if ($edit == "1" || isset($account_id)) {
	print "<input type='submit' name='SUBMIT' value='EDIT'>";
	print "&nbsp;&nbsp;&nbsp;<input type='submit' name='SUBMIT' value='DELETE'>";
}else{
	print "<input type='submit' name='SUBMIT' value='ADD'>";
}

?>


</p>
</form>
</body>
</html>
