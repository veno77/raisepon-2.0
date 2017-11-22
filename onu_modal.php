<?php
include ("common.php");
include ("classes/onu_class.php");

if ($user_class < "9")
	exit();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$onu_obj = new onu();
	$onu_obj->get_data_onu();					
	
	?>
	<form id="contact_form" action="onu.php" method="post">
	<?php if (null !== $onu_obj->getOnu_id())
		print "<input type=\"hidden\" name=\"onu_id\" value=\"". $onu_obj->getOnu_id() ."\">";
	?>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="name">Name</label>
				<input type="text" name="name" class="form-control" placeholder="Name" aria-describedby="sizing-addon1" id="name" <?php if(isset($onu_obj->name)) print "value=\"" . $onu_obj->name . "\""; ?> >
			</div>
		</div>
	</div>
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="ports">Ports</label>
				<input type="text" name="ports" class="form-control" placeholder="Ports" aria-describedby="sizing-addon1" id="ports" <?php if(isset($onu_obj->ports)) print "value=\"" . $onu_obj->ports . "\""; ?> >
			</div>
		</div>
	</div>	
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<div class="checkbox">
					<label class="checkbox-inline"><input type="checkbox" name="rf" value="1" <?php if($onu_obj->getRf() == "1") print " checked"; ?> >RF</label>
					<label class="checkbox-inline"><input type="checkbox" name="pse" value="1" <?php if($onu_obj->getPse() == "1") print " checked"; ?> >PSE</label>
					<label class="checkbox-inline"><input type="checkbox" name="hgu" value="1" <?php if($onu_obj->getHgu() == "1") print " checked"; ?> >HGU</label>
				</div>
				<div class="radio">
					<label class="radio-inline"><input type="radio" name="pon_type" value="GPON" <?php if($onu_obj->getPon_type() == "GPON") print " checked"; ?>>GPON</label>
					<label class="radio-inline"><input type="radio" name="pon_type" value="EPON" <?php if($onu_obj->getPon_type() == "EPON") print " checked"; ?>>EPON</label>
				</div>
			</div>
		</div>
	</div>	

	<div class=row>
		<div class="form-group">
			<div class="col-md-4 col-md-offset-4">
				<?php
				$edit = (isset($_GET['edit']) ? $_GET['edit'] : null);
				if ($edit == "1" || null !== $onu_obj->getOnu_id()) {
				?>
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="EDIT">EDIT</button> 
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="DELETE">DELETE</button>
				<?php }else{ ?>
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="ADD">ADD</button>
				<?php } ?>
			</div>
		</div>
	</div>

		
<?php
}
?>