<?php
include ("classes/customers_class.php");
$customers_obj = new customers();
?>
<script type="text/javascript" language="javascript">
	$(function() {
        $("#select-olt2").change(function() {
                $("#select-pon2").load("get.php?choice=" + $("#select-olt2").val());
        });
	});
/*	$(function() {
		$("#auto").click(function() {
		   if ($(this).is(":checked")) { 
			  $("#select-olt2").prop("disabled", true);
			  $("#select-pon2").prop("disabled", true);
		   } else {
			  $("#select-olt2").prop("disabled", false);  
			  $("#select-pon2").prop("disabled", false);  
		   }
		});
	});
*/
</script>
<form id="contact_form" action="customers.php" method="post">
<?php if (null !== $customers_obj->getCustomers_id()) {
	$customers_obj->get_data_customer();					
	print "<input type=\"hidden\" name=\"customers_id\" value=\"". $customers_obj->getCustomers_id() ."\">";
	print "<input type=\"hidden\" name=\"old_olt\" value=\"". $customers_obj->getOld_olt() ."\">";
	print "<input type=\"hidden\" name=\"old_pon_port\" value=\"". $customers_obj->getOld_pon_port() ."\">";
	print "<input type=\"hidden\" name=\"old_pon_onu_id\" value=\"". $customers_obj->getOld_pon_onu_id() ."\">";
	print "<input type=\"hidden\" name=\"old_ports\" value=\"". $customers_obj->getOld_ports() ."\">";
	print "<input type=\"hidden\" name=\"old_state\" value=\"". $customers_obj->getOld_state() ."\">";
	if (!empty($_POST['submit_page']) && $_POST['submit_page'] == 'LOAD'){
		echo '<input type="hidden" name="online" value="'.$_POST['online'].'">';
		echo '<input type="hidden" name="offline" value="'.$_POST['offline'].'">';
		echo '<input type="hidden" name="pending" value="'.$_POST['pending'].'">';
		echo '<input type="hidden" name="submit_page" value="'.$_POST['submit_page'].'">';
	}elseif (!empty($_POST['submit_page']) && $_POST['submit_page'] == 'SEARCH'){
		echo '<input type="hidden" name="name" value="'. $customers_obj->getName() .'">';
		echo '<input type="hidden" name="address" value="'.$customers_obj->getAddress().'">';
		echo '<input type="hidden" name="egn" value="'.$customers_obj->getEgn().'">';
		echo '<input type="hidden" name="sn" value="'.$customers_obj->getSn().'">';
		echo '<input type="hidden" name="submit_page" value="'.$_POST['submit_page'].'">';
	}
	if (!empty($customers_obj->getOld_onu_ip_address()))
		print "<input type=\"hidden\" name=\"old_onu_ip_address\" value=\"". $customers_obj->getOld_onu_ip_address() ."\">"; 
	print "<input type=\"hidden\" name=\"state_rf\" value=\"". $customers_obj->getState_rf() ."\">";	
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
			<select class="form-control" id="select-olt2" name="olt">
				<?php 
				if (null == $customers_obj->getOlt())
					print "	<option value=\"\">Select</option>";
				$rows = $customers_obj->get_Olt_models();
				foreach ($rows as $row) {
					if($customers_obj->getOld_olt() == $row['ID'] && $customers_obj->getOlt() == null) {
						print "<option value=\"" . $row['ID'] ."\" selected>" . $row['NAME'] . "</option>";
					} else if($customers_obj->getOlt() == $row['ID'] ) {
						print "<option value=\"" . $row['ID'] ."\" selected>" . $row['NAME'] . "</option>";
					} else {
						print "<option value=\"" . $row['ID'] ."\">" . $row['NAME'] . "</option>";
					}
				}					
				?>
			</select>
		</div>
	</div>
	<div class="col-md-4"> 
		<div class="form-group">
			<label for="pon_port">PON PORT*</label>
			<select class="form-control" id="select-pon2" name="pon_port" >
				<?php if (null !== $customers_obj->getCustomers_id() || null !== $customers_obj->getPon_port() || null !== $customers_obj->getOld_pon_port()) {
				//	echo $customers_obj->getOld_olt();
					$rows = $customers_obj->get_Pon_ports();
					foreach ($rows as $row) {
						if($customers_obj->getOld_pon_port() == $row['ID'] && $customers_obj->getPon_port() == null) {
							print "<option value=\"" . $row['ID'] ."\" selected>" . $row['NAME'] ." === ". $row['SLOT_ID'] ."/" . $row['PORT_ID'] ."</option>";
						}else if($customers_obj->getPon_port() == $row['ID']) {
							print "<option value=\"" . $row['ID'] ."\" selected>" . $row['NAME'] ." === ". $row['SLOT_ID'] ."/" . $row['PORT_ID'] ."</option>";
						} else {
							print "<option value=\"" . $row['ID'] ."\">" . $row['NAME'] ." === ". $row['SLOT_ID'] ."/" . $row['PORT_ID'] ."</option>";
						}				
					}
				} else {
					print "<option value=\"\" >Select OLT</option>";
				} ?>
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
						if($customers_obj->getOldservice() == $row['ID']) {
							print "<option value=\"" . $row['ID'] ."\" selected>" . $row['NAME'] ." === ". $row['ID'] ."</option>";
						} else {
							print "<option value=\"" . $row['ID'] ."\">" . $row['NAME'] ." === ". $row['ID'] ."</option>";
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
<div class="row">
	<div class="col-md-4 col-md-offset-2">
		<div class="form-group">
			<label><input type="checkbox" id="auto" name="auto" value="YES"<?php if($customers_obj->getAuto() == "YES") print "checked" ?>> AUTO</label>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
		<?php
		if (null !== $customers_obj->getCustomers_id()) {
		?>
			<label><input type="checkbox" id="state" name="state" value="YES"<?php if($customers_obj->getOld_state() == "YES") print "checked" ?>> ACTIVE</label> 
		<?php }else{ ?>
			<label><input type="checkbox" id="state" name="state" value="YES" checked> ACTIVE</label> 
		<?php } ?>
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

		
