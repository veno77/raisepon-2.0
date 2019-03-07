<?php
include ("header.php");
include ("common.php");
//include ("dbconnect.php");
include ("navigation.php");
include ("classes/customers_class.php");
if ($user_class < "6")
	exit();

$customers_obj = new customers();
if ($_SERVER["REQUEST_METHOD"] == "POST") {


	//INSERT NEW CUSTOMER
	if ($customers_obj->getSubmit() == "ADD") {
		if (!empty($customers_obj->getName()) && !empty($customers_obj->getSn())) {
			$error = $customers_obj->add_customer();	
			if (!empty($error)) {
				echo $error;
			}else{
				$output = $customers_obj->update_history("add", $cur_user_id);
				echo "<center><div class=\"bg-success  text-white\">Customer added Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, ONU Model, OLT, Pon Port and SN are required fields!</div></center>";
		}
	}



	// EDIT CUSTOMER
	if ($customers_obj->getSubmit() == "EDIT") {
		if (!empty($customers_obj->getCustomers_id()) && !empty($customers_obj->getName())  && !empty($customers_obj->getSn())) {
			$error = $customers_obj->edit_customer();	
			if (!empty($error)) {
				echo $error;
			}else{
				$output = $customers_obj->update_history("Edit Customer: SERVICE " . $customers_obj->get_Service_name() . ", AUTO: " . $customers_obj->getAuto() . ", STATE: " . $customers_obj->getState(), $cur_user_id);
				echo "<center><div class=\"bg-success  text-white\">Customer edited Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, ONU Model, OLT, Pon Port and SN are required fields! Or missing Customers_ID!</div></center>";
		}
	}
		

	// DELETE CUSTOMER
	
	if ($customers_obj->getSubmit() == "DELETE") {
		if (!empty($customers_obj->getCustomers_id())) {
			$error = $customers_obj->delete_customer();
			if (!empty($error)) {
				echo $error;
			}else{
				$output = $customers_obj->update_history("delete", $cur_user_id);
				echo "<center><div class=\"bg-success  text-white\">Customer Deleted Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Missing Customers_ID!</div></center>";
		}	
			
		

		
	}
}


$rows = $customers_obj->get_Illegal_onus();
?>
<div class="container">
<div class="text-center">
	<div class="page-header">
	
		<?php 	print "<h2>Illegal ONUs</h2>"; ?>
	</div>
</div>
<div class=row>
	<div class="text-center">
		<div class="table-responsive">
			<table class="table table-bordered table-condensed table-hover">
				<thead>
					<tr align=center style=font-weight:bold>
						<th>OLT</th>
						<th>MAC_ADDRESS/SN</th>
						<th>PON PORT</th>
						<th>TIME</th>
						<th>ADD</th>
					</tr>
				</thead>
				<?php
				foreach ($rows as $k => $v) {
					$olt_model = $customers_obj->get_Olt_model($k);
					if ($v) { 
						foreach ($olt_model as $row) {
							$olt_name = $row['NAME'];
						}
						foreach ($v as $roww) {
							$pon_port = $customers_obj->get_Pon_port($k, $roww{'1'},$roww{'2'});
							foreach ($pon_port as $pon) {
								$pon_id = $pon{'ID'};
							}
							$onu_id = $customers_obj->check_Sn($roww{'0'});
							if (isset($onu_id)) {
								print "<tr><td>" . $olt_name . "</td><td>" . $roww{'0'} . "</td><td>" . $roww{'1'} . "/"  . $roww{'2'} . "</td><td>" . $roww{'3'} . "</td><td><button type=\"button\" class=\"btn btn-default\" onClick=\"editCustomer('" . $k . "','" . $pon_id . "','" . $onu_id . "');\">EDIT</button></td></tr>";
							} else {
								print "<tr><td>" . $olt_name . "</td><td>" . $roww{'0'} . "</td><td>" . $roww{'1'} . "/"  . $roww{'2'} . "</td><td>" . $roww{'3'} . "</td><td><button type=\"button\" class=\"btn btn-default\" onClick=\"addCustomer('" . $k . "','" . $pon_id . "','" . $roww{'0'} . "');\">ADD</button></td></tr>";
							}
						}
					}
				}?>					
			</table>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<button type="button" class="btn btn-info" onClick="getCustomer();">ADD NEW CUSTOMER</button>
		</div>
	</div>
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog"> 
			  <!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">ONU</h4>
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

