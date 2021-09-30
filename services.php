<?php
include ("header.php");
include ("common.php");
include ("classes/services_class.php");
include ("navigation.php");
require_once("classes/index_class.php");

if ($user_class < "6")
	exit();


$services_obj = new services();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($services_obj->getSubmit() == "ADD") {
		if (!empty($services_obj->getName()) && !empty($services_obj->getLine_profile_id()) && !empty($services_obj->getService_Profile_id())) {
			$error = $services_obj->create_service();	
			if (isset($error)) {
				echo $error;	
			}else{
				
				echo "<center><div class=\"bg-success  text-white\">Service added Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, Line_Profile and Service_Profile are required fields!</div></center>";
		}
	}


	if ($services_obj->getSubmit() == "EDIT") {
		if (!empty($services_obj->getService_id()) && !empty($services_obj->getName()) && !empty($services_obj->getLine_profile_id()) && !empty($services_obj->getService_Profile_id())) {
			$error = $services_obj->edit_service();	
			if (isset($error)) {
				echo $error;	
			}else{
				print "<center><div class=\"bg-success  text-white\">Service Edited Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, Line_Profile and Service_Profile are required fields! Or you are missing SERVICES ID!</div></center>";
		}
	}



	if ($services_obj->getSubmit() == "DELETE") {
		if (!empty($services_obj->getService_id())) {
			$error = $services_obj->delete_service();	
			if (isset($error)){
				echo $error;
			}else{
				echo "<center><div class=\"bg-success  text-white\">Service Deleted Succesfully</div></center>";
			}
		} else {
			echo  "<center><div class=\"bg-danger text-white\">ERROR: SERVICE ID missing!</div></center>";
		}
	}
	
	
	if ($services_obj->getSubmit() == "RUN") {
		if (!empty($services_obj->getService_id()) && !empty($services_obj->getService_new_id())) {
			$error = $services_obj->change_service();	
			if (isset($error)) {
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";	
			}else{
				print "<center><div class=\"bg-success  text-white\">Service Changed Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: OLD SERVICE and NEW SERVICE are required fields! </div></center>";
		}
		exit();
	}
	
}

?>

<div id="output">
</div>


<div class="container">
	<div class="text-center">
		<div class="page-header">
			<h2>SERVICES Configuration</h2>
		</div>
	</div>
	<div class=row>
        	<div class="text-center">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<thead>
					  <tr>
						<th>Name</th>
						<th>Line Profile</th>
						<th>Service Profile</th>
						<th>Edit</th>
					  </tr>
					</thead>
					<tbody>
					<?php 
					// BUILD EXISTING TABLE
					$rows = $services_obj->build_table_services(); 
					foreach ($rows as $row) {
						print "<tr><td>" . $row{'NAME'} . "</td><td>" . $row{'LINE_PROFILE_NAME'} . "</td><td>" . $row{'SERVICE_PROFILE_NAME'} . "</td><td><button type=\"button\" class=\"btn btn-default\" onClick=\"getService('". $row{'ID'} ."');\">EDIT</button></td></tr>";		
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
				<button type="button" class="btn btn-info" onClick="getService();">ADD NEW SERVICE</button>
			</div>
		</div>
	<div class="col-md-4"></div>
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog"> 
			  <!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						 <h4 class="modal-title">Services</h4>
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


<div class="container">
	<div class="text-center">
		<div class="page-header">
			<h2>Change Service</h2>
		</div>
	</div>
	<div class="row justify-content-md-center">
		<div class="text-center">
			<div class="form-group">
				<form class="form-inline" id="load" method="post">
					<div class="content">
						<label for="olt_id">OLT</label>
						<select class="form-control" id="select-olt" name="olt_id">
						<option value="" class="rhth">Select OLT</option>
						<?php
						$index_obj = new index();
						$rows = $index_obj->get_from_olt();
						foreach ($rows as $row) { 
										print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
						}
						?>
						</select>
						<label for="pon_id">PON</label>
						<select class="form-control" id="select-pon" name="pon_id">
						<option value="">PON PORT</option></select>
						<label for="service_id">OLD SERVICE</label>
						<select class="form-control" id="select-service" name="service_id">
						<option value="">SELECT SERVICE</option>
							<?php
						$index_obj = new index();
						$rows = $services_obj->build_table_services(); 
						foreach ($rows as $row) { 
										print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
						}
						?>
						</select>
						<label for="service_new_id">MOVE TO NEW SERVICE</label>
						<select class="form-control" id="select-new-service" name="service_new_id">
						<option value="">SELECT SERVICE</option>
							<?php
						$index_obj = new index();
						$rows = $services_obj->build_table_services(); 
						foreach ($rows as $row) { 
										print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
						}
						?>
						</select>
						<input type="hidden" name="SUBMIT" value="RUN">
						<button class="btn btn-info" type="button" onClick="RunServiceChange('RUN');">RUN</button>	
					</div>
				</form>
			</div>
		</div>
	</div>
</div>				
