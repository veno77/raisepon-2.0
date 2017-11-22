<?php
include ("common.php");
include ("dbconnect.php");
navigation();
if ($user_class < "9")
	exit();

$nameErr = $portsErr = $template_idErr = "";
$name = $ports = $template_id = $t_id = "";

print "<p><center>SVR TEMPLATE Configuration</p>";


if (!($_SERVER["REQUEST_METHOD"] == "POST")&&!($_GET)) {
	print "<form action=\"templates.php\" method=\"post\">";
	print "OLT*:<select id=\"select-olt\" name=\"olt\">";
	try {
		$result = $db->query("SELECT * from OLT");
	} catch (PDOException $e) {
		echo "Connection Failed:" . $e->getMessage() . "\n";
		exit;
	}
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		if($olt == $row{'ID'}) {
			print "<option value=\"" . $row{'ID'} ."\" selected>" . $row{'NAME'} . "</option>";
		} else {
			print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
        }
	}
	print '</select>';
	print "<input type='submit' name='SUBMIT' value='GET'><br><br>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($_POST["SUBMIT"]!="GET") {
		if ($_POST["t_id"]) {
			$t_id = test_input($_POST["t_id"]);
		}
		if ($_POST["template_id"]) {
			$template_id = test_input($_POST["template_id"]);
		}
		if (empty($_POST["name"])) {
			$nameErr = "Name is required";
		} else {
			$name = test_input($_POST["name"]);
		}
		if (empty($_POST["ports"])) {
			$portsErr = "Number of Ports Required!";
		} else {
			$ports = test_input($_POST["ports"]);
		}
	}
    if (empty($_POST["olt"])) {
		$oltErr = "OLT is required";
	} else {
		$olt = test_input($_POST["olt"]);
	}
	
	if ($_POST["SUBMIT"]) {
		$submit = test_input($_POST["SUBMIT"]);
	}
	
	print "<form action=\"templates.php\" method=\"post\">";
	print "OLT*:<select id=\"select-olt\" name=\"olt\">";
	
	try {
		$result = $db->query("SELECT * from OLT");
	} catch (PDOException $e) {
        echo "Connection Failed:" . $e->getMessage() . "\n";
        exit;
	}
	
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		if($olt == $row{'ID'}) {
			print "<option value=\"" . $row{'ID'} ."\" selected>" . $row{'NAME'} . "</option>";
        } else {
        	print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
        }
	}

	print '</select>';
	print "<input type='submit' name='SUBMIT' value='GET'><br><br>";
	// ADD TEMPLATE
	if ($name !== '' && $ports !== ''  && $template_id !== '' && $submit == "ADD") {
		try {
			$result = $db->query("INSERT INTO SVR_TEMPLATE (NAME, PORTS, OLT, TEMPLATE_ID) VALUES ('$name', '$ports', '$olt', '$template_id')");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
        	exit;
		}
		exit("SVR Template added Succesfully");
	}
	
	// EDIT SVR TEMPLATE
	if ($template_id !== '' && $name !== '' && $ports !== '' && $submit == "EDIT") {
		// UPDATE TEMPALTES
		try {
			$result = $db->query("UPDATE SVR_TEMPLATE SET NAME = '$name', PORTS = '$ports', TEMPLATE_ID = '$template_id' where ID = '$t_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
        	exit;
		}
		exit("SVR Template Edited Succesfully");
	}
	// DELETE SVR TEMPLATE
	if ($t_id !== '' && $submit == "DELETE") {
		// CHECK IF TEMPLATE IS ASSIGNED TO ANY CUSTOMER
		try {
			$result = $db->query("SELECT SVR_TEMPLATE from CUSTOMERS where SVR_TEMPLATE =  '$t_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["SVR_TEMPLATE"])
				exit ("SVR TEMPLATE IS ASSIGNED TO CUSTOMERS, Please remove SVR_TEMPLATES from customers to Delete it!");
		}
		
		try {
			$result = $db->query("DELETE FROM SVR_TEMPLATE where ID='$t_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		exit("SVR TEMPLATE Deleted Succesfully");
	}
	//END OF POST METHOD
}

// GET METHOD
if ($_GET) {
	$olt = $_GET['olt'];
	$t_id = $_GET['id'];
	if (!preg_match('/^[0-9]*$/', $t_id)) {
		print "that sux";
        exit;
	} else {
		try {
			$result = $db->query("SELECT ID, NAME, PORTS, TEMPLATE_ID from SVR_TEMPLATE where ID='$t_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$t_id = $row["ID"];
			$name = $row["NAME"];
			$ports = $row["PORTS"];
			$template_id = $row["TEMPLATE_ID"];
		}
	}

	print "<form action=\"templates.php\" method=\"post\">";
	print "OLT*:<select id=\"select-olt\" name=\"olt\">";
	try {
		$result = $db->query("SELECT * from OLT");
	} catch (PDOException $e) {
		echo "Connection Failed:" . $e->getMessage() . "\n";
		exit;
	}
	
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		if($olt == $row{'ID'}) {
			print "<option value=\"" . $row{'ID'} ."\" selected>" . $row{'NAME'} . "</option>";
		} else {
			print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
		}
	}

	print '</select>';
	print "<input type='submit' name='SUBMIT' value='GET'><br><br>";
}

	
if (($_SERVER["REQUEST_METHOD"] == "POST")||($_GET)) {
	try {
		$result = $db->query("SELECT ID, NAME, PORTS, TEMPLATE_ID from SVR_TEMPLATE where OLT='$olt'");
	} catch (PDOException $e) {
		echo "Connection Failed:" . $e->getMessage() . "\n";
        exit;
	}
	
	print "<table border=1 cellpadding=1 cellspacing=1><tr align=center style=font-weight:bold><td>ID</td><td>NAME</td><td>PORTS</td><td>TEMPLATES_ID</td></tr>";
	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		print "<tr align=right><td><a href='templates.php?edit=1&id=". $row{'ID'} . "&olt=" . $olt . "'>" . $row{'ID'} . "</a></td><td>" . $row{'NAME'} . "</td><td>" . $row{'PORTS'} . "</td><td>" . $row{'TEMPLATE_ID'} . "</td></tr>";
	}
	print "</table>";
	print "<form action=\"templates.php\" method=\"post\">";
	print "<p><table>";
	if ($_GET["edit"] == "1") {
		print "<tr><td>Template ID*:</td><td>" . $template_id . "</td>";
		print "<input type=\"hidden\" name=\"template_id\" value=\"". $template_id ."\">";
		print "<input type=\"hidden\" name=\"t_id\" value=\"". $t_id ."\">";
	} else {
		print "<tr><td>Template ID*:</td><td><input type=\"text\" name=\"template_id\"";
		if($template_id)
			print "value=\"".$template_id ."\"></td>";
	}
	if($template_idErr != "") 
		print "<td style=\"color:red\">" . $template_idErr . "</td>";
	print "</tr>";
	print "<tr><td>Name*:</td><td><input type=\"text\" name=\"name\"";
	if($name) 
		print "value=\"".$name ."\"";
	print "></td>";
	if($nameErr != "") 
		print "<td style=\"color:red\">" . $nameErr . "</td>";
	print "</tr><tr><td>Ports*:</td><td><input type=\"text\" name=\"ports\"";
	if($ports) 
		print "value=\"".$ports ."\"";
	print "></td>";
	if($portsErr != "") 
		print "<td style=\"color:red\">" . $portsErr . "</td>";
	print "</tr></table></p>";
	if ($_GET["edit"] == "1" || $t_id) {
		print "<input type='submit' name='SUBMIT' value='EDIT'>";
		print "&nbsp;&nbsp;&nbsp;<input type='submit' name='SUBMIT' value='DELETE'>";
	}else{
		print "<input type='submit' name='SUBMIT' value='ADD'>";
	}
	print "</form>";
}
?>

