<?php

include ("common.php");
include ("navigation.php");
include ("classes/index_class.php");
include ("classes/snmp_class.php");
//header('Cache-control: private', true);

$index_obj = new index();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (!empty($index_obj->getOlt_id())) {
		$OLT_NAME = $index_obj->getOlt_name();
	}
	
	if (!empty($index_obj->getPon_id())) {
		$row = $index_obj->getPon_data();	
		$PON_NAME = $row{'NAME'};
		$SLOT_ID = $row{'SLOT_ID'};
		$PORT_ID = $row{'PORT_ID'};
	}
}
?>
<div class="container">
	<div class="text-center">
		<div class="page-header">
			<h1>Search ONUs</h1>
		</div>
	</div>
	<div class="row justify-content-md-center">
		<div class="text-center">
			<div class="form-group">
				<form class="form-inline" action="index.php" method="post">
					<div class="content">
						<label for="olt_id">OLT</label>
						<select class="form-control" id="select-olt" name="olt_id">
						<option value="" class="rhth">Select OLT</option>
						<?php
						$rows = $index_obj->get_from_olt();
						foreach ($rows as $row) { 
										print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
						}
						?>
						</select>
						<label for="pon_id">PON</label>
						<select class="form-control" id="select-pon" name="pon_id">
						<option value="">PON PORT</option></select>
						<button class="btn btn-basic" type="submit" name="SUBMIT" value="LOAD">LOAD</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="row justify-content-md-center">
		<div class="text-center">
			<div class="form-group">
				<form class="form-inline" action="index.php" method="post">
					<label for="name">Name</label>
					<input type="text" name="name"  size="15"  class="form-control" placeholder="Name"  aria-describedby="sizing-addon1">
					<label for="egn">EGN</label>
					<input type="text" name="egn"  maxlength="10" size="10" class="form-control" placeholder="EGN" aria-describedby="sizing-addon1">
					<label for="sn">SN</label>
					<input type="text" name="sn"  maxlength="15" size="15" class="form-control" placeholder="SN" aria-describedby="sizing-addon1">
					<button class="btn btn-basic"  type="submit" name="SUBMIT" value="SEARCH">SEARCH</button>
				</form>
			</div>
		</div>
	</div>
	<div class="row justify-content-md-center">
		<div class="text-center">
			<div class="form-group">
				<form class="form-inline" action="index.php" method="post">
					<button class="btn btn-basic"  type="submit" name="SUBMIT" value="UNASSIGNED">UNASSIGNED</button>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
if (!empty($index_obj->getPon_id()) || !empty($index_obj->getName()) || !empty($index_obj->getEgn()) || !empty($index_obj->getSn()) || $index_obj->getSubmit() == "UNASSIGNED") {
?>
	<div class="container">
		<div class="text-center">
			<div class="page-header">
			<?php 
			if (!empty($index_obj->getOlt_id())) 
				print "<h1>OLT: " . $OLT_NAME . "</h1><h2>PON: " . $PON_NAME . "   (" . $SLOT_ID . "/" . $PORT_ID . ")</h2><br><br>"  ;
			if (!empty($index_obj->getName())) 
				print "<h1>Name: " . $index_obj->getName() . "</h1>";
			?>
			</div>
		</div>
		<form class="form-inline"  name="myform3" action="update.php" method="post">
			<div class="row justify-content-md-center">
				<div class="table-responsive">
					<table class="table table-bordered table-condensed table-hover">
						<thead>
							<tr align=center style=font-weight:bold>
								<th><input type="checkbox" id="selectall"></th>
								<th>ONU</th>
								<th>Name</th>
								<th>Address</th>
								<th>SERVICE</th>
								<!-- <th>RF</th> -->
								<th>SN/MAC</th>
								<th>PWR</th>
								<th>STATUS</th>
								<th>LAST ONLINE</th>
								<th>OFFLINE REASON</th>
								<th>SYNC</th>
								<th>Edit</th>
							</tr>
						</thead>
						<?php
						$rows = $index_obj->build_table(); 
						foreach ($rows as $row) { 
							$snmp_obj = new snmp_oid();
							$big_onu_id = type2id($row{'SLOT_ID'}, $row{'PORT_ID'}, $row{'PON_ONU_ID'});
							$big_onu_id_2 = 10000000 * $row{'SLOT_ID'} + 100000 * $row{'PORT_ID'} + 1000 * $row{'PON_ONU_ID'} + 1;
							$big_onu_id_3 = $row{'SLOT_ID'} * 10000000 + $row{'PORT_ID'} * 100000 + $row{'PON_ONU_ID'};
							$onu_status_oid = $snmp_obj->get_pon_oid("onu_status_oid", $row{'PON_TYPE'}) . "." . $big_onu_id;
							$onu_last_online_oid = $snmp_obj->get_pon_oid("onu_last_online_oid", $row{'PON_TYPE'}) . "." . $big_onu_id;
							$onu_offline_reason_oid = $snmp_obj->get_pon_oid("onu_offline_reason_oid", $row{'PON_TYPE'}) . "." . $big_onu_id;
							$onu_sn_oid = $snmp_obj->get_pon_oid("onu_sn_oid", $row{'PON_TYPE'}) . "." . $big_onu_id;
							//GET STATUS via SNMP
							snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
							$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
							$status = $session->get($onu_status_oid);
							$power = '';
							$last_online = "Never";
							$rf_state = "";
							if ($status == '1') {
								$status = "<font color=green>Online</font>";
								//GET POWER via SNMP
								if ($row{'PON_TYPE'} == "GPON") {
									$onu_rx_power_oid = $snmp_obj->get_pon_oid("onu_rx_power_oid", $row{'PON_TYPE'}) . "." . $big_onu_id_2;
									$power = $session->get($onu_rx_power_oid);
									$power = round(($power-15000)/500,2);
								}
								if ($row{'PON_TYPE'} == "EPON") {
									$onu_rx_power_oid = $snmp_obj->get_pon_oid("onu_rx_power_oid", $row{'PON_TYPE'}) . "." . $big_onu_id_3;
									$power = $session->get($onu_rx_power_oid);
									$power = round(10*log10($power/10000),2);
								}
								if ($power < "-25") {
									$power = "<font color=red>" . $power . "</font>" ;
								} else {
									$power = "<font color=green>" . $power . "</font>" ;
								}
								/*
								if ($row{'RF'} == "1") {
									$index = $row{'SLOT_ID'} * 10000000 + $row{'PORT_ID'} * 100000 + $row{'PON_ONU_ID'} * 1000 + 162;
									$onu_rf_status_oid = $snmp_obj->get_pon_oid("onu_rf_status_oid") . "." . $index;
									snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
									$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
									$rf_state = $session->get($onu_rf_status_oid);
									if ($rf_state == "0" || $rf_state == "2") {
										$rf_state = "<img src=\"pic/off_small.png\">" ;
									}else if($rf_state == "1") {
										$rf_state = "<img src=\"pic/green_small.png\">" ;
									}
								}
								*/
							}else{
								$status = "<font color=red>Offline</font>";
								}

							//LAST ONLINE
							snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
							$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
							$last_online = $index_obj->calc_last_online($session->get($onu_last_online_oid));
							//ONU OFFLINE REASON
							snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
							$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
							$offline_reason = $session->get($onu_offline_reason_oid);
							if ($row{'PON_TYPE'} == "GPON") {
								if ($offline_reason == '1') {
									$offline_reason = "unknown(1)" ;
								} else if($offline_reason == '6') {
									$offline_reason = "dyingGaspReceived(6)" ;
								} else if($offline_reason == '12') {
									$offline_reason = "backboneFiberCut(12)" ;	
								} else if($offline_reason == '13') {
									$offline_reason = "branchFiberCut(13)" ;
								} else if($offline_reason == '7') {
									$offline_reason = "emergencyStop(7)" ;
								} else if($offline_reason == '11') {
									$offline_reason = "duplicatedOnuId(11)" ;
								} else if ($offline_reason == '10') {
									$offline_reason = "rangingFlag(10)" ;
								} else if ($offline_reason == '3') {
									$offline_reason = "hostRequest(3)" ;
								} else if ($offline_reason == '11') {
									$offline_reason = "duplicatedOnuId(11)" ;
								}
							}
							if ($row{'PON_TYPE'} == "EPON") {
								if ($offline_reason == '1') {
									$offline_reason = "unknown(1)" ;
								} else if($offline_reason == '2') {
									$offline_reason = "dyingGasp(2)" ;
								} else if($offline_reason == '3') {
									$offline_reason = "backboneFiberCut(3)" ;
								} else if($offline_reason == '4') {
									$offline_reason = "branchFiberCut(4)" ;
								} else if($offline_reason == '5') {
									$offline_reason = "oamDisconnect(5)" ;
								} else if($offline_reason == '6') {
									$offline_reason = "duplicateReg(6)" ;
								} else if ($offline_reason == '7') {
									$offline_reason = "oltDeregOperation(7)" ;
								}
													
							}
							//SYNC CHCECK
							
							if ($row{'PON_TYPE'} == "EPON") {
								snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
								$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
								$check_sn = $session->get($onu_sn_oid);
								$check_sn = trim(str_replace('Hex-STRING: ', '', $check_sn));
								$check_sn = str_replace('"', '', str_replace(' ', '', $check_sn));
							} else {
								$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
								$check_sn = $session->get($onu_sn_oid);	
							}
							//$check_sn = str_replace("52434D47","RCMG", $check_sn);
							$db_sn = $row{'SN'};
					
							
							if (strcasecmp($check_sn, $db_sn) == 0){
								$sync = "<font color=green>OK</font>" ;
							} else {
								$sync = "<font color=red>NOT OK</font>";
							}
							// PRINT TABLE
							//	print "<tr align=right><td><input type=\"checkbox\" class=\"case\" name=\"check_list[]\" value=\"" . $row{'ID'} . "\"></td><td><a href=\"customers.php?edit=1&id=".$row{'ID'}."\">".$row{'PON_ONU_ID'}."</a></td><td>".$row{'NAME'}."</td><td>".$row{'ADDRESS'}."</td><td>".$row{'ONU_NAME'}."</td><td><a href=\"onu_details.php?id=" . $row{'ID'} . "\">".$rf_state."</a></td><td>" . $db_sn ."</td><td>" . $power ."</td><td align=\"center\" style=\"vertical-align:middle\"><a href=\"onu_details.php?id=" . $row{'ID'} . "\">" . $status ."</a></td><td>" . $last_online ."</td><td>" . $offline_reason ."</td><td>" . $sync ."</td></tr>";
						
						?>
						<tr>
							<td><input type="checkbox" class="case" name="check_list[]" value="<?php echo $row{'ID'}; ?>"></td>
							<td><?php echo $row{'PON_ONU_ID'}; ?></td>
							<td><?php echo $row{'NAME'}; ?></td>
							<td><?php echo $row{'ADDRESS'}; ?></td>
							<td><?php echo $row{'SERVICE_NAME'}; ?></td>
						<!--	<td><a href="onu_details.php?id=<?php echo $row{'ID'}; ?>"><?php echo $rf_state; ?></a></td> -->
							<td><?php echo $db_sn; ?></td>
							<td><?php echo $power; ?></td>
							<td><a href="onu_details.php?id=<?php echo $row{'ID'}; ?>"><?php echo $status; ?></a></td>
							<td><?php echo $last_online; ?></td>
							<td><?php echo $offline_reason; ?></td>
							<td><?php echo $sync; ?></td>
							<td><button type="button" class="btn btn-default" onClick="getCustomer('<?php echo $row{'ID'}; ?>');">EDIT</button></td>
						</tr>
						<?php } ?>
					</table>
				</div>
			</div>
<!--
			<div class="row justify-content-md-center">
				<div class="text-center">
					<div class="form-group">
						<label for="olt_port">OLT</label>
						<select class="form-control" id="select-olt-2" name="olt_port">
							<option value="" class="rhth">Select OLT</option>
							<?php
							$rows = $index_obj->get_from_olt();
							foreach ($rows as $row) { 
								print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
							}
							?>
						</select>
						<select class="form-control" id="select-pon-2" name="pon_port">
							<option value="">PON PORT</option>
						</select>
						<button class="btn btn-info" type="submit" name="SUBMIT" value="MOVE SELECTED">MOVE SELECTED</button>					
					</div>
				</div>
			</div>
-->
		</form>
	</div>
	<div class="container">
		<div class="modal fade" id="myModal" role="dialog">
			<div class="modal-dialog"> 
				  <!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
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
