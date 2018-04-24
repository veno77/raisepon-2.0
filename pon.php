<?php
include ("header.php");
include ("common.php");
include ("dbconnect.php");
include ("navigation.php");
include ("classes/pon_class.php");
include ("classes/snmp_class.php");

if ($user_class < "6")
	exit();

$pon_obj = new pon();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// ADD PON PORT
	if ($pon_obj->getSubmit() == "ADD") {
		if (!empty($pon_obj->getName()) && !empty($pon_obj->getSlot_id()) && !empty($pon_obj->getPort_id()) && !empty($pon_obj->getCards_model_id ())) {
			$error = $pon_obj->create_pon();	
			if (isset($error)) {
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";	
			}else{
				echo "<center><div class=\"bg-success  text-white\">PON Port added Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, SLOT_ID, PORT_ID and MODEL_CARD are required fields!</div></center>";
		}
	}

	// EDIT OLT
	if ($pon_obj->getSubmit() == "EDIT") {
		if (!empty($pon_obj->getPon_id()) && !empty($pon_obj->getName()) && !empty($pon_obj->getSlot_id()) && !empty($pon_obj->getPort_id()) && !empty($pon_obj->getCards_model_id ())) {
			$error = $pon_obj->edit_pon();
			if (isset($error)) {
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";
			}else{
				print "<center><div class=\"bg-success  text-white\">PON Edited Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, SLOT_ID, PORT_ID and MODEL_CARD are required fields! Or you are missing PON_ID!</div></center>";
		}
	}
	
	// DELETE OLT
	if ($pon_obj->getSubmit() == "DELETE") {
		if (!empty($pon_obj->getPon_id())) {
		$error = $pon_obj->delete_pon();
			if (isset($error)) {
				echo "<center><div class=\"bg-danger text-white\">" . $error . "</div></center>";	
			}else{
				print "<center><div class=\"bg-success  text-white\">PON Port Deleted Succesfully</div></center>";

			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: PON_ID missing!</div></center>";
		
		}
	}
}
?>


<div class="container">
	<div class="text-center">
		<div class="page-header">
			<h2>PON Configuration</h2>
		</div>
	</div>
	<div class="row justify-content-md-center">
		<div class="text-center">
			<div class="form-group">
				<form class="form-inline" action="pon.php" method="post">
					<div class="content">
						<label for="olt_id">OLT</label>
						<select class="form-control" id="select-olt" name="olt">
						<option value="" class="rhth">Select OLT</option>
						<?php
						$rows = $pon_obj->get_from_olt();
						foreach ($rows as $row) { 
										print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
						}
						?>
						</select>
						<button class="btn btn-basic" type="submit" name="SUBMIT" value="LOAD">LOAD</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
if (!empty($pon_obj->getOlt())) {
?>
<div class="container">
	<div class="text-center">
		<h2>OLT: <?php echo $pon_obj->getOlt_name(); ?></h2>
	</div>
	<div class=row>
		<div class="text-center">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed table-hover">
					<thead>
					  <tr>
						<th>Name</th>
						<th>SLOT_ID</th>
						<th>PORT_ID</th>
						<th>CARDS_MODEL</th>
						<th>EDIT</th>
					  </tr>
					</thead>
					<tbody>
					<?php
					$rows = $pon_obj->build_table_pon(); 
					foreach ($rows as $row) {
												?>
						<tr>
							<td><?php echo $row{'NAME'}; ?></td>
							<td><?php echo $row{'SLOT_ID'}; ?></td>
							<td><?php echo $row{'PORT_ID'}; ?></td>
							<td><?php echo $row{'CARDS_MODEL_NAME'}; ?></td>
							<td><button type="button" class="btn btn-default" onClick="getPon('<?php echo $row{'ID'}; ?>','<?php echo $row{'OLT'}; ?>');">EDIT</button></td>
						</tr>
						<?php
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="text-center">
				<button type="button" class="btn btn-info" onClick="addPon('<?php echo $pon_obj->getOlt(); ?>');">ADD NEW PON</button>
		</div>
	</div>
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog"> 
			  <!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">PON</h4>
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
<?php
}
?>
