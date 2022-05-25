<?php
require_once("classes/olt_class.php");
require_once("classes/pon_class.php");
require_once("classes/customers_class.php");

$onu_data = array();
$pon_onu_id = array();
if (!empty($_POST['data'])) {
	$onu_data = $_POST['data'];

	$olt_obj = new olt();
	$pon_obj = new pon();

	$olt_obj->get_data_olt();
	$olt_name = $olt_obj->getName();

	$pon_obj->get_data_pon();
	$pon_name = $pon_obj->getName();
?>
<div class="form-group"> 
	<form class="form-inline" id="confirm_delete" method="post">

	<?php
	print "<input type=\"hidden\" name=\"olt_id\" class=\"form-control\" value=\"". $_POST['olt_id'] ."\">";
	print "<input type=\"hidden\" name=\"pon_id\" class=\"form-control\" value=\"". $_POST['pon_id'] ."\">";
	foreach ($onu_data as $onu){
		$customers_obj = new customers();
		$customers_obj->setCustomer_id($onu);
		$customers_obj->get_data_customer();
		array_push($pon_onu_id,$customers_obj->getOld_pon_onu_id());
		print "<input type=\"hidden\" name=\"data[]\" value=\"". $onu ."\">";

	}

	?>
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="form-group">
				<?php
				print "<div>Are you sure you want to delete ONU:</div><div>";
				foreach($pon_onu_id as $onu_id){
				print $onu_id . ",";
				} 
				print "</div><div>on OLT: <b>" . $olt_name . "</b>     PON: <b>" . $pon_name . "</div>";
				?>
			</div>
		</div>
	</div>
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<input type="hidden" name="SUBMIT" value="CONFIRM_DELETE">
				<button class="btn btn-basic" type="button" onClick="ConfirmDelete();">CONFIRM</button> 
			</div>
		</div>
	</div>
	</form>
</div>
<?php
}else{
	print "NO ONU(S) SELECTED for DELETE! PLEASE SELECT ONU(S) TO BE DELETED";
}
?>
