<?php
include ("common.php");
include ("dbconnect.php");
include ("navigation.php");
if ($user_class < "9")
	exit();

$nameErr = $oltErr = $slot_idErr = $port_idErr = "";
$olt_id = $name = $olt = $slot_id = $port_id = $pon_id = "";

print "<p><center>PON Ports Configuration</p>";
if (!($_SERVER["REQUEST_METHOD"] == "POST")&&!($_GET)) {
	print "<form action=\"pon_old.php\" method=\"post\">";
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
	print "</select>";
	print "<input type='submit' name='SUBMIT' value='GET'><br><br>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST["pon_id"])) {
		$pon_id = test_input($_POST["pon_id"]);
	}

	if (empty($_POST["olt"])) {
		$oltErr = "OLT is required";
	} else {
		$olt = test_input($_POST["olt"]);
	}

	if (isset($_POST["olt_id"])) {
		$olt_id = test_input($_POST["olt_id"]);
	}

	if ($_POST["SUBMIT"]!="GET") {
		$submit = test_input($_POST["SUBMIT"]);
		if (empty($_POST["name"])) {
			$nameErr = "Name is required";
		} else {
			$name = test_input($_POST["name"]);
		}
		
		if (empty($_POST["slot_id"])) {
			$slot_idErr = "SLOT_ID is required";
		} else {
			$slot_id = test_input($_POST["slot_id"]);
		}
		if (empty($_POST["port_id"])) {
			$port_idErr = "Port ID is required";
		} else {
			$port_id = test_input($_POST["port_id"]);
		}
	}
	
	print "<form action=\"pon_old.php\" method=\"post\">";
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
	print "</select>";
	print "<input type='submit' name='SUBMIT' value='GET'><br><br>";
	
	try {
		$result = $db->query("SELECT PON.ID, PON.NAME as NAME, OLT, SLOT_ID, PORT_ID from PON LEFT JOIN OLT on PON.OLT=OLT.ID where OLT.ID='$olt' order by SLOT_ID, PORT_ID");
	} catch (PDOException $e) {
		echo "Connection Failed:" . $e->getMessage() . "\n";
        exit;
	}
	
	print "<table border=1 cellpadding=1 cellspacing=1><tr align=center style=font-weight:bold><td>NAME</td></a><td>SLOT_ID</td><td>PORT_ID</td></tr>";
	
	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		print "<tr align=right><td><a href='pon_old.php?id=". $row{'ID'} . "&olt_id=" .$olt. "'>" . $row{'NAME'} . "</a></td><td>" . $row{'SLOT_ID'} . "</td><td>" . $row{'PORT_ID'} . "</td></tr>";
	}
	print "</table>";
	print_r($row);
	
	// ADD PON Port
	if ($name !== '' && $slot_id !== '' && $port_id !== '' && $olt !== '' && $submit == "ADD") {
		// CHECK SLOT_ID, PORT_ID for DUPLICATES
		try {
			$result = $db->query("SELECT SLOT_ID, PORT_ID from PON where OLT='$olt' and SLOT_ID='$slot_id' and PORT_ID='$port_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["PORT_ID"])
				exit ("ERROR!  DUPLICATE PON PORT!");
		}
		
		try {
			$result = $db->query("INSERT INTO PON (NAME, OLT, SLOT_ID, PORT_ID) VALUES ('$name', '$olt', '$slot_id', '$port_id')");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		try {
			$result = $db->query("SELECT  INET_NTOA(IP_ADDRESS) as IP_ADDRESS, OLT_MODEL.TYPE from OLT LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID where OLT.ID='$olt'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ip_address = $row{'IP_ADDRESS'} ;
			$type = $row{'TYPE'};
		}
		//CREATE RRD
		$traffic = array("traffic", "unicast", "broadcast", "multicast");
		foreach ($traffic as $tr) {
			$rrd_name = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $slot_id . "000000" . $port_id . "_" . $tr . ".rrd";
			$opts = array("--step", "300", "--start", 0,
				"DS:input:DERIVE:600:0:U",
				"DS:output:DERIVE:600:0:U",
				"RRA:AVERAGE:0.5:1:600",
				"RRA:AVERAGE:0.5:6:700",
				"RRA:AVERAGE:0.5:24:775",
				"RRA:AVERAGE:0.5:288:797",
				"RRA:MAX:0.5:1:600",
				"RRA:MAX:0.5:6:700",
				"RRA:MAX:0.5:24:775",
				"RRA:MAX:0.5:288:797"
			);
			$ret = rrd_create($rrd_name, $opts);
			if( $ret == 0 )
			{
				$err = rrd_error();
				echo "$err";
			}
		}
		exit("<BR><BR>PON Port added Succesfully");
	}
	// EDIT PON
	if ($pon_id !== '' && $name !== '' && $submit == "EDIT") {
		// CHECK SLOT_ID, PORT_ID for DUPLICATES
		try {
			$result = $db->query("SELECT SLOT_ID, PORT_ID from PON where OLT='$olt' and SLOT_ID='$slot_id' and PORT_ID='$port_id' and ID != '$pon_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["PORT_ID"])
				exit ("ERROR!  DUPLICATE PON PORT!");
		}
		// UPDATE PON
		try {
			$result = $db->query("UPDATE PON SET NAME = '$name' where ID = '$pon_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		exit("<BR><BR>PON Port Edited Succesfully");
	}
	// DELETE PON PORT
	if ($pon_id !== '' && $submit == "DELETE") {
		// CHECK IF PON IS ASSIGNED TO ANY CUSTOMER
		try {
			$result = $db->query("SELECT PON_PORT from CUSTOMERS where PON_PORT =  '$pon_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["PON_PORT"])
				exit ("<BR><BR>PON PORT IS ASSIGNED TO CUSTOMERS, Please remove PON PORT from customers to Delete it!");
		}
		
		try {
			$result = $db->query("DELETE FROM PON where ID='$pon_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		try {
			$result = $db->query("SELECT  INET_NTOA(IP_ADDRESS) as IP_ADDRESS from OLT where ID='$olt'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ip_address = $row{'IP_ADDRESS'} ;
		}
		
		//DELETE RRD
		$traffic = array("traffic", "unicast", "broadcast", "multicast", "power");
		foreach ($traffic as $tr) {
			$rrd_name = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $slot_id . "000000" . $port_id . "_" . $tr . ".rrd";
			unlink($rrd_name);
		}
		exit("<BR><BR>PON Port Deleted Succesfully");
	}
}

if ($_GET) {
	$pon_id = $_GET['id'];
	$olt = $_GET['olt_id'];
	if (!preg_match('/^[0-9]*$/', $pon_id)) {
		print "that sux";
        exit;
	} else {
		try {
			$result = $db->query("SELECT NAME, OLT, SLOT_ID, PORT_ID from PON where ID='$pon_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$name = $row["NAME"];
			$slot_id = $row["SLOT_ID"];
			$port_id = $row["PORT_ID"];
		}
	}
	print "<form action=\"pon_old.php\" method=\"post\">";
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
	
	print "</select>";
	print "<input type='submit' name='SUBMIT' value='GET'><br><br>";
	try {
		$result = $db->query("SELECT PON.ID, PON.NAME as NAME, OLT, SLOT_ID, PORT_ID  from PON LEFT JOIN OLT on PON.OLT=OLT.ID where OLT.ID='$olt' order by SLOT_ID, PORT_ID");
	} catch (PDOException $e) {
		echo "Connection Failed:" . $e->getMessage() . "\n";
        exit;
	}
	print "<table border=1 cellpadding=1 cellspacing=1><tr align=center style=font-weight:bold><td>NAME</td><td>SLOT_ID</td><td>PORT_ID</td></tr>";
	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		print "<tr align=right><td><a href='pon_old.php?id=". $row{'ID'} . "&olt_id=" .$olt. "'>" . $row{'NAME'} . "</a></td><td>" . $row{'SLOT_ID'} . "</td><td>" . $row{'PORT_ID'} . "</td></tr>";
	}
	print "</table>";
}

if (($_SERVER["REQUEST_METHOD"] == "POST")||($_GET)) {
	print "<form action=\"pon_old.php\" method=\"post\">";
	if ($olt)
		print "<input type=\"hidden\" name=\"olt_id\" value=\"". $olt ."\">";
//	if ($pon_id)
//	print "<input type=\"hidden\" name=\"pon_id\" value=\"". $pon_id ."\">";
//	if ($_GET["edit"] == "1" || $pon_id) {
	if ($_GET) {
		print "<input type=\"hidden\" name=\"pon_id\" value=\"". $pon_id ."\">";
		print "<input type=\"hidden\" name=\"slot_id\" value=\"". $slot_id ."\">";
		print "<input type=\"hidden\" name=\"port_id\" value=\"". $port_id ."\">";
		print "<p><table>";
		print "<tr><td>Name*:</td><td><input type=\"text\" name=\"name\""; 
		if($name) print "value=\"".$name ."\"";
		print "></td>";
		if($nameErr != "") print "<td style=\"color:red\">" . $nameErr . "</td>";
		print "</tr><tr><td>SLOT_ID*:</td><td>";
		if($name) 
			print $slot_id ;
		print "</td></tr><tr><td>PORT_ID*:</td><td>";
		if($name) 
			print $port_id ;
		print "</td></tr></table></p>";
		print "<input type='submit' name='SUBMIT' value='EDIT'>";
		print "&nbsp;&nbsp;&nbsp;<input type='submit' name='SUBMIT' value='DELETE'>";
	}else{
		print "<p><table><tr><td>Name*:</td><td><input type=\"text\" name=\"name\"";
		if($name) 
			print "value=\"".$name ."\"";
		print "></td>";
		if($nameErr != "") 
			print "<td style=\"color:red\">" . $nameErr . "</td>";
		print "</tr><tr><td>SLOT_ID*:</td><td><input type=\"text\" name=\"slot_id\"";
		if($slot_id) 
			print "value=\"".$slot_id ."\"";
		print"></td>";
		if($slot_idErr != "") 
			print "<td style=\"color:red\">" . $slot_idErr . "</td>";
		print "</tr><tr><td>PORT_ID*:</td><td><input type=\"text\" name=\"port_id\"";
		if($port_id) 
			print "value=\"".$port_id ."\""; 
		print "></td>";
		if($port_idErr != "") 
			print "<td style=\"color:red\">" . $port_idErr . "</td>";
		print "</tr></table></p>";
		print "<input type='submit' name='SUBMIT' value='ADD'>";
	}
}
print "</center>";
?>
