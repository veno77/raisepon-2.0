<?php
include ("common.php");
include ("classes/service_profile_class.php");

if ($user_class < "9")
	exit();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$service_profile_obj = new service_profile();
	$service_profile_obj->get_data_service_profile();					
	?>
	<form id="contact_form" action="service_profile.php" method="post">
	<?php if (null !== $service_profile_obj->getId()) {
		print "<input type=\"hidden\" name=\"id\" value=\"". $service_profile_obj->getId() ."\">";
	}
	?>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="name">Name</label>
				<input type="text" name="name" class="form-control" placeholder="Name" aria-describedby="sizing-addon1" id="name" <?php if(null !== $service_profile_obj->getName()) print "value=\"" . $service_profile_obj->getName() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="ports">Ports</label>
				<input type="text" name="ports" class="form-control" placeholder="Ports" aria-describedby="sizing-addon1" id="ports" <?php if(null !== $service_profile_obj->getPorts()) print "value=\"" . $service_profile_obj->getPorts() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="service_profile_id">Service_Profile_Id</label>
				<input type="text" name="service_profile_id" class="form-control" placeholder="Service_Profile_Id" aria-describedby="sizing-addon1" id="service_profile_id" <?php if(null !== $service_profile_obj->getService_profile_id()) print "value=\"" . $service_profile_obj->getService_profile_id() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-2 col-md-offset-4">
			<div class="form-group">
				<label><input type="checkbox" id="hgu" name="hgu" value="Yes"<?php if($service_profile_obj->getHgu() == "Yes") print "checked"; ?>> HGU</label>
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<label><input type="checkbox" id="rf" name="rf" value="Yes"<?php if($service_profile_obj->getRf() == "Yes") print "checked"; ?>> RF</label>
			</div>
		</div>
	</div>

	<div class=row>
		<div class="form-group">
			<div class="col-md-4 col-md-offset-4">
				<?php
				$edit = (isset($_GET['edit']) ? $_GET['edit'] : null);
				if ($edit == "1" || null !== $service_profile_obj->getId()) {
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