<?php
include ("header.php");
include ("common.php");
include ("classes/line_profile_class.php");
include ("navigation.php");

//navigation();
if ($user_class < "6")
	exit();


$line_profile_obj = new line_profile();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($line_profile_obj->getSubmit() == "ADD") {
		if (!empty($line_profile_obj->getName()) && !empty($line_profile_obj->getLine_profile_id())) {
			$error = $line_profile_obj->create_line_profile();	
			if (isset($error)) {
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";	
			}else{
				
				echo "<center><div class=\"bg-success  text-white\">Service Profile added Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, Ports and line_profile_Id are required fields!</div></center>";
		}
	}


	if ($line_profile_obj->getSubmit() == "EDIT") {
		if (!empty($line_profile_obj->getId()) && !empty($line_profile_obj->getName()) && !empty($line_profile_obj->getLine_profile_id())) {
			$error = $line_profile_obj->edit_line_profile();	
			if (isset($error)) {
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";	
			}else{
				print "<center><div class=\"bg-success  text-white\">Service Profile Edited Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, Ports and line_profile_Id are required fields! Or you are missing ID!</div></center>";
		}
	}



	if ($line_profile_obj->getSubmit() == "DELETE") {
		if (!empty($line_profile_obj->getId())) {
			$error = $line_profile_obj->delete_line_profile();	
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
			<h2>Line Profile Configuration</h2>
		</div>
	</div>
	<div class=row>
		<div class="col-md-6 col-md-offset-3">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<thead>
					  <tr>
						<th>Name</th>
						<th>Line Profile ID</th>
						<th>Edit</th>
					  </tr>
					</thead>
					<tbody>
					<?php 
					// BUILD EXISTING TABLE
					$rows = $line_profile_obj->build_table_line_profile(); 
					foreach ($rows as $row) {
						print "<tr><td>" . $row{'NAME'} . "</td><td>" . $row{'LINE_PROFILE_ID'} . "</td><td><button type=\"button\" class=\"btn btn-default\" onClick=\"getLine_profile('". $row{'ID'} ."');\">EDIT</button></td></tr>";		
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
				<button type="button" class="btn btn-info" onClick="getLine_profile();">ADD NEW Line PROFILE</button>
			</div>
		</div>
	<div class="col-md-4"></div>
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog"> 
			  <!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Line Profile</h4>
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
				

