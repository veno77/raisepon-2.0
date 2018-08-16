<?php
include ("common.php");
include ("classes/olt_class.php");

if ($user_class < "9")
	exit();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$olt_obj = new olt();
	$olt_obj->get_data_olt();					
	
	?>
	<form id="contact_form" action="olt.php" method="post">
	<?php if (null !== $olt_obj->getOlt_id()) {
		print "<input type=\"hidden\" name=\"olt_id\" value=\"". $olt_obj->getOlt_id() ."\">";
		print "<input type=\"hidden\" name=\"old_ip\" value=\"". $olt_obj->getOlt_ip_address() ."\">";
	}
	?>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="name">Name*</label>
				<input type="text" name="name" class="form-control" placeholder="Name" aria-describedby="sizing-addon1" id="name" <?php if(!empty($olt_obj->getName())) print "value=\"" . $olt_obj->getName() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="olt_model">OLT MODEL*</label>
				<select class="form-control" id="select-olt-model" name="olt_model">
					<option value="" class="rhth">Select</option>
					<?php $rows = $olt_obj->get_Olt_model();
					foreach ($rows as $row) {
						if($olt_obj->getOlt_model() == $row{'ID'}) {
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
				<label for="ip_address">IP Address*</label>
				<input type="text" name="ip_address" class="form-control" placeholder="IP Address" aria-describedby="sizing-addon1" id="ip_address" <?php if(!empty($olt_obj->getOlt_ip_address())) print "value=\"" . $olt_obj->getOlt_ip_address() . "\""; ?> >
			</div>
		</div>
	</div>	
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="ro">R/O Community*</label>
				<input type="text" name="ro" class="form-control" placeholder="R/O Community" aria-describedby="sizing-addon1" id="ro" <?php if(!empty($olt_obj->getSnmp_community_ro())) print "value=\"" . $olt_obj->getSnmp_community_ro() . "\""; ?> >
			</div>
		</div>
	</div>	
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">		
				<label for="rw">R/W Community*</label>
				<input type="text" name="rw" class="form-control" placeholder="R/W Community" aria-describedby="sizing-addon1" id="rw" <?php if(!empty($olt_obj->getSnmp_community_rw())) print "value=\"" . $olt_obj->getSnmp_community_rw() . "\""; ?> >
			</div>
		</div>
	</div>	

	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<?php
				if (null !== $olt_obj->getOlt_id()) {
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