<?php
include ("header.php");
include ("common.php");
include ("classes/accounts_class.php");
include ("navigation.php");

//navigation();
if ($user_class < "9")
	exit();


$accounts_obj = new accounts();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if( $accounts_obj->getForm_token() != $_SESSION['form_token'] && $accounts_obj->getSubmit() !== "DELETE"){
		exit('Invalid form submission');
	}
	if ($accounts_obj->getSubmit() == "ADD") {
		if (!empty($accounts_obj->getUsername()) && !empty($accounts_obj->getPassword()) && !empty($accounts_obj->getType())) {
			$error = $accounts_obj->create();	
			if (isset($error)) {
				echo $error;	
			}else{			
				echo "<center><div class=\"bg-success  text-white\">Account added Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Username, Password and Type are required fields!</div></center>";
		}

	}


	if ($accounts_obj->getSubmit() == "EDIT") {
		if (!empty($accounts_obj->getAccount_id()) && !empty($accounts_obj->getUsername()) && !empty($accounts_obj->getType())) {
			$error = $accounts_obj->edit();	
			if (isset($error)) {
				echo $error;	
			}else{
				print "<center><div class=\"bg-success  text-white\">Account Edited Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Username, Password and Type are required fields! Or you are missing Account ID!</div></center>";
		}
	}



	if ($accounts_obj->getSubmit() == "DELETE") {
		if (!empty($accounts_obj->getAccount_id())) {
			$error = $accounts_obj->delete();	
			if (isset($error)){
				echo $error;
			}else{
				echo "<center><div class=\"bg-success  text-white\">Account Deleted Succesfully</div></center>";
			}
		} else {
			echo  "<center><div class=\"bg-danger text-white\">ERROR: ACCOUNT ID missing!</div></center>";
		}
	}
	
	unset( $_SESSION['form_token'] );
}

?>



<div class="container">
	<div class="text-center">
		<div class="page-header">
			<h2>Accounts</h2>
		</div>
	</div>
	<div class=row>
        	<div class="text-center">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<thead>
					  <tr>
						<th>ID</th>
						<th>Username</th>
						<th>Type</th>
						<th>Edit</th>
					  </tr>
					</thead>
					<tbody>
					<?php 
					// BUILD EXISTING TABLE
					$rows = $accounts_obj->build_table(); 
					foreach ($rows as $row) {
						if ($row['TYPE'] == '9')
							$type = "Admin";
						if ($row['TYPE'] == '6')
							$type = "Operator";
						if ($row['TYPE'] == '3')
							$type = "Visitor";
						print "<tr><td>" . $row['ID'] . "</td><td>" . $row['USERNAME'] . "</td><td>" . $type . "</td><td><button type=\"button\" class=\"btn btn-default\" onClick=\"getAccount('". $row['ID'] ."');\">EDIT</button></td></tr>";		
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
				<button type="button" class="btn btn-info" onClick="getAccount();">ADD NEW ACCOUNT</button>
			</div>
		</div>
	<div class="col-md-4"></div>
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog"> 
			  <!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						 <h4 class="modal-title">User Account</h4>
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
				

