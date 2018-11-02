<?php
include ("header.php");
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
		$PON_TYPE = $row{'PON_TYPE'};
	}
	
	
}else{
?>
<div class="container">
	<div class="text-center">
		<div class="page-header">
			<h2>Search ONUs</h2>
		</div>
	</div>
	<div class="row justify-content-md-center">
		<div class="text-center">
			<div class="form-group">
				<form class="form-inline" id="load" method="post">
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
						<input type="hidden" name="SUBMIT" value="LOAD">
						<button class="btn btn-basic" type="button" onClick="LoadIndex();">LOAD</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="row justify-content-md-center">
		<div class="text-center">
			<div class="form-group">
				<form class="form-inline" id="search" method="post">
					<label for="name">Name</label>
					<input type="text" name="name"  size="15"  class="form-control" placeholder="Name"  aria-describedby="sizing-addon1">
					<label for="egn">EGN</label>
					<input type="text" name="egn"  maxlength="10" size="10" class="form-control" placeholder="EGN" aria-describedby="sizing-addon1">
					<label for="sn">SN</label>
					<input type="text" name="sn"  maxlength="15" size="15" class="form-control" placeholder="SN" aria-describedby="sizing-addon1">
					<input type="hidden" name="SUBMIT" value="SEARCH">
					<button class="btn btn-basic"  type="button" onClick="SearchIndex();">SEARCH</button>
				</form>
			</div>
		</div>
	</div>
	<div class="row justify-content-md-center">
		<div class="text-center">
			<div class="form-group">
				<form class="form-inline" id="unassigned" method="post">
					<input type="hidden" name="SUBMIT" value="UNASSIGNED">
					<button class="btn btn-basic"  type="button" onClick="UnassignedIndex();">UNASSIGNED</button>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
}
?>
<div class="container" >
	<div id="output" class="text-center">
		<?php
		if (!empty($index_obj->getPon_id()) || !empty($index_obj->getName()) || !empty($index_obj->getEgn()) || !empty($index_obj->getSn()) || $index_obj->getSubmit() == "UNASSIGNED") {
		?>
			<div class="page-header">
			<?php 
			if (!empty($index_obj->getOlt_id())) 
				print "<h2>OLT: " . $OLT_NAME . "</h2><h3>PON: " . $PON_NAME . "   (" . $SLOT_ID . "/" . $PORT_ID . ")</h3><br><br>"  ;
			if (!empty($index_obj->getName())) 
				print "<h2>Name: " . $index_obj->getName() . "</h2>";
			?>
			</div>
		</div>
		<!--	<form class="form-inline"  name="myform3" action="update.php" method="post"> -->
		<div class="row justify-content-md-center">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed table-hover">
					<thead>
						<tr align=center style=font-weight:bold>
						<!--		<th><input type="checkbox" id="selectall"></th> -->
							<th>ONU</th>
							<th>Name</th>
							<th>Address</th>
							<th>SERVICE</th>
							<!-- <th>RF</th> -->
							<th>SN/MAC</th>
							<th>PWR<br>(db)</th>
							<th>DIST<br>(m)</th>
							
							<th>STATUS</th>
							<!--<th>LAST ONLINE</th> -->
							<th>OFFLINE<br>REASON</th>
							<th>INFO</th>
							<th>SYN</th>
							<th>EDIT</th>
						</tr>
					</thead>
				<?php
				$rows = $index_obj->build_table(); 
				if(!empty($rows)) {
					foreach ($rows as $row) { 
						if (isset($row{'PON_TYPE'})) {
							$snmp_obj = new snmp_oid();
							$big_onu_id = type2id($row{'SLOT_ID'}, $row{'PORT_ID'}, $row{'PON_ONU_ID'});
							if ($row{'PON_ONU_ID'} < 100) {
								$big_onu_id_rx_gpon = 10000000 * $row{'SLOT_ID'} + 100000 * $row{'PORT_ID'} + 1000 * $row{'PON_ONU_ID'} + 1;
							}else{
								$big_onu_id_rx_gpon = (3<<28)+(10000000 * $row{'SLOT_ID'} + 100000 * $row{'PORT_ID'} + 1000 * ($row{'PON_ONU_ID'}%100) + 1);
							}
							$big_onu_id_3 = $row{'SLOT_ID'} * 10000000 + $row{'PORT_ID'} * 100000 + $row{'PON_ONU_ID'};
							
							$onu_status_oid = $snmp_obj->get_pon_oid("onu_status_oid", $row{'PON_TYPE'}) . "." . $big_onu_id;
							$onu_last_online_oid = $snmp_obj->get_pon_oid("onu_last_online_oid", $row{'PON_TYPE'}) . "." . $big_onu_id;
							$onu_offline_reason_oid = $snmp_obj->get_pon_oid("onu_offline_reason_oid", $row{'PON_TYPE'}) . "." . $big_onu_id;
							$onu_sn_oid = $snmp_obj->get_pon_oid("onu_sn_oid", $row{'PON_TYPE'}) . "." . $big_onu_id;
							if ($row{'PON_TYPE'} == "GPON")
								$onu_register_distance_oid = $snmp_obj->get_pon_oid("onu_register_distance_oid", $row{'PON_TYPE'}) . "." . $big_onu_id;
							$dot3MpcpRoundTripTime = $snmp_obj->get_pon_oid("dot3MpcpRoundTripTime", "OLT") . "." . $big_onu_id;
							//GET STATUS via SNMP
							snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
							$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
							$status = $session->get($onu_status_oid);
							$power = '';
							$last_online = "Never";
							$rf_state = "";
							$onu_register_distance = "";
							if ($status == '1') {
								$status = "<font color=green>Online</font>";
								//GET POWER/DISTANCE via SNMP
								if ($row{'PON_TYPE'} == "GPON") {
									$onu_rx_power_oid = $snmp_obj->get_pon_oid("onu_rx_power_oid", $row{'PON_TYPE'}) . "." . $big_onu_id_rx_gpon;
									$power = $session->get($onu_rx_power_oid);
									if ($power > 32767)
										$power = $power - 65535 - 1;
									$power = round(($power-15000)/500,2);
									
									
									$onu_register_distance = $session->get($onu_register_distance_oid);
								}
								if ($row{'PON_TYPE'} == "EPON") {
									$onu_rx_power_oid = $snmp_obj->get_pon_oid("onu_rx_power_oid", $row{'PON_TYPE'}) . "." . $big_onu_id_3;
									$power = $session->get($onu_rx_power_oid);
									$power = round(10*log10($power/10000),2);
									
									$dot3MpcpRoundTripTime = $session->get($dot3MpcpRoundTripTime);
									if ($dot3MpcpRoundTripTime <= '46')
										$onu_register_distance = '1';
									if ($dot3MpcpRoundTripTime > '46')
										$onu_register_distance = ($dot3MpcpRoundTripTime - 46)*1.6;
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
							}else if($status == '2'){
								$status = "<font color=blue>Pending</font>";
							}else if($status == '3'){
								$status = "<font color=red>Offline</font>";
							}

							//LAST ONLINE
							//snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
							//$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
							//$last_online = $index_obj->calc_last_online($session->get($onu_last_online_oid));
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
						}else{
							$check_sn = NULL;
							$rf_state = NULL;
							$power = NULL;
							$status = NULL;
							$last_online = NULL;
							$offline_reason = NULL;
							$db_sn = $row{'SN'};
							$sync = NULL;
						}	
						?>
	
						<tr align=right>
							<!-- <td><input type="checkbox" class="case" name="check_list[]" value="<?php echo $row{'ID'}; ?>"></td> -->
							<td><?php if ($index_obj->getSubmit() == "SEARCH"){echo $row{'OLT_NAME'} . "/" . $row{'SLOT_ID'} . "/" . $row{'PORT_ID'} . "/"	;} echo $row{'PON_ONU_ID'}; ?></td>
							<td><?php echo $row{'NAME'}; ?></td>
							<td><?php echo $row{'ADDRESS'}; ?></td>
							<td><?php echo $row{'SERVICE_NAME'}; ?></td>
							<!--	<td><a href="onu_details.php?id=<?php echo $row{'ID'}; ?>"><?php echo $rf_state; ?></a></td> -->
							<td><?php echo $db_sn; ?></td>
							<td><?php echo $power; ?></td>
							<?php echo "<td>" . round($onu_register_distance) . "</td>"; ?>
							<td><?php echo $status; ?></td>
							<!--	<td><?php echo $last_online; ?></td> -->
							<td><?php echo $offline_reason; ?></td>
							<td><?php if ($index_obj->getSubmit() != "UNASSIGNED") { echo "<a href=\"onu_details.php?id=" . $row{'ID'} . "\">";} ?><button type="button" class="btn btn-default">INFO</button></а></td>
							<td><?php echo $sync; ?></td>
							<td><button type="button" class="btn btn-default" onClick="getCustomer('<?php echo $row{'ID'}; ?>');">EDIT</button></td>
						</tr>
					<?php }
				} ?>
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
	</form>
	-->
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
