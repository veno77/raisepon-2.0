<?php
include ("common.php");
include ("classes/ip_pool_class.php");
include ("classes/customers_class.php");

if ($user_class < "9")
	exit();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$ip_pool_obj = new ip_pool();
	$ip_pool_obj->get_data_olt_ip_pool();
	$customers_obj = new customers();
	?>
	<form id="contact_form" action="ip_pool.php" method="post">
	<?php if (null !== $ip_pool_obj->getBinding_id()) {
		print "<input type=\"hidden\" name=\"binding_id\" value=\"". $ip_pool_obj->getBinding_id() ."\">";
	}
	?>
<div class="row">
	<div class="col-md-4 col-md-offset-1">
		<div class="form-group">
			<label for="OLT">OLT</label>
			<select class="form-control" id="select-olt2" name="olt_id">
				<?php 
				print "	<option value=\"\">Select</option>";
				$rows = $customers_obj->get_Olt_models();
				foreach ($rows as $row) {
					if($ip_pool_obj->getOlt_id() == $row{'ID'}) {
						print "<option value=\"" . $row{'ID'} ."\" selected>" . $row{'NAME'} . "</option>";
					} else {
						print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
					}
				}					
				?>
			</select>
		</div>
	</div>
	<div class="col-md-6"> 
		<div class="form-group">
			<label for="ip_pool">IP_POOL</label>
			<select class="form-control" id="select-ip-pool" name="id">
				<?php 
				print "	<option value=\"\">Select</option>";
				$rows = $ip_pool_obj->get_IP_pools();
				foreach ($rows as $row) {
					if($ip_pool_obj->getId() == $row{'ID'}) {
						print "<option value=\"" . $row{'ID'} ."\" selected>" . $row{'SUBNET'} . "/" . $row{'NETMASK'} . "</option>";
					} else {
						print "<option value=\"" . $row{'ID'} ."\">" . $row{'SUBNET'} . "/" . $row{'NETMASK'} ."</option>";
					}
				}					
				?>
			</select>
		</div>
	</div>	
</div>	
	<div class=row>
		<div class="form-group">
			<div class="col-md-4 col-md-offset-4">
				<?php
				$edit = (isset($_GET['edit']) ? $_GET['edit'] : null);
				if ($edit == "1" || null !== $ip_pool_obj->getId()) {
				?>
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="EDIT_BINDING">EDIT</button> 
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="DELETE_BINDING">DELETE</button>
				<?php }else{ ?>
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="ADD_BINDING">ADD</button>
				<?php } ?>
			</div>
		</div>
	</div>

		
<?php
}
?>