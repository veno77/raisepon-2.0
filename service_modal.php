<?php
include ("common.php");
include ("classes/services_class.php");

if ($user_class < "9")
	exit();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$services_obj = new services();
	$services_obj->get_data_services();					
	?>
	<form id="contact_form" action="services.php" method="post">
	<?php if (null !== $services_obj->getService_id()) {
		print "<input type=\"hidden\" name=\"service_id\" value=\"". $services_obj->getService_id() ."\">";
	}
	?>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="name">Name</label>
				<input type="text" name="name" class="form-control" placeholder="Name" aria-describedby="sizing-addon1" id="name" <?php if(null !== $services_obj->getName()) print "value=\"" . $services_obj->getName() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="line_profile_id">LINE PROFILE*</label>
				<select class="form-control" id="select-line-profile" name="line_profile_id">
					<option value="" class="rhth">Select</option>
					<?php $rows = $services_obj->get_Line_profile_info();
					foreach ($rows as $row) {
						if($services_obj->getLine_profile_id() == $row{'ID'}) {
							print "<option value=\"" . $row{'ID'} ."\" selected>" . $row{'NAME'} . "</option>";
						} else {
								print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
						}
					}?>
				</select>
			</div>
		</div>
	</div>		
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="service_profile_id">SERVICE PROFILE*</label>
				<select class="form-control" id="select-service-profile" name="service_profile_id">
					<option value="" class="rhth">Select</option>
					<?php $rows = $services_obj->get_Service_profile_info();
					foreach ($rows as $row) {
						if($services_obj->getService_profile_id() == $row{'ID'}) {
							print "<option value=\"" . $row{'ID'} ."\" selected>" . $row{'NAME'} . "</option>";
						} else {
								print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
						}
					}?>
				</select>
			</div>
		</div>
	</div>

	<div class=row>
		<div class="form-group">
			<div class="col-md-4 col-md-offset-4">
				<?php
				$edit = (isset($_GET['edit']) ? $_GET['edit'] : null);
				if ($edit == "1" || null !== $services_obj->getService_id()) {
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