<?php
include ("common.php");
include ("classes/pon_class.php");

if ($user_class < "9")
	exit();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$pon_obj = new pon();
	$pon_obj->get_data_pon();					
	
	?>
	<form id="contact_form" action="pon.php" method="post">
	<?php if (null !== $pon_obj->getPon_id()) {
		print "<input type=\"hidden\" name=\"pon_id\" value=\"". $pon_obj->getPon_id() ."\">";	
	}
	print "<input type=\"hidden\" name=\"olt\" value=\"". $pon_obj->getOlt() ."\">";	
		?>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="name">Name*</label>
				<input type="text" name="name" class="form-control" placeholder="Name" aria-describedby="sizing-addon1" id="name" <?php if(!empty($pon_obj->getName())) print "value=\"" . $pon_obj->getName() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="slot_id">Slot_ID*</label>				
				<input type="text" name="slot_id" class="form-control" placeholder="SLOT_ID" aria-describedby="sizing-addon1" id="slot_id" <?php if(!empty($pon_obj->getSlot_id())) print "value=\"" . $pon_obj->getSlot_id() . "\" readonly"; ?>>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="port_id">Port_ID*</label>
				<input type="text" name="port_id" class="form-control" placeholder="PORT_ID" aria-describedby="sizing-addon1" id="port_id" <?php if(!empty($pon_obj->getPort_id())) print "value=\"" . $pon_obj->getPort_id() . "\" readonly"; ?> >
			</div>
		</div>
	</div>
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="cards_model_id">CARDS MODEL*</label>
				<select class="form-control" id="select-cards-model" name="cards_model_id">
					<?php if (null == $pon_obj->getPon_id()) print "<option value=\"\">Select</option>"; ?>
					<?php $rows = $pon_obj->get_Cards_model();
					foreach ($rows as $row) {
						if($pon_obj->getCards_model_id() == $row{'ID'}) {
							print "<option value=\"" . $row{'ID'} ."\" selected>" . $row{'NAME'} . "</option>";
						} else {
								if (null !== $pon_obj->getPon_id()) { 
								print "<option disabled=\"disabled\"  value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
								}else{
								print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
								}
						}
					}?>
				</select>
			</div>
		</div>
	</div>	


	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<?php
				if (null !== $pon_obj->getPon_id()) {
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