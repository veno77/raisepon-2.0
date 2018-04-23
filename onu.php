<?php
include ("common.php");
include ("classes/onu_class.php");
include ("navigation.php");

//navigation();
if ($user_class < "9")
	exit();


$onu_obj = new onu();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($onu_obj->getSubmit() == "ADD") {
		if (!empty($onu_obj->getName()) && !empty($onu_obj->getPorts()) && !empty($onu_obj->getPon_type())) {
			$error = $onu_obj->create_onu();	
			if (isset($error)) {
				echo $error;	
			}else{
				
				echo "<center><div class=\"bg-success  text-white\">ONU added Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, Ports and Pon_Type are required fields!</div></center>";
		}
	}


	if ($onu_obj->getSubmit() == "EDIT") {
		if (!empty($onu_obj->getOnu_id()) && !empty($onu_obj->getName()) && !empty($onu_obj->getPorts()) && !empty($onu_obj->getPon_type())) {
			$error = $onu_obj->edit_onu();	
			if (isset($error)) {
				echo $error;	
			}else{
				print "<center><div class=\"bg-success  text-white\">ONU Edited Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, Ports and pon_type are required fields! Or you are missing ONU_ID!</div></center>";
		}
	}



	if ($onu_obj->getSubmit() == "DELETE") {
		if (!empty($onu_obj->getOnu_id())) {
			$error = $onu_obj->delete_onu();	
			if (isset($error)){
				echo $error;
			}else{
				echo "<center><div class=\"bg-success  text-white\">ONU Deleted Succesfully</div></center>";
			}
		} else {
			echo  "<center><div class=\"bg-danger text-white\">ERROR: ONU_ID missing!</div></center>";
		}
	}
}

?>



<div class="container">
	<div class="text-center">
		<div class="page-header">
			<h2>ONU Configuration</h2>
		</div>
	</div>
	<div class=row>
		<div class="col-md-6 col-md-offset-3">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<thead>
					  <tr>
						<th>Name</th>
						<th>Ports</th>
						<th>RF</th>
						<th>PSE</th>
						<th>HGU</th>
						<th>PON</th>
						<th>Edit</th>
					  </tr>
					</thead>
					<tbody>
					<?php 
					// BUILD EXISTING TABLE
					$rows = $onu_obj->build_table_onu(); 
					foreach ($rows as $row) {
						print "<tr><td>" . $row{'NAME'} . "</td><td>" . $row{'PORTS'} . "</td><td>" . $row{'RF'} . "</td><td>" . $row{'PSE'} ."</td><td>" . $row{'HGU'} ."</td><td>" . $row{'PON_TYPE'} ."</td><td><button type=\"button\" class=\"btn btn-default\" onClick=\"getOnu('". $row{'ID'} ."');\">EDIT</button></td></tr>";		
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-md-4"></div>
			<div class="col-md-4">
				<button type="button" class="btn btn-info" onClick="getOnu();">ADD NEW ONU</button>
			</div>
		</div>
	<div class="col-md-4"></div>
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog"> 
			  <!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body" id="modalbody">
					</div>
					<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>
				

