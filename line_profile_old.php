<?php
include ("common.php");
include ("dbconnect.php");
include ("navigation.php");
//navigation();
if ($user_class < "9")
	exit();

$nameErr = $line_profile_idErr = "";
$name = $line_profile_id = $t_id = "";

print "<p><center>LINE PROFILE Configuration</p>";


if (!($_SERVER["REQUEST_METHOD"] == "POST")&&!($_GET)) {
	print "<form action=\"line_profile.php\" method=\"post\">";
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
		if (isset($_POST["t_id"])) {
			$t_id = test_input($_POST["t_id"]);
		}
		if ($_POST["line_profile_id"]) {
			$line_profile_id = test_input($_POST["line_profile_id"]);
		}
		if (empty($_POST["name"])) {
			$nameErr = "Name is required";
		} else {
			$name = test_input($_POST["name"]);
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
	
	print "<form action=\"line_profile.php\" method=\"post\">";
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
	// ADD LINE PROFILE
	if ($name !== '' && $line_profile_id !== '' && $submit == "ADD") {
		try {
			$result = $db->query("INSERT INTO LINE_PROFILE (NAME, OLT, LINE_PROFILE_ID) VALUES ('$name', '$olt', '$line_profile_id')");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
        	exit;
		}
		exit("LINE PROFILE added Succesfully");
	}
	
	// EDIT LINE PROFILE
	if ($line_profile_id !== '' && $name !== '' && $submit == "EDIT") {
		// UPDATE TEMPALTES
		try {
			$result = $db->query("UPDATE LINE_PROFILE SET NAME = '$name', LINE_PROFILE_ID = '$line_profile_id' where ID = '$t_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
        	exit;
		}
		exit("LINE PROFILE Edited Succesfully");
	}
	// DELETE LINE PROFILE
	if ($t_id !== '' && $submit == "DELETE") {
		// CHECK IF PROFILE IS ASSIGNED TO ANY CUSTOMER
		try {
			$result = $db->query("SELECT LINE_PROFILE from CUSTOMERS where LINE_PROFILE =  '$t_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["LINE_PROFILE"])
				exit (" ERROR: LINE PROFILE IS ASSIGNED TO CUSTOMERS, Please remove LINE PROFILE from customers before Deletng it!");
		}
		
		try {
			$result = $db->query("DELETE FROM LINE_PROFILE where ID='$t_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		exit("LINE PROFILE Deleted Succesfully");
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
			$result = $db->query("SELECT ID, NAME, LINE_PROFILE_ID from LINE_PROFILE where ID='$t_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$t_id = $row["ID"];
			$name = $row["NAME"];
			$line_profile_id = $row["LINE_PROFILE_ID"];
		}
	}

	print "<form action=\"line_profile.php\" method=\"post\">";
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
		$result = $db->query("SELECT ID, NAME, LINE_PROFILE_ID from LINE_PROFILE where OLT='$olt'");
	} catch (PDOException $e) {
		echo "Connection Failed:" . $e->getMessage() . "\n";
        exit;
	}
	
	print "<table border=1 cellpadding=1 cellspacing=1><tr align=center style=font-weight:bold><td>ID</td><td>NAME</td><td>LINE_PROFILE_ID</td></tr>";
	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		print "<tr align=right><td><a href='line_profile.php?edit=1&id=". $row{'ID'} . "&olt=" . $olt . "'>" . $row{'ID'} . "</a></td><td>" . $row{'NAME'} . "</td><td>" . $row{'LINE_PROFILE_ID'} . "</td></tr>";
	}
	print "</table>";
	print "<form action=\"line_profile.php\" method=\"post\">";
	print "<p><table>";
	if (isset($_GET["edit"])) {
		$edit = $_GET["edit"];
	} else {
		$edit = NULL;
	}

	if ($edit == "1") {
		print "<tr><td>LINE PROFILE ID*:</td><td>" . $line_profile_id . "</td>";
		print "<input type=\"hidden\" name=\"line_profile_id\" value=\"". $line_profile_id ."\">";
		print "<input type=\"hidden\" name=\"t_id\" value=\"". $t_id ."\">";
	} else {
		print "<tr><td>Line Profile ID*:</td><td><input type=\"text\" name=\"line_profile_id\"";
		if($line_profile_id)
			print "value=\"".$line_profile_id ."\"></td>";
	}
	if($line_profile_idErr != "") 
		print "<td style=\"color:red\">" . $line_profile_idErr . "</td>";
	print "</tr>";
	print "<tr><td>Name*:</td><td><input type=\"text\" name=\"name\"";
	if($name) 
		print "value=\"".$name ."\"";
	print "></td>";
	if($nameErr != "") 
		print "<td style=\"color:red\">" . $nameErr . "</td>";
	print "</tr></table></p>";

	if ($edit == "1" || $t_id) {
		print "<input type='submit' name='SUBMIT' value='EDIT'>";
		print "&nbsp;&nbsp;&nbsp;<input type='submit' name='SUBMIT' value='DELETE'>";
	}else{
		print "<input type='submit' name='SUBMIT' value='ADD'>";
	}
	print "</form>";
}
?>

