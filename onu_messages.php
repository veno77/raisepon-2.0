<?php
include ("common.php");
include ("classes/onu_class.php");

if ($user_class < "9")
	exit();
exit("boza");
//POST METHOD

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$onu_obj = new onu();

	if ($onu_obj->getSubmit() == "ADD") {
		if (!empty($onu_obj->getName()) && !empty($onu_obj->getPorts()) && !empty($onu_obj->getPon_type())) {
			$error = $onu_obj->create_onu();	
			if (isset($error)) {
				echo $error;	
			}else{
				echo "ONU added Succesfully";
			}
		} else {
			echo "ERROR: Name, Ports and Pon_Type are required fields!";
		}
	}
	
	
	if ($onu_obj->getSubmit() == "EDIT") {
		if (!empty($onu_obj->getOnu_id()) && !empty($onu_obj->getName()) && !empty($onu_obj->getPorts()) && !empty($onu_obj->getPon_type())) {
			$error = $onu_obj->edit_onu();	
			if (isset($error)) {
				echo $error;	
			}else{
				print "<div class=row>ONU Edited Succesfully</div>";
			}
		} else {
			echo "ERROR: Name, Ports and pon_type are required fields! Or you are missing ONU_ID!";
		}
	}
	
	
	
	if ($onu_obj->getSubmit() == "DELETE") {
		if (!empty($onu_obj->getOnu_id())) {
			$error = $onu_obj->delete_onu();	
			if (isset($error)){
				echo $error;
			}else{
				echo "ONU Deleted Succesfully";
			}
		} else {
			echo  "ERROR: ONU_ID missing!";
		}
	}



}
?>

	