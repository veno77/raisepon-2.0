<?php
include ("common.php");
include ("classes/service_profile_class.php");
include ("navigation.php");
if ($user_class < "9")
	exit();

$nameErr = $portsErr = $service_profile_idErr = "";
$name = $ports =  $service_profile_id = $t_id = "";

print "<p><center>SERVICE PROFILE Configuration</p>";


if (!($_SERVER["REQUEST_METHOD"] == "POST")&&!($_GET)) {
	print "<form action=\"service_profile.php\" method=\"post\">";
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
		if ($_POST["service_profile_id"]) {
			$service_profile_id = test_input($_POST["service_profile_id"]);
		}
		if (empty($_POST["name"])) {
			$nameErr = "Name is required";
		} else {
			$name = test_input($_POST["name"]);
		}
		if (empty($_POST["ports"])) {
                        $portsErr = "Ports is required";
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
	
	print "<form action=\"service_profile.php\" method=\"post\">";
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
	// ADD SERVICE PROFILE
	if ($name !== '' && $ports !== '' && $service_profile_id !== '' && $submit == "ADD") {
		try {
			$result = $db->query("INSERT INTO SERVICE_PROFILE (NAME, PORTS, OLT, SERVICE_PROFILE_ID) VALUES ('$name', '$ports', '$olt', '$service_profile_id')");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
        	exit;
		}
		exit("SERVICE PROFILE added Succesfully");
	}
	
	// EDIT SERVICE PROFILE
	if ($service_profile_id !== '' && $name !== '' && $submit == "EDIT") {
		// UPDATE PROFILE
		try {
			$result = $db->query("UPDATE SERVICE_PROFILE SET NAME = '$name', SERVICE_PROFILE_ID = '$service_profile_id' where ID = '$t_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
        	exit;
		}
		exit("SERVICE PROFILE Edited Succesfully");
	}
	// DELETE SERVICE PROFILE
	if ($t_id !== '' && $submit == "DELETE") {
		// CHECK IF TEMPLATE IS ASSIGNED TO ANY CUSTOMER
		try {
			$result = $db->query("SELECT SERVICE_PROFILE from CUSTOMERS where SERVICE_PROFILE =  '$t_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["SERVICE_PROFILE"])
				exit (" ERROR: SERVICE PROFILE IS ASSIGNED TO CUSTOMERS, Please remove SERVICE PROFILE from customers before Delete it!");
		}
		
		try {
			$result = $db->query("DELETE FROM SERVICE_PROFILE where ID='$t_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		exit("SERVICE PROFILE Deleted Succesfully");
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
			$result = $db->query("SELECT ID, NAME, PORTS, SERVICE_PROFILE_ID from SERVICE_PROFILE where ID='$t_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$t_id = $row["ID"];
			$name = $row["NAME"];
			$ports = $row["PORTS"];
			$service_profile_id = $row["SERVICE_PROFILE_ID"];
		}
	}

	print "<form action=\"service_profile.php\" method=\"post\">";
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
		$result = $db->query("SELECT ID, NAME, PORTS, SERVICE_PROFILE_ID from SERVICE_PROFILE where OLT='$olt'");
	} catch (PDOException $e) {
		echo "Connection Failed:" . $e->getMessage() . "\n";
        exit;
	}
	
	print "<table border=1 cellpadding=1 cellspacing=1><tr align=center style=font-weight:bold><td>ID</td><td>NAME</td><td>PORTS</td><td>SERVICE_PROFILE_ID</td></tr>";
	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		print "<tr align=right><td><a href='service_profile.php?edit=1&id=". $row{'ID'} . "&olt=" . $olt . "'>" . $row{'ID'} . "</a></td><td>" . $row{'NAME'} . "</td><td>" . $row{'PORTS'} . "</td><td>" . $row{'SERVICE_PROFILE_ID'} . "</td></tr>";
	}
	print "</table>";
	print "<form action=\"service_profile.php\" method=\"post\">";
	print "<p><table>";
	if (isset($_GET["edit"])) {
		$edit = $_GET["edit"];
	} else {
		$edit = NULL;
	}

	if ($edit == "1") {
		print "<tr><td>Service Profile ID*:</td><td>" . $service_profile_id . "</td>";
		print "<input type=\"hidden\" name=\"service_profile_id\" value=\"". $service_profile_id ."\">";
		print "<input type=\"hidden\" name=\"t_id\" value=\"". $t_id ."\">";
	} else {
		print "<tr><td>Service Profile ID*:</td><td><input type=\"text\" name=\"service_profile_id\"";
		if($service_profile_id)
			print "value=\"".$service_profile_id ."\"";
		print "></td>";
	}
	if($service_profile_idErr != "") 
		print "<td style=\"color:red\">" . $service_profile_idErr . "</td>";
	print "</tr>";
	print "<tr><td>Name*:</td><td><input type=\"text\" name=\"name\"";
	if($name) 
		print "value=\"".$name ."\"";
	print "></td>";
	if($nameErr != "") 
		print "<td style=\"color:red\">" . $nameErr . "</td>";

	print "</tr>";


	if ($edit == "1") {
                print "<tr><td>Ports*:</td><td>" . $ports . "</td>";
                print "<input type=\"hidden\" name=\"ports\" value=\"". $ports ."\">";
	} else {
                print "<tr><td>Ports*:</td><td><input type=\"text\" name=\"ports\"></td>";
	}

        if($portsErr != "")
                print "<td style=\"color:red\">" . $portsErr . "</td>";




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

