<?php
include ("header.php");
include ("common.php");
include ("navigation.php");
include ("classes/backup_class.php");
include ("classes/snmp_class.php");

if ($user_class < "6")
	exit();

$backup_obj = new backup(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// ADD
	if ($backup_obj->getSubmit() == "ADD") {
		if (!empty($backup_obj->getName()) && !empty($backup_obj->getIp_address()) && !empty($backup_obj->getUsername()) && !empty($backup_obj->getPassword()) && !empty($backup_obj->getDirectory())) {
			$error = $backup_obj->create();	
			if (isset($error)) {
				echo $error;	
			}else{
				echo "<center><div class=\"bg-success  text-white\">FTP added Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, IP_Address, USERNAME, PASSWORD and DIRECTORY are required fields!</div></center>";
		}
	}

	// EDIT
	if ($backup_obj->getSubmit() == "EDIT") {
		if (!empty($backup_obj->getId()) && !empty($backup_obj->getName()) && !empty($backup_obj->getIp_address()) && !empty($backup_obj->getUsername()) && !empty($backup_obj->getPassword()) && !empty($backup_obj->getDirectory())) {
			$error = $backup_obj->edit();
			if (isset($error)) {
				echo $error;	
			}else{
				print "<center><div class=\"bg-success  text-white\">FTP Edited Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, IP_Address, USERNAME, PASSWORD and DIRECTORY are required fields! Or you are missing ID!</div></center>";
		}
	}
	
	// DELETE
	if ($backup_obj->getSubmit() == "DELETE") {
		if (!empty($backup_obj->getId())) {
		$error = $backup_obj->delete();
			if (isset($error)) {
				echo $error;	
			}else{
				print "<center><div class=\"bg-success  text-white\">FTP Deleted Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: ID missing!</div></center>";
		
		}
	}
	
		// ADD EMAIL
	if ($backup_obj->getSubmit() == "ADD_EMAIL") {
		if (!empty($backup_obj->getEmail())) {
			$error = $backup_obj->create_email();	
			if (isset($error)) {
				echo $error;	
			}else{
				echo "<center><div class=\"bg-success  text-white\">Email added Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Email is required field!</div></center>";
		}
	}

	// EDIT
	if ($backup_obj->getSubmit() == "EDIT_EMAIL") {
		if (!empty($backup_obj->getEmailid()) && !empty($backup_obj->getEmail())) {
			$error = $backup_obj->edit_email();
			if (isset($error)) {
				echo $error;	
			}else{
				print "<center><div class=\"bg-success  text-white\">Email Edited Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Email is required field! Or you are missing ID!</div></center>";
		}
	}
	
	// DELETE
	if ($backup_obj->getSubmit() == "DELETE_EMAIL") {
		if (!empty($backup_obj->getEmailid())) {
		$error = $backup_obj->delete_email();
			if (isset($error)) {
				echo $error;	
			}else{
				print "<center><div class=\"bg-success  text-white\">Email Deleted Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: ID missing!</div></center>";
		
		}
	}
}
?>
<div class="container">
	<div class="text-center">
		<div class="page-header">
			<h2>OLT Startup-Config Backup FTP Configuration</h2>
		</div>
	</div>
	<div class=row>
		<div class="text-center">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed table-hover">
					<thead>
					  <tr>
						<th>Name</th>
						<th>IP Address</th>
						<th>Username</th>
						<th>Password</th>
						<th>Directory</th>
						<th>Edit</th>
					  </tr>
					</thead>
					<tbody>
					<?php
					$rows = $backup_obj->build_table(); 
					foreach ($rows as $row) {
						?>
						<tr>
							<td><?php echo $row{'NAME'}; ?></td>
							<td><?php echo $row{'IP_ADDRESS'}; ?></td>
							<td><?php echo $row{'USERNAME'}; ?></td>
							<td><?php echo $row{'PASSWORD'}; ?></td>
							<td><?php echo $row{'DIRECTORY'}; ?></td>
							<td><button type="button" class="btn btn-default" onClick="getBackup('<?php echo $row{'ID'}; ?>');">EDIT</button></td>
						</tr>
						<?php
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
		<div class="text-center">
				<button type="button" class="btn btn-info" onClick="getBackup();">ADD NEW FTP</button>
		</div>
	</div>
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog"> 
			  <!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">FTP</h4>
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
			<h2>RAISEPON Database Backup EMAIL Configuration</h2>
		</div>
	</div>
	<div class=row>
		<div class="text-center">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed table-hover">
					<thead>
					  <tr>
						<th>Email</th>
						<th>Edit</th>
					  </tr>
					</thead>
					<tbody>
					<?php
					$rows = $backup_obj->build_table_email(); 
					foreach ($rows as $row) {
						?>
						<tr>
							<td><?php echo $row{'EMAIL'}; ?></td>
							<td><button type="button" class="btn btn-default" onClick="getBackupEmail('<?php echo $row{'ID'}; ?>');">EDIT</button></td>
						</tr>
						<?php
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
		<div class="text-center">
				<button type="button" class="btn btn-info" onClick="getBackupEmail();">ADD NEW EMAIL</button>
		</div>
	</div>
	<div class="modal fade" id="myModalEmail" role="dialog">
		<div class="modal-dialog"> 
			  <!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">EMAIL</h4>
					</div>
					<div class="modal-body" id="modalbodyemail">
					</div>
					<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>	
