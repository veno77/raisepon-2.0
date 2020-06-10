<?php
include ("header.php");
include ("common.php");
include ("navigation.php");
include ("classes/olt_class.php");
include ("classes/snmp_class.php");

if ($user_class < "6")
	exit();

$olt_obj = new olt(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// ADD OLT
	if ($olt_obj->getSubmit() == "ADD") {
		if (!empty($olt_obj->getName()) && !empty($olt_obj->getOlt_ip_address()) && !empty($olt_obj->getSnmp_community_ro()) && !empty($olt_obj->getSnmp_community_rw()) && !empty($olt_obj->getOlt_model())) {
			$error = $olt_obj->create_olt();	
			if (isset($error)) {
				echo $error;	
			}else{
				echo "<center><div class=\"bg-success  text-white\">OLT added Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, OLT IP_Address, RO and RW Communities and OLT Model are required fields!</div></center>";
		}
	}

	// EDIT OLT
	if ($olt_obj->getSubmit() == "EDIT") {
		if (!empty($olt_obj->getOlt_id()) && !empty($olt_obj->getName()) && !empty($olt_obj->getOlt_ip_address()) && !empty($olt_obj->getSnmp_community_ro()) && !empty($olt_obj->getSnmp_community_rw()) && !empty($olt_obj->getOlt_model())) {
			$error = $olt_obj->edit_olt();
			if (isset($error)) {
				echo $error;	
			}else{
				print "<center><div class=\"bg-success  text-white\">OLT Edited Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: Name, OLT IP_Address, RO and RW Communities and OLT Model are required fields! Or you are missing OLT_ID!</div></center>";
		}
	}
	
	// DELETE OLT
	if ($olt_obj->getSubmit() == "DELETE") {
		if (!empty($olt_obj->getOlt_id())) {
		$error = $olt_obj->delete_olt();
			if (isset($error)) {
				echo $error;	
			}else{
				print "<center><div class=\"bg-success  text-white\">OLT Deleted Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: OLT_ID missing!</div></center>";
		
		}
	}
	
	//SAVE OLT CONFIG
	if ($olt_obj->getSubmit() == "SAVE") {
		if (!empty($olt_obj->getOlt_id())) {
		$error = $olt_obj->save_olt();
			if (isset($error)) {
				echo $error;	
			}else{
				print "<center><div class=\"bg-success  text-white\">OLT Config Saved Succesfully</div></center>";
			}
		} else {
			echo "<center><div class=\"bg-danger text-white\">ERROR: OLT_ID missing!</div></center>";
		
		}
	}
}
?>
<div class="container">
	<div class="text-center">
		<div class="page-header">
			<h2>OLT Configuration</h2>
		</div>
	</div>
	<div class=row>
		<div class="text-center">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed table-hover">
					<thead>
					  <tr>
						<th>Name</th>
						<th>Model</th>
						<th>IP Address</th>
						<th>R/O</th>
						<th>R/W</th>
						<th>Status</th>
						<th>Temp</th>
						<th>CPU</th>
						<th>Uptime</th>
						<th>Backup</th>
						<th>Info</th>
						<th>Config</th>
						<th>Edit</th>
					  </tr>
					</thead>
					<tbody>
					<?php
					$rows = $olt_obj->build_table_olt(); 
					foreach ($rows as $row) {
						$snmp_obj = new snmp_oid();
						snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
						$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'}, 100000, 2);
						$status = $session->get($snmp_obj->get_pon_oid("olt_status_oid", "OLT"));
						$temp = '';
						$save = '';
						$cpu = '';
						$sysuptime = '';
						$slot = '';
						$total_cpu = "";
						if ($status) {
							$status = "<font color=green>Online</font>";
							$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
							$temp = $session->get($snmp_obj->get_pon_oid("olt_temp_oid", "OLT"));
							if ($temp > '65') {
								$temp = "<font color=red>" . $temp . "\xc2\xb0C</font>";
							}else {
								$temp = $temp . "\xc2\xb0C";
							}
							snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
							snmp_set_quick_print(TRUE);
							snmp_set_enum_print(TRUE);
							snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
							$session = new SNMP(SNMP::VERSION_1, $row{'IP_ADDRESS'}, $row{'RO'});
							$olt_cpu_oid = $snmp_obj->get_pon_oid("olt_cpu_oid", "OLT");
							$cpus = $session->walk($olt_cpu_oid);
							foreach ($cpus as $cpu_oid => $cpu) {
								$slot = str_replace($olt_cpu_oid, '', substr($cpu_oid, 0, -1));
								$slot = str_replace('.','',$slot);
								if ($cpu > '50') {
									$cpu = "Slot" . $slot . ": <font color=red>" . $cpu . "%</font>";
								}else{
									$cpu = "Slot" . $slot . ": " . $cpu . "%";
								}
								$total_cpu = $total_cpu . $cpu . "<br>";
							}
							snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
							$session = new SNMP(SNMP::VERSION_2C, $row{'IP_ADDRESS'}, $row{'RO'});
							$sysuptime = $session->get($snmp_obj->get_pon_oid("sys_uptime_oid", "OLT"));
							$sysuptime_days = floor($sysuptime/(100*3600*24));
							$sysuptime_hours = $sysuptime/(100*3600)%24;
							$sysuptime_minutes = $sysuptime/(100*60)%60;
							$sysuptime = $sysuptime_days . " day(s) " . $sysuptime_hours . " hour(s) " . $sysuptime_minutes . " minutes";
							$save = '<form action="olt.php" method="post"><input type="hidden" name="olt_id" value="' . $row{'ID'} .'"><button type="submit" class="btn btn-default" name="SUBMIT" value="SAVE">SAVE</button></form>';
						}else{
							$status = "<font color=red>Offline</font>";
						}
					
						//	print "<tr><td>" . $row{'NAME'} . "</td><td>" .$row{'OLT_NAME'} . "</td><td>" . $row{'IP_ADDRESS'} . "</td><td>" . $row{'RO'} . "</td><td>" . $row{'RW'} . "</td><td>" . $status . "</td><td>" . $temp .  "</td><td>" . $cpu . "</td><td>" . $save . "</td></tr>";
						?>
						<tr>
							<td><?php echo $row{'NAME'}; ?></td>
							<td><?php echo $row{'OLT_NAME'}; ?></td>
							<td><?php echo $row{'IP_ADDRESS'}; ?></td>
							<td><?php echo $row{'RO'}; ?></td>
							<td><?php echo $row{'RW'}; ?></td>
							<td><?php echo $status; ?></td>
							<td><?php echo $temp; ?></td>
							<td><?php echo $total_cpu; ?></td>
							<td><?php echo $sysuptime; ?></td>
							<td><?php echo $row{'BACKUP_NAME'}; ?></td>
							<td><?php echo "<a href=\"olt_details.php?id=" . $row{'ID'} . "\">"; ?><button type="button" class="btn btn-default">INFO</button></a></td>
							<td><?php echo $save; ?></td>
							<td><button type="button" class="btn btn-default" onClick="getOlt('<?php echo $row{'ID'}; ?>');">EDIT</button></td>
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
				<button type="button" class="btn btn-info" onClick="getOlt();">ADD NEW OLT</button>
		</div>
	</div>
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog"> 
			  <!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">OLT</h4>
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

