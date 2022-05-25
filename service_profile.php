<?php
include ("header.php");
include ("common.php");
include ("classes/service_profile_class.php");
include ("navigation.php");

if ($user_class < "6")
	exit();


$service_profile_obj = new service_profile();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($service_profile_obj->getSubmit() == "ADD") {
		if (!empty($service_profile_obj->getName()) && !empty($service_profile_obj->getPorts()) && !empty($service_profile_obj->getService_Profile_id())) {
			$error = $service_profile_obj->create_service_profile();	
			if (isset($error)) {
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";	
			}else{
				
				echo "<center><div class=\"bg-success  text-white\">Service Profile added Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, Ports and Service_Profile_Id are required fields!</div></center>";
		}
	}


	if ($service_profile_obj->getSubmit() == "EDIT") {
		if (!empty($service_profile_obj->getId()) && !empty($service_profile_obj->getName()) && !empty($service_profile_obj->getPorts()) && !empty($service_profile_obj->getService_Profile_id())) {
			$error = $service_profile_obj->edit_service_profile();	
			if (isset($error)) {
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";	
			}else{
				print "<center><div class=\"bg-success  text-white\">Service Profile Edited Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, Ports and Service_Profile_Id are required fields! Or you are missing ID!</div></center>";
		}
	}



	if ($service_profile_obj->getSubmit() == "DELETE") {
		if (!empty($service_profile_obj->getId())) {
			$error = $service_profile_obj->delete_service_profile();	
			if (isset($error)){
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";	
			}else{
				echo "<center><div class=\"bg-success  text-white\">Service Profile Deleted Succesfully</div></center>";
			}
		} else {
			echo  "<center><div class=\"bg-danger text-white\">ERROR: ID missing!</div></center>";
		}
	}
}

?>



<div class="container">
	<div class="text-center">
		<div class="page-header">
			<h2>Service Profile Configuration</h2>
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
						<th>Service Profile ID</th>
						<th>HGU</th>
						<th>RF</th>
						<th>Edit</th>
					  </tr>
					</thead>
					<tbody>
					<?php 
					// BUILD EXISTING TABLE
					$rows = $service_profile_obj->build_table_service_profile(); 
					foreach ($rows as $row) {
						print "<tr><td>" . $row['NAME'] . "</td><td>" . $row['PORTS'] . "</td><td>" . $row['SERVICE_PROFILE_ID'] . "</td><td>" . $row['HGU'] . "</td><td>" . $row['RF'] . "</td><td><button type=\"button\" class=\"btn btn-default\" onClick=\"getService_Profile('". $row['ID'] ."');\">EDIT</button></td></tr>";		
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
				<button type="button" class="btn btn-info" onClick="getService_Profile();">ADD NEW SERVICE PROFILE</button>
			</div>
		</div>
	<div class="col-md-4"></div>
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog"> 
			  <!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Service Profile</h4>
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
				

