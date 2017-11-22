<?php
include ("common.php");
include ("classes/customers_class.php");



	$customers_obj = new customers();
	?>
	<form id="contact_form" action="customers.php" method="post">
	<?php if (null !== $customers_obj->getCustomers_id()) {
		$customers_obj->get_data_customer();					
		print "<input type=\"hidden\" name=\"customers_id\" value=\"". $customers_obj->getCustomers_id() ."\">";
		print "<input type=\"hidden\" name=\"old_olt\" value=\"". $customers_obj->getOld_olt() ."\">";
		print "<input type=\"hidden\" name=\"old_pon_port\" value=\"". $customers_obj->getOld_pon_port() ."\">";
		print "<input type=\"hidden\" name=\"old_pon_onu_id\" value=\"". $customers_obj->getOld_pon_onu_id() ."\">";
		print "<input type=\"hidden\" name=\"old_ports\" value=\"". $customers_obj->getOld_ports() ."\">";
	}
	?>
	<div class="row">
		<div class="col-md-4 col-md-offset-2">
			<div class="form-group">
				<label for="name">Name*</label>
				<input type="text" name="name" class="form-control" placeholder="Name" aria-describedby="sizing-addon1" id="name" <?php if(!empty($customers_obj->getName())) print "value=\"" . $customers_obj->getName() . "\""; ?> >
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label for="egn">EGN</label>
				<input type="text" name="egn" class="form-control" placeholder="EGN" aria-describedby="sizing-addon1" id="egn" <?php if(!empty($customers_obj->getEgn())) print "value=\"" . $customers_obj->getEgn() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="form-group">
				<label for="address">Address</label>
				<input type="text" name="address" class="form-control" placeholder="Address" aria-describedby="sizing-addon1" id="address" <?php if(!empty($customers_obj->getAddress())) print "value=\"" . $customers_obj->getAddress() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-2">
			<div class="form-group">
				<label for="olt">OLT*</label>
				<select class="form-control" id="select-olt" name="olt">";
					<?php 
					if (null == $customers_obj->getCustomers_id() && null == $customers_obj->getOld_olt())
						print "	<option value=\"\">Select</option>";
					$rows = $customers_obj->get_Olt_model("");
					foreach ($rows as $row) {
						if($customers_obj->getOld_olt() == $row{'ID'}) {
							print "<option value=\"" . $row{'ID'} ."\" selected>" . $row{'NAME'} . "</option>";
						} else {
							if (null !== $customers_obj->getCustomers_id() || null !== $customers_obj->getOld_olt()) {
								print "<option disabled=\"disabled\" value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
							}else{
								print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
							}
						}
					}					
					?>
				</select>
			</div>
		</div>
		<div class="col-md-4"> 
			<div class="form-group">
				<label for="pon_port">PON PORT*</label>
				<select class="form-control" id="select-pon" name="pon_port">
					<?php if (null !== $customers_obj->getCustomers_id() || null !== $customers_obj->getOld_pon_port()) {
					//	echo $customers_obj->getOld_olt();
						$rows = $customers_obj->get_Pon_ports();
						foreach ($rows as $row) {
							if($customers_obj->getOld_pon_port() == $row{'ID'}) {
								print "<option value=\"" . $row{'ID'} ."\" selected>" . $row{'NAME'} ." === ". $row{'SLOT_ID'} ."/" . $row{'PORT_ID'} ."</option>";
							} else {
								print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} ." === ". $row{'SLOT_ID'} ."/" . $row{'PORT_ID'} ."</option>";
							}				
						}
					} else {
						print "<option value=\"\" >Select OLT</option>";
					}?>
				</select>
			</div>
		</div>	
	</div>	
	<div class="row">
		<div class="col-md-4 col-md-offset-2">
			<div class="form-group">
				<label for="service">SERVICE:</label>
				<select class="form-control" id="service" name="service">
					<option value="" class="rhth">Select</option>
					<?php $rows = $customers_obj->get_Service();
						foreach ($rows as $row) {
							if($customers_obj->getOldservice() == $row{'ID'}) {
								print "<option value=\"" . $row{'ID'} ."\" selected>" . $row{'NAME'} ." === ". $row{'ID'} ."</option>";
							} else {
								print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} ." === ". $row{'ID'} ."</option>";
							}				
						}
					?>
				</select>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label for="sn">SN/MAC*</label>
				<input type="text" name="sn" maxlength="17" size="17" class="form-control" placeholder="SN/MAC" aria-describedby="sizing-addon1" id="sn" <?php if(!empty($customers_obj->getSn())) print "value=\"" . $customers_obj->getSn() . "\""; ?> >
			</div>
		</div>
	</div>
	
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<?php
				if (null !== $customers_obj->getCustomers_id()) {
				?>
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="EDIT">EDIT</button> 
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="DELETE">DELETE</button>
				<?php }else{ ?>
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="ADD">ADD</button>
				<?php } ?>
			</div>
		</div>
	</div>

		
