<?php
include_once("header.php");
include_once("common.php");
include_once("classes/ip_pool_class.php");
include_once("navigation.php");

if ($user_class < "6")
	exit();


$ip_pool_obj = new ip_pool();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($ip_pool_obj->getSubmit() == "ADD") {
		if (!empty($ip_pool_obj->getSubnet()) && !empty($ip_pool_obj->getNetmask()) && !empty($ip_pool_obj->getStart_ip()) && !empty($ip_pool_obj->getEnd_ip()) && !empty($ip_pool_obj->getGateway()) && !empty($ip_pool_obj->getVlan())) {
			$error = $ip_pool_obj->create_ip_pool();	
			if (isset($error)) {
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";	
			}else{
				
				echo "<center><div class=\"bg-success  text-white\">IP Pool added Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Subnet, Netmask, Start_IP, End_IP, Gateway and Vlan are required fields!</div></center>";
		}
	}


	if ($ip_pool_obj->getSubmit() == "EDIT") {
		if (!empty($ip_pool_obj->getId()) && !empty($ip_pool_obj->getSubnet()) && !empty($ip_pool_obj->getNetmask()) && !empty($ip_pool_obj->getStart_ip()) && !empty($ip_pool_obj->getEnd_ip()) && !empty($ip_pool_obj->getGateway()) && !empty($ip_pool_obj->getVlan())) {
			$error = $ip_pool_obj->edit_ip_pool();	
			if (isset($error)) {
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";	
			}else{
				print "<center><div class=\"bg-success  text-white\">IP Pool Edited Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Subnet, Netmask, Start_IP, End_IP, Gateway and Vlan are required fields! Or you are missing ID!</div></center>";
		}
	}



	if ($ip_pool_obj->getSubmit() == "DELETE") {
		if (!empty($ip_pool_obj->getId())) {
			$error = $ip_pool_obj->delete_ip_pool();	
			if (isset($error)){
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";	
			}else{
				echo "<center><div class=\"bg-success  text-white\">IP Pool Deleted Succesfully</div></center>";
			}
		} else {
			echo  "<center><div class=\"bg-danger text-white\">ERROR: ID missing!</div></center>";
		}
	}
	
	
	if ($ip_pool_obj->getSubmit() == "ADD_BINDING") {
		if (!empty($ip_pool_obj->getId()) && !empty($ip_pool_obj->getOlt_id())) {
			$error = $ip_pool_obj->create_binding();	
			if (isset($error)) {
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";	
			}else{
				
				echo "<center><div class=\"bg-success  text-white\">Binding added Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: OLT and IP Pool are required fields!</div></center>";
		}
	}


	if ($ip_pool_obj->getSubmit() == "EDIT_BINDING") {
		if (!empty($ip_pool_obj->getBinding_id()) && !empty($ip_pool_obj->getId()) && !empty($ip_pool_obj->getOlt_id())) {
			$error = $ip_pool_obj->edit_binding();	
			if (isset($error)) {
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";	
			}else{
				print "<center><div class=\"bg-success  text-white\">Binding Edited Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: OLT and IP Pool are required fields! Or you are missing ID!</div></center>";
		}
	}



	if ($ip_pool_obj->getSubmit() == "DELETE_BINDING") {
		if (!empty($ip_pool_obj->getBinding_id())) {
			$error = $ip_pool_obj->delete_binding();	
			if (isset($error)){
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";	
			}else{
				echo "<center><div class=\"bg-success  text-white\">Binding Deleted Succesfully</div></center>";
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
			<h2>IP Pool Configuration</h2>
		</div>
	</div>
	<div class=row>
		<div class="col-md-8 col-md-offset-2">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<thead>
					  <tr>
						<th>Subnet</th>
						<th>Netmask</th>
						<th>Start_IP</th>
						<th>End_IP</th>
						<th>Gateway</th>
						<th>Vlan</th>
						<th>Edit</th>
					  </tr>
					</thead>
					<tbody>
					<?php 
					// BUILD EXISTING TABLE
					$rows = $ip_pool_obj->build_table_ip_pool(); 
					foreach ($rows as $row) {
						print "<tr><td>" . $row['SUBNET'] . "</td><td>" . $row['NETMASK'] . "</td><td>" . $row['START_IP'] . "</td><td>" . $row['END_IP'] . "</td><td>" . $row['GATEWAY'] . "</td><td>" . $row['VLAN'] . "</td><td><button type=\"button\" class=\"btn btn-default\" onClick=\"get_ip_pool('". $row['ID'] ."');\">EDIT</button></td></tr>";		
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
				<button type="button" class="btn btn-info" onClick="get_ip_pool();">ADD NEW IP_POOL</button>
			</div>
		</div>
	<div class="col-md-4"></div>
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog"> 
			  <!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">IP Pool</h4>
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
			<h2>Binded Pools</h2>
		</div>
	</div>
	<div class=row>
		<div class="col-md-8 col-md-offset-2">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<thead>
					  <tr>
						<th>OLT</th>
						<th>POOL</th>
						<th>SERVICE</th>
						<th>APPLY</th>
						<th>Edit</th>
					  </tr>
					</thead>
					<tbody>
					<?php 
					// BUILD EXISTING TABLE
					$rows = $ip_pool_obj->build_table_olt_ip_pool(); 
					foreach ($rows as $row) {
						print "<tr><td>" . $row['OLT_NAME'] . "</td><td>" . $row['SUBNET'] . "/" . $row['NETMASK'] . "</td><td>" . $row['SERVICES_NAME']	 . "</td><td><button type=\"button\" class=\"btn btn-default\" onClick=\"apply_pool('". $row['BINDING_ID'] ."');\">APPLY</button></td><td><button type=\"button\" class=\"btn btn-default\" onClick=\"olt_ip_pool('". $row['BINDING_ID'] ."');\">EDIT</button></td></tr>";		
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
				<button type="button" class="btn btn-info" onClick="olt_ip_pool();">ADD NEW BINDING</button>
			</div>
		</div>
	<div class="col-md-4"></div>
	<div class="modal fade" id="Modal_Binding" role="dialog">
		<div class="modal-dialog"> 
			  <!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">IP Pool Binding</h4>
					</div>
					<div class="modal-body" id="modalbody_binding">
					</div>
					<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>			
<div class="container" >
	<div id="output" class="text-center">
	</div>
</div>
