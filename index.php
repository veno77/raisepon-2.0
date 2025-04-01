<?php
require_once("common.php");

require_once("header.php");
require_once("navigation.php");
require_once("classes/index_class.php");
require_once("classes/snmp_class.php");
require_once("classes/customers_class.php");


$index_obj = new index();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (!empty($index_obj->getOlt_id())) {
		$OLT_NAME = $index_obj->getOlt_name();
	}
	
	if (!empty($index_obj->getPon_id())) {
		$row = $index_obj->getPon_data();	
		$PON_NAME = $row['NAME'];
		$SLOT_ID = $row['SLOT_ID'];
		$PORT_ID = $row['PORT_ID'];
		$PON_TYPE = $row['PON_TYPE'];
	}
}
if (!isset($_POST['initial'])){
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
										print "<option value=\"" . $row['ID'] ."\">" . $row['NAME'] . "</option>";
						}
						?>
						</select>
						<label for="pon_id">PON</label>
						<select class="form-control" id="select-pon" name="pon_id">
						<option value="">PON PORT</option></select>
						<label><input type="checkbox" id="online" name="online" value="YES" checked>online</label>
						<label><input type="checkbox" id="offline" name="offline" value="YES" checked>offline</label>
						<label><input type="checkbox" id="pending" name="pending" value="YES" checked>pending</label>
						<input type="hidden" name="SUBMIT" value="LOAD">
						<input type="hidden" name="initial" value="YES">
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
					<label for="address">Address</label>
					<input type="text" name="address"  size="15"  class="form-control" placeholder="Address"  aria-describedby="sizing-addon1">
					<label for="egn">EGN</label>
					<input type="text" name="egn"  maxlength="10" size="10" class="form-control" placeholder="EGN" aria-describedby="sizing-addon1">
					<label for="sn">SN/MAC</label>
					<input type="text" name="sn"  maxlength="15" size="15" class="form-control" placeholder="SN/MAC" aria-describedby="sizing-addon1">
					<input type="hidden" name="SUBMIT" value="SEARCH">
					<input type="hidden" name="initial" value="YES">
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
<div class="container">
	<div id="output" class="text-center">
		<?php
		if (!empty($index_obj->getPon_id()) || !empty($index_obj->getName()) || !empty($index_obj->getAddress()) || !empty($index_obj->getEgn()) || !empty($index_obj->getSn()) || $index_obj->getSubmit() == "UNASSIGNED") {
		?>
			<div class="page-header">
			<?php 
			if (!empty($index_obj->getOlt_id())) 
				print "<h2>OLT: " . $OLT_NAME . "</h2><h3>PON: " . $PON_NAME . "   (" . $SLOT_ID . "/" . $PORT_ID . ")</h3><br><br>"  ;
			if (!empty($index_obj->getName())) 
				print "<h2>Name: " . $index_obj->getName() . "</h2>";
			?>
			</div>
		<!--	<form class="form-inline"  name="myform3" action="update.php" method="post"> -->
		<div class="row justify-content-md-center">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed table-hover">
					<thead>
						
				<?php
				if ($index_obj->getSubmit() == "CONFIRM_DELETE") {
					foreach($_POST['data'] as $customers_id){
						$customers_obj = new customers();
						$customers_obj->setCustomer_id($customers_id);
						$customers_obj->get_data_customer();
						$error = $customers_obj->delete_customer();
						if(!empty($error))
							print $error;
					}
					$index_obj->setOlt_id($_POST['olt_id']);
					$index_obj->setPon_id($_POST['pon_id']);
					$index_obj->setOnline("YES");
					$index_obj->setOffline("YES");
					$index_obj->setPending("YES");
					$index_obj->setSubmit("LOAD");
				}
				if ($index_obj->getSubmit() == "LOAD") {
					$row = $index_obj->getPon_data();
					
					?>
					<tr align=center style=font-weight:bold>
						<?php if ($user_class >= "9") { ?>
							<th><input type="checkbox" id="selectall"></th>
						<?php } ?>
							<th>ONU</th>
							<th><button type="button" class="btn btn-default" onClick="orderby('NAME','<?php echo $row['OLT']; ?>','<?php echo $row['ID']; ?>','<?php echo $index_obj->getOnline(); ?>','<?php echo $index_obj->getOffline(); ?>','<?php echo $index_obj->getPending(); ?>');">Name</th>
							<th class="hidden-xs hidden-sm"><button type="button" class="btn btn-default" onClick="orderby('ADDRESS','<?php echo $row['OLT']; ?>','<?php echo $row['ID']; ?>','<?php echo $index_obj->getOnline(); ?>','<?php echo $index_obj->getOffline(); ?>','<?php echo $index_obj->getPending(); ?>');">Address</th>
							<th class="hidden-xs hidden-sm">EGN</th>
							<th>SERVICE</th>
							<!-- <th>RF</th> -->
							<th class="hidden-xs hidden-sm"><button type="button" class="btn btn-default" onClick="orderby('SN','<?php echo $row['OLT']; ?>','<?php echo $row['ID']; ?>','<?php echo $index_obj->getOnline(); ?>','<?php echo $index_obj->getOffline(); ?>','<?php echo $index_obj->getPending(); ?>');">SN/MAC</th>
							<th>PWR<br>(db)</th>
							<th class="hidden-xs hidden-sm">DIST<br>(m)</th>
							
							<th>STATUS</th>
							<!--<th>LAST ONLINE</th> -->
							<th class="hidden-xs hidden-sm">OFFLINE<br>REASON</th>
							<th>INFO</th>
							<th class="hidden-xs hidden-sm">SYNC</th>
							<?php if ($user_class >= "6") { ?><th>EDIT</th><?php } ?>
						</tr>
					</thead>
					<?php
					$big_onu_id = type2id($row['SLOT_ID'], $row['PORT_ID'], "1");
					$big_onu_id = $big_onu_id - 1;
					$snmp_obj = new snmp_oid();
					$onu_status_oid = $snmp_obj->get_pon_oid("onu_status_oid", $row['PON_TYPE']);
					$onu_status_oid_boi = $snmp_obj->get_pon_oid("onu_status_oid", $row['PON_TYPE']) . "." . $big_onu_id;
					$onu_offline_reason_oid = $snmp_obj->get_pon_oid("onu_offline_reason_oid", $row['PON_TYPE']);
					$onu_offline_reason_oid_boi = $snmp_obj->get_pon_oid("onu_offline_reason_oid", $row['PON_TYPE']) . "." . $big_onu_id;
					$onu_sn_oid = $snmp_obj->get_pon_oid("onu_sn_oid", $row['PON_TYPE']);
					$onu_sn_oid_boi = $snmp_obj->get_pon_oid("onu_sn_oid", $row['PON_TYPE']) . "." . $big_onu_id;
					exec("$snmpbulkget -Onq -Cr128 -v2c -c $row[RO] $row[IP_ADDRESS] $onu_status_oid_boi", $output , $return_var);
					$onu_status = array();
					foreach($output as $line) {
						if (strpos($line, $onu_status_oid) !== false) {
							$line = str_replace("." . $onu_status_oid . ".", "", $line);
							$line = explode(" ", $line);
							$onu_status[$line[0]] = $line[1];
						}
					}
					exec("$snmpbulkget -Onq -Cr128 -v2c -c $row[RO] $row[IP_ADDRESS] $onu_offline_reason_oid_boi", $output , $return_var);
					$onu_offline_reason = array();
					foreach($output as $line) {
						if (strpos($line, $onu_offline_reason_oid) !== false) {
							$line = str_replace("." . $onu_offline_reason_oid . ".", "", $line);
							$line = explode(" ", $line);
							$onu_offline_reason[$line[0]] = $line[1];
						}
					}
				
					if ($row['PON_TYPE'] == "GPON" || $row['PON_TYPE'] == "XGSPON") {
						exec("$snmpbulkget -OnqE -Cr128 -v2c -c $row[RO] $row[IP_ADDRESS] $onu_sn_oid_boi", $output , $return_var);
						$onu_sn = array();
						foreach($output as $line) {
							if (strpos($line, $onu_sn_oid . ".") !== false) {
								$line = str_replace("." . $onu_sn_oid . ".", "", $line);
								$line = explode(" ", $line);
								$onu_sn[$line[0]] = $line[1];
							}
						}
						$onu_register_distance_oid = $snmp_obj->get_pon_oid("onu_register_distance_oid", $row['PON_TYPE']);
						$onu_register_distance_oid_boi = $snmp_obj->get_pon_oid("onu_register_distance_oid", $row['PON_TYPE']) . "." . $big_onu_id;
						exec("$snmpbulkget -Onq -Cr128 -v2c -c $row[RO] $row[IP_ADDRESS] $onu_register_distance_oid_boi", $output , $return_var);
						$onu_register_distance_arr = array();
						foreach($output as $line) {
							if (strpos($line, $onu_register_distance_oid . ".") !== false) {
								$line = str_replace("." . $onu_register_distance_oid . ".", "", $line);
								$line = explode(" ", $line);
								$onu_register_distance_arr[$line[0]] = $line[1];
							}
						}				
					}	
					if ($row['PON_TYPE'] == "EPON") {
						exec("$snmpbulkget -On -Cr128 -v2c -c $row[RO] $row[IP_ADDRESS] $onu_sn_oid_boi", $output , $return_var);
						$onu_sn = array();
						foreach($output as $line) {
							if (strpos($line, $onu_sn_oid . ".") !== false) {
								$line = str_replace("." . $onu_sn_oid . ".", "", $line);
								$line = explode(" = ", $line);
								$line[1] = trim(str_replace('Hex-STRING: ', '', $line[1]));
								$line[1] = str_replace(' ', '', $line[1]);
								$onu_sn[$line[0]] = $line[1];	
							}
						}
						$dot3MpcpRoundTripTime = $snmp_obj->get_pon_oid("dot3MpcpRoundTripTime", "OLT");
						$dot3MpcpRoundTripTime_boi = $snmp_obj->get_pon_oid("dot3MpcpRoundTripTime", "OLT") . "." . $big_onu_id;
						exec("$snmpbulkget -Onq -Cr128 -v2c -c $row[RO] $row[IP_ADDRESS] $dot3MpcpRoundTripTime_boi", $output , $return_var);
						$onu_register_distance_arr = array();
						foreach($output as $line) {
							if (strpos($line, $dot3MpcpRoundTripTime . ".") !== false) {
								$line = str_replace("." . $dot3MpcpRoundTripTime . ".", "", $line);
								$line = explode(" ", $line);
								if ($line[1] <= '46')
									$line[1] = '1';
								if ($line[1] > '46')
									$line[1] = number_format(round(($line[1] - 46)*1.6));
								$onu_register_distance_arr[$line[0]] = $line[1];
							}
						}				
					}
				}else{
					?>
					<tr align=center style=font-weight:bold>
						<?php if ($user_class >= "9") { ?>
							<th><input type="checkbox" id="selectall"></th>
						<?php } ?>
							<th>ONU</th>
							<th>Name</th>
							<th class="hidden-xs hidden-sm">Address</th>
							<th class="hidden-xs hidden-sm">EGN</th>
							<th>SERVICE</th>
							<!-- <th>RF</th> -->
							<th class="hidden-xs hidden-sm">SN/MAC</th>
							<th>PWR<br>(db)</th>
							<th class="hidden-xs hidden-sm">DIST<br>(m)</th>
							
							<th>STATUS</th>
							<!--<th>LAST ONLINE</th> -->
							<th class="hidden-xs hidden-sm">OFFLINE<br>REASON</th>
							<th>INFO</th>
							<th class="hidden-xs hidden-sm">SYNC</th>
							<?php if ($user_class >= "6") { ?><th>EDIT</th><?php } ?>
						</tr>
					</thead>
					<?php
				}
				$rows = $index_obj->build_table();
				$count = 0;
				if(!empty($rows)) {	
					foreach ($rows as $row) {
						$onu_register_distance = "";
						if (isset($row['PON_TYPE'])) {
							$snmp_obj = new snmp_oid();
							$big_onu_id = type2id($row['SLOT_ID'], $row['PORT_ID'], $row['PON_ONU_ID']);
							$onu_status_oid = $snmp_obj->get_pon_oid("onu_status_oid", $row['PON_TYPE']) . "." . $big_onu_id;
							$onu_last_online_oid = $snmp_obj->get_pon_oid("onu_last_online_oid", $row['PON_TYPE']) . "." . $big_onu_id;
							$onu_offline_reason_oid = $snmp_obj->get_pon_oid("onu_offline_reason_oid", $row['PON_TYPE']) . "." . $big_onu_id;
							$onu_sn_oid = $snmp_obj->get_pon_oid("onu_sn_oid", $row['PON_TYPE']) . "." . $big_onu_id;
							if ($row['PON_TYPE'] == "GPON")
								$onu_register_distance_oid = $snmp_obj->get_pon_oid("onu_register_distance_oid", $row['PON_TYPE']) . "." . $big_onu_id;
							$dot3MpcpRoundTripTime = $snmp_obj->get_pon_oid("dot3MpcpRoundTripTime", "OLT") . "." . $big_onu_id;
								//GET ONU STATUS via SNMP
							if ($index_obj->getSubmit() == "LOAD") {
								$status = $onu_status[$big_onu_id];
							}else{
								snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
								$session = new SNMP(SNMP::VERSION_2C, $row['IP_ADDRESS'], $row['RO']);
								$status = $session->get($onu_status_oid);
							}
							$power = '';
							$last_online = "Never";
							$rf_state = "";
							if ($status == '1') {								
								if ($index_obj->getOnline() != "YES" && $index_obj->getSubmit() != "SEARCH")
									continue;

								$status = "<font color=green>Online</font>";
								//GET POWER/DISTANCE via SNMP
								if ($index_obj->getSubmit() == "LOAD") {
									$onu_register_distance = $onu_register_distance_arr[$big_onu_id];
								}else{
									snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
									$session = new SNMP(SNMP::VERSION_2C, $row['IP_ADDRESS'], $row['RO'], 500000, 2);
									if ($row['PON_TYPE'] == "GPON") {
										$onu_register_distance = $session->get($onu_register_distance_oid);
									}
									if ($row['PON_TYPE'] == "EPON") {
										$dot3MpcpRoundTripTime = $session->get($dot3MpcpRoundTripTime);
										if ($dot3MpcpRoundTripTime <= '46')
											$onu_register_distance = '1';
										if ($dot3MpcpRoundTripTime > '46')
											$onu_register_distance = number_format(round(($dot3MpcpRoundTripTime - 46)*1.6));
									}
									if ($row['PON_TYPE'] == "XGSPON") {
										$onu_register_distance = $session->get($onu_register_distance_oid);
									}
								}
								$power = $index_obj->get_rx_power($row['ID']);
								if ($power) {
									if ($power < "-25") {
										$power = "<font color=red>" . $power . "</font>" ;
									} else {
										$power = "<font color=green>" . $power . "</font>" ;
									}
								} else {
									$power = NULL;
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
							}
							
							if($status == '2'){
								if ($index_obj->getPending() != "YES" && $index_obj->getSubmit() != "SEARCH")
									continue;
								$status = "<font color=blue>Pending</font>";
							}else if($status == '3'){
								if ($index_obj->getOffline() != "YES" && $index_obj->getSubmit() != "SEARCH")
									continue;
								$status = "<font color=red>Offline</font>";
							}

							//ONU OFFLINE REASON
							if ($index_obj->getSubmit() == "LOAD") {
								$offline_reason = $onu_offline_reason[$big_onu_id];
							}else{
								$offline_reason = $session->get($onu_offline_reason_oid);
							}
							if ($row['PON_TYPE'] == "GPON" || $row['PON_TYPE'] == "XGSPON") {
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
							if ($row['PON_TYPE'] == "EPON") {
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
							if ($index_obj->getSubmit() == "LOAD") {
								$check_sn = str_replace("\"", "", $onu_sn[$big_onu_id]);
							}else{
								if ($row['PON_TYPE'] == "EPON") {
									snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
									$session = new SNMP(SNMP::VERSION_2C, $row['IP_ADDRESS'], $row['RO'], 500000, 2);
									$check_sn = $session->get($onu_sn_oid);
									$check_sn = trim(str_replace('Hex-STRING: ', '', $check_sn));
									$check_sn = str_replace('"', '', str_replace(' ', '', $check_sn));
								} else {
									$session = new SNMP(SNMP::VERSION_2C, $row['IP_ADDRESS'], $row['RO']);
									$check_sn = $session->get($onu_sn_oid);	
								}
							}

							//$check_sn = str_replace("52434D47","RCMG", $check_sn);
							$db_sn = $row['SN'];
							
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
							$db_sn = $row['SN'];
							$sync = NULL;
						}

						if ($row['ID'] == $index_obj->getOnu_id()) {
							echo "<tr class=\"bg-danger\" align=\"right\">";
						}else{
							echo "<tr align=\"right\">";
						}
						if ($user_class >= "9") { ?>	
							<td><input type="checkbox" class="case" name="check_list[]" value="<?php echo $row['ID']; ?>"></td>	
						<?php }
						if ($index_obj->getSubmit() == "SEARCH") {									
						?>
							<td><button type="button" class="btn btn-default" onClick="ShowSamePon('<?php echo $row['OLT_ID'] . "','" . $row['PON_ID'] . "','" . $row['ID']; ?>');"><?php echo $row['OLT_NAME'] . "/" . $row['SLOT_ID'] . "/" . $row['PORT_ID'] . "/"	; echo $row['PON_ONU_ID']; ?></button></td>
						<?php }else{ ?>
							<td><?php echo $row['PON_ONU_ID']; ?></td>
						<?php } ?>
						<td><?php echo $row['NAME']; ?></td>
							<td class="hidden-xs hidden-sm"><?php echo $row['ADDRESS']; ?></td>
							<td class="hidden-xs hidden-sm"><?php echo $row['EGN']; ?></td>
							<td><?php echo $row['SERVICE_NAME']; ?></td>
							<td class="hidden-xs hidden-sm"><?php echo $db_sn; ?></td>
							<td><?php echo $power; ?></td>
							<?php echo "<td class=\"hidden-xs hidden-sm\">" . $onu_register_distance . "</td>"; ?>
							<td><?php echo $status; ?></td>
							<!--	<td><?php echo $last_online; ?></td> -->
							<td class="hidden-xs hidden-sm"><?php echo $offline_reason; ?></td>
							<td><?php if ($index_obj->getSubmit() != "UNASSIGNED") { echo "<a href=\"onu_details.php?id=" . $row['ID'] . "\">";} ?><button type="button" class="btn btn-default">INFO</button></а></td>
							<td class="hidden-xs hidden-sm"><?php echo $sync; ?></td>
							<?php if ($user_class >= "6") { 
							if ($index_obj->getSubmit() == "SEARCH") {?>
								<td><button type="button" class="btn btn-default" onClick="getCustomerSearch('<?php echo $row['ID']; ?>','<?php echo $index_obj->getSubmit(); ?>');">EDIT</button></td>
							<?php }else{ ?>
								<td><button type="button" class="btn btn-default" onClick="getCustomer('<?php echo $row['ID']; ?>','<?php echo $index_obj->getOnline(); ?>','<?php echo $index_obj->getOffline(); ?>','<?php echo $index_obj->getPending(); ?>','<?php echo $index_obj->getSubmit(); ?>');">EDIT</button></td>
							<?php }} ?>
						</tr>
					<?php 
					$count = $count + 1 ; 
					}
				} ?>
			</table>
		</div>
	<div>Total: <?php echo $count; ?></div>
	<?php if ($user_class >= "9") { ?>
	<div><button type="button" class="btn btn-danger" onClick="delete_selected('<?php echo $row['OLT_ID']; ?>','<?php echo $row['PON_ID']; ?>');">DELETE SELECTED</button></div>
	<?php } ?>
	<div>&nbsp;</div>
</div>
</div>
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
