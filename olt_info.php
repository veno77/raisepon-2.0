<?php
include ("common.php");
include ("dbconnect.php");
include ("classes/snmp_class.php");

$mac_address_table = $olt_id = $type = "";
$snmp_obj = new snmp_oid();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST["olt_id"])) {
                $olt_id = test_input($_POST["olt_id"]);
        }
	if ($_POST["type"]) {
                $type = test_input($_POST["type"]);
        }

	if (isset($_POST["port_num"])) {
                $port_num = test_input($_POST["port_num"]);
        }	
	if ($_POST["type"] == "Reboot") {
		try {
			$result = $db->query("SELECT OLT.ID, OLT.NAME as OLT_NAME, MODEL, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE from OLT LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID where OLT.ID = '$olt_id'");

			} catch (PDOException $e) {
					echo "Connection Failed:" . $e->getMessage() . "\n";
					exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ip_address = $row['IP_ADDRESS'];
			$olt_name = $row['OLT_NAME'];
			$ro = $row['RO'];
			$rw = $row['RW'];
			$olt_type = $row['TYPE'];
		}

		$reboot_oid = $snmp_obj->get_pon_oid("olt_reboot_oid", "olt");
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$session = new SNMP(SNMP::VERSION_2C, $ip_address, $rw);
		
		$reboot = $session->set($reboot_oid, 'i', '1');
		if ($session->getError())
				return(var_dump($session->getError()));
		echo "<center><div class=\"bg-success  text-white\">OLT Rebooted Succesfully</center></div>";

	}
		
	if ($_POST["type"] == "SET_RF") {
		try {
			$result = $db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME as NAME, SN, PON_ONU_ID, CUSTOMERS.SERVICE, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, OLT.ID, INET_NTOA(OLT.IP_ADDRESS)as IP_ADDRESS, OLT.NAME as OLT_NAME, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where CUSTOMERS.ID = '$customer_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ip_address = $row['IP_ADDRESS'];
			$port_id = $row['PORT_ID'];
			$slot_id = $row['SLOT_ID'];
			$pon_onu_id = $row['PON_ONU_ID'];
			$olt_name = $row['OLT_NAME'];
			$ro = $row['RO'];
			$rw = $row['RW'];
			$olt_type = $row['TYPE'];
			$name = $row['NAME'];
			$pon_type = $row['PON_TYPE'];
		}

		if ($pon_type == "EPON")
		$index_rf = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + 162;						
		if ($pon_type == "GPON")
		$index_rf = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + 1;		
	
		$onu_rf_status_oid = $snmp_obj->get_pon_oid("onu_rf_status_oid", $pon_type) . "." . $index_rf;
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$session = new SNMP(SNMP::VERSION_2C, $ip_address, $rw);
		$set_rf = $session->set($onu_rf_status_oid, 'i', $rf_val);
		if ($session->getError())
				return(var_dump($session->getError()));

		$type = "ports";
	}
	
	if ($_POST["type"] == "SET_UNI") {
		try {
			$result = $db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME as NAME, SN, PON_ONU_ID, CUSTOMERS.SERVICE, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, OLT.ID, INET_NTOA(OLT.IP_ADDRESS)as IP_ADDRESS, OLT.NAME as OLT_NAME, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where CUSTOMERS.ID = '$customer_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ip_address = $row['IP_ADDRESS'];
			$port_id = $row['PORT_ID'];
			$slot_id = $row['SLOT_ID'];
			$pon_onu_id = $row['PON_ONU_ID'];
			$olt_name = $row['OLT_NAME'];
			$ro = $row['RO'];
			$rw = $row['RW'];
			$olt_type = $row['TYPE'];
			$name = $row['NAME'];
			$pon_type = $row['PON_TYPE'];
		}

		if ($pon_type == "EPON")
		$index_uni = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + $port_num;						
		if ($pon_type == "GPON")
		$index_uni = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + $port_num;		
	
		$uni_port_admin_set = $snmp_obj->get_pon_oid("uni_port_admin_set_oid", $pon_type) . "." . $index_uni;
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$session = new SNMP(SNMP::VERSION_2C, $ip_address, $rw);
		$set_uni = $session->set($uni_port_admin_set, 'i', $uni_val);
		if ($session->getError())
				return(var_dump($session->getError()));
			
		$type = "ports";
	}

	if ($type == "info"){
		try {
			$result = $db->query("SELECT OLT.ID, OLT.NAME as OLT_NAME, MODEL, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE, OLT_MODEL.NAME as OLT_MODEL_NAME, BACKUP_STATUS.DATE, BACKUP_STATUS.REASON from OLT LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN BACKUP_STATUS on OLT.ID = BACKUP_STATUS.OLT where OLT.ID = '$olt_id'");

			} catch (PDOException $e) {
					echo "Connection Failed:" . $e->getMessage() . "\n";
					exit;
			}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ip_address = $row['IP_ADDRESS'];
			$olt_name = $row['OLT_NAME'];
			$ro = $row['RO'];
			$rw = $row['RW'];
			$olt_type = $row['TYPE'];
			$olt_model_name = $row['OLT_MODEL_NAME'];
			$date = $row['DATE'];
			$reason = $row['REASON'];
		}
		$index = 10000000;
		if ($olt_model_name == "ISCOM6820-GP" || $olt_model_name == "ISCOM6820-EP")
			$index = 30000000;
		if ($reason == "1")
			$reason = "noError(1)";
		if ($reason == "2")
			$reason = "badMethod(2)";
		if ($reason == "3")
			$reason = "badSourceAddress(3)";
		if ($reason == "4")
			$reason = "badDestAddress(4)";
		if ($reason == "5")
			$reason = "badPort(5)";
		if ($reason == "6")
			$reason = "badUserName(6)";
		if ($reason == "7")
			$reason = "badPassword(7)";
		if ($reason == "8")
			$reason = "badFileName(8)";
		if ($reason == "9")
			$reason = "fileOpenFail(9)(8)";
		if ($reason == "10")
			$reason = "fileWriteFail(10)";
		if ($reason == "11")
			$reason = "timeout(11)";
		if ($reason == "12")
			$reason = "noMem(12)";
		if ($reason == "13")
			$reason = "noConfig(13)";
		if ($reason == "14")
			$reason = "fileTooLarge(14)";
		if ($reason == "15")
			$reason = " unknown(15)";
		if ($reason == "16")
			$reason = "badSourceFileType(16)";
		if ($reason == "17")
			$reason = "badDestFileType(17)";
		$sys_uptime_oid = $snmp_obj->get_pon_oid("sys_uptime_oid", "olt");
		$olt_serial_number_oid = $snmp_obj->get_pon_oid("olt_serial_number_oid", "olt");
		$olt_hw_version_oid = $snmp_obj->get_pon_oid("olt_hw_version_oid", "olt");
		$olt_model_oid = $snmp_obj->get_pon_oid("olt_model_oid", "olt");
		$olt_slot_num_oid = $snmp_obj->get_pon_oid("olt_slot_num_oid", "olt");
		$olt_mac_address_oid = $snmp_obj->get_pon_oid("olt_mac_address_oid", "olt");

		$raisecomSWFileVersion1_oid = "1.3.6.1.4.1.8886.1.26.3.1.1.2." . $index . ".0.1" ;
		$raisecomSWFileVersion2_oid = "1.3.6.1.4.1.8886.1.26.3.1.1.2." . $index . ".0.2" ;
		$raisecomSWFileCommit1_oid = "1.3.6.1.4.1.8886.1.26.3.1.1.3." . $index . ".0.1" ;
		$raisecomSWFileCommit2_oid = "1.3.6.1.4.1.8886.1.26.3.1.1.3." . $index . ".0.2" ;
		$raisecomSWFileActivate1_oid = "1.3.6.1.4.1.8886.1.26.3.1.1.4." . $index . ".0.1" ;
		$raisecomSWFileActivate2_oid = "1.3.6.1.4.1.8886.1.26.3.1.1.4." . $index . ".0.2" ;
		
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro);
		$sysuptime = $session->get($snmp_obj->get_pon_oid("sys_uptime_oid", "OLT"));							
		if ($session->getError())
			return(var_dump($session->getError()));
		if ($sysuptime) {
			$sysuptime_days = floor($sysuptime/(100*3600*24));
			$sysuptime_hours = $sysuptime/(100*3600)%24;
			$sysuptime_minutes = $sysuptime/(100*60)%60;
			$sysuptime = $sysuptime_days . " day(s) " . $sysuptime_hours . " hour(s) " . $sysuptime_minutes . " minutes";$olt_serial_number = $session->get($olt_serial_number_oid);	
			$olt_hw_version = $session->get($olt_hw_version_oid);	
			$olt_model = $session->get($olt_model_oid);	
			$olt_slot_num = $session->get($olt_slot_num_oid);	
			$raisecomSWFileVersion1 = $session->get($raisecomSWFileVersion1_oid);
			$raisecomSWFileVersion2 = $session->get($raisecomSWFileVersion2_oid);
			$raisecomSWFileCommit1 = $session->get($raisecomSWFileCommit1_oid);
			if ($raisecomSWFileCommit1 == "1") {
				$raisecomSWFileCommit1 = "(committed)";
			}else{
				$raisecomSWFileCommit1 = "";
			}
			$raisecomSWFileCommit2 = $session->get($raisecomSWFileCommit2_oid);
			
			if ($raisecomSWFileCommit2 == "1") {
				$raisecomSWFileCommit2 = "(committed)";
			}else{
				$raisecomSWFileCommit2 = "";
			}
			$raisecomSWFileActivate1 = $session->get($raisecomSWFileActivate1_oid);
			if ($raisecomSWFileActivate1 == "1") {
					$raisecomSWFileActivate1 = "(active)";
			}else{
					$raisecomSWFileActivate1 = "";
			}
			$raisecomSWFileActivate2 = $session->get($raisecomSWFileActivate2_oid);

			if ($raisecomSWFileActivate2 == "1") {
					$raisecomSWFileActivate2 = "(active)";
			}else{
					$raisecomSWFileActivate2 = "";
			}

			snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
			$session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro);
			$substr = array("\"", " ", "Hex-STRING:");
			$olt_mac_address = str_replace($substr, '',$session->get($olt_mac_address_oid));	


			print "<div class=\"table-responsive\"><table class=\"table table-bordered table-condensed table-hover\">";
			print "<tr><th>Device Type:</th><td>" . $olt_model . "</td></tr>";
			print "<tr><th>HW Revision:</th><td>" . $olt_hw_version . "</td></tr>";
			print "<tr><th>SW Version 1:</th><td>" . $raisecomSWFileVersion1 . " " . $raisecomSWFileCommit1 . " " . $raisecomSWFileActivate1 . "</th></tr>";
			print "<tr><th>SW Version 2:</th><td>" . $raisecomSWFileVersion2 . " " . $raisecomSWFileCommit2 . " " . $raisecomSWFileActivate2 . "</th></tr>";
			print "<tr><th>Serial Number:</th><td>" . $olt_serial_number . "</td></tr>";
			print "<tr><th>Mac Address:</th><td>" . $olt_mac_address . "</td></tr>";
			print "<tr><th>Slots per System:</th><td>" . $olt_slot_num . "</td></tr>";
			print "<tr><th>Uptime:</th><td>" . $sysuptime . "</td></tr>";
			print "<tr><th>Status:</th><td><font color=green>Online</font></td></tr>";
			print "<tr><th>Backup Status:</th><td>" . $reason . " - " . $date . "</td></tr>";
			print "</table></div>";
			print "<div class=\"form-group\"><form class=\"form-horizontal\" action=\"onu_info.php\" method=\"post\">";
			print "<input type=\"hidden\" name=\"olt_id\" value=\"". $olt_id ."\">";
			print "<div class=\"row justify-content-md-center\"><div class=\"col-md-4\">";
			print "<button class=\"btn btn-info\" type=\"button\" onClick=\"getOltPage('" . $olt_id . "', 'Reboot');\">Reboot</button>";
			print "</div></div>";
			print "</form></div>";
		}
	}


	if ($type == "ports"){
		try {
			$result = $db->query("SELECT OLT.ID, OLT.NAME as OLT_NAME, MODEL, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE from OLT LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID where OLT.ID = '$olt_id'");

			} catch (PDOException $e) {
					echo "Connection Failed:" . $e->getMessage() . "\n";
					exit;
			}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ip_address = $row['IP_ADDRESS'];
			$olt_name = $row['OLT_NAME'];
			$ro = $row['RO'];
			$rw = $row['RW'];
			$olt_type = $row['TYPE'];
		}
		$snmp_obj = new snmp_oid();
		snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
		snmp_set_quick_print(TRUE);
		snmp_set_enum_print(TRUE);
		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		$session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro, 1000000);
		$status = $session->get($snmp_obj->get_pon_oid("olt_status_oid", "OLT"));
		if ($status) {
			print "Interfaces";
			$ethernet_port_info = array(); 
			$dot3StatsIndex = $snmp_obj->get_pon_oid("dot3StatsIndex", "OLT");
			$output = $session->walk($dot3StatsIndex);
			foreach ($output as $oid => $index) {
				$ifAdminStatus = $session->get($snmp_obj->get_pon_oid("ifAdminStatus", "OLT") . "." . $index);
				$ifDescr = $session->get($snmp_obj->get_pon_oid("ifDescr", "OLT"). "." . $index);
				$ifOperStatus = $session->get($snmp_obj->get_pon_oid("ifOperStatus", "OLT"). "." . $index);
				$ifHighSpeed = $session->get($snmp_obj->get_pon_oid("ifHighSpeed", "OLT"). "." . $index);
				$dot3StatsDuplexStatus = $session->get($snmp_obj->get_pon_oid("dot3StatsDuplexStatus", "OLT"). "." . $index);

				if ($ifAdminStatus == "1") {
						$ifAdminStatus = "<font color=green>Enabled</font>";
					}else if ($ifAdminStatus == "2") {
						$ifAdminStatus = "<font color=red>Disabled</font>";
				}
				if ($ifOperStatus == "1") {
					$ifOperStatus = "<font color=green>Up</font>";
				}else if ($ifOperStatus == "2") {
					$ifOperStatus = "<font color=red>Down</font>";
				}
				if ($dot3StatsDuplexStatus == "1") {
					$dot3StatsDuplexStatus = "Unknown";
				}else if ($dot3StatsDuplexStatus == "2") {
					$dot3StatsDuplexStatus = "half";
				}else if ($dot3StatsDuplexStatus == "3") {
					$dot3StatsDuplexStatus = "full";
				}
				$pon_port_info[$index] = array($ifDescr, $ifAdminStatus, $ifOperStatus, $ifHighSpeed, $dot3StatsDuplexStatus);		
			}
		
			?>
			<div class="table-responsive">
				<table class="table table-bordered table-condensed table-hover">
					<thead>
						<tr>
							<th>ID</th>
							<th>Description</th>
							<th>Admin</th>
							<th>Operate</th>
							<th>Speed</th>
							<th>Duplex</th>
						</tr>  
					</thead>
			<?php
			$data = array();
			foreach ($pon_port_info as $index => $data) {
				print "<tr  align=center><td>" . $index . "</td>";
				foreach ($data as $d) {
					print "<td>" . $d . "</td>";
				}
				print "</tr>";
			}
			print "</table></div>";
			print "PON Interfaces";

			if (($olt_type == "EPON") || ($olt_type == "XPON")) {
				$pon_port_info = array(); 
				$rcEponPONPortIndex_oid = $snmp_obj->get_pon_oid("rcEponPONPortIndex", "EPON");
				$output = $session->walk($rcEponPONPortIndex_oid);
				foreach ($output as $oid => $index) {
					$rcEponPONPortAdmin = $session->get($snmp_obj->get_pon_oid("rcEponPONPortAdmin", "EPON") . "." . $index);
					$ifDescr = $session->get($snmp_obj->get_pon_oid("ifDescr", "OLT"). "." . $index);
					$rcEponPONPortOperStatus = $session->get($snmp_obj->get_pon_oid("rcEponPONPortOperStatus", "EPON"). "." . $index);
					$rcEponPONPortRegisteredONUNumber = $session->get($snmp_obj->get_pon_oid("rcEponPONPortRegisteredONUNumber", "EPON"). "." . $index);
					$rcEponPONPortSFPOperStatus = $session->get($snmp_obj->get_pon_oid("rcEponPONPortSFPOperStatus", "EPON"). "." . $index);
					$rcEponPONPortCreateONUNumber = $session->get($snmp_obj->get_pon_oid("rcEponPONPortCreateONUNumber", "EPON"). "." . $index);

					if ($rcEponPONPortAdmin == "1") {
						$rcEponPONPortAdmin = "<font color=green>Up</font>";
					}else if ($rcEponPONPortAdmin == "2") {
						$rcEponPONPortAdmin = "<font color=red>Down</font>";
					}
					if ($rcEponPONPortOperStatus == "1") {
						$rcEponPONPortOperStatus = "<font color=green>Up</font>";
					}else if ($rcEponPONPortOperStatus == "2") {
						$rcEponPONPortOperStatus = "<font color=red>Down</font>";
					}
					if ($rcEponPONPortSFPOperStatus == "1") {
						$rcEponPONPortSFPOperStatus = "<font color=green>ok</font>";
					}else if ($rcEponPONPortSFPOperStatus == "2") {
						$rcEponPONPortSFPOperStatus = "<font color=red>tx_fault</font>";
					}else if ($rcEponPONPortSFPOperStatus == "3") {
						$rcEponPONPortSFPOperStatus = "Unknown";
					}
					$pon_port_info[$index] = array($ifDescr, $rcEponPONPortAdmin, $rcEponPONPortOperStatus, $rcEponPONPortSFPOperStatus, $rcEponPONPortCreateONUNumber, $rcEponPONPortRegisteredONUNumber);		
				}
			
				?>
				<div class="table-responsive">
					<table class="table table-bordered table-condensed table-hover">
						<thead>
							<tr>
								<th>ID</th>
								<th>Description</th>
								<th>Admin</th>
								<th>State</th>
								<th>SFP</th>
								<th>Created ONUs</th>
								<th>Online ONUs</th>
							</tr>  
						</thead>
				<?php
				$data = array();
				foreach ($pon_port_info as $index => $data) {
					print "<tr  align=center><td>" . $index . "</td>";
					foreach ($data as $d) {
						print "<td>" . $d . "</td>";
					}
					print "</tr>";
				}
				print "</table></div>";		
			}
			if (($olt_type == "GPON") || ($olt_type == "XPON")) {
				$pon_port_info = array(); 
				$rcGponPONPortIndex = $snmp_obj->get_pon_oid("rcGponPONPortIndex", "GPON");
				$output = $session->walk($rcGponPONPortIndex);
				foreach ($output as $oid => $index) {
					$ifAdminStatus = $session->get($snmp_obj->get_pon_oid("ifAdminStatus", "OLT") . "." . $index);
					$ifDescr = $session->get($snmp_obj->get_pon_oid("ifDescr", "OLT"). "." . $index);
					$rcGponPONPortOperStatus = $session->get($snmp_obj->get_pon_oid("rcGponPONPortOperStatus", "GPON"). "." . $index);
					$rcGponPONPortRegisteredONUNumber = $session->get($snmp_obj->get_pon_oid("rcGponPONPortRegisteredONUNumber", "GPON"). "." . $index);
					$rcGponPONPortSFPOperStatus = $session->get($snmp_obj->get_pon_oid("rcGponPONPortSFPOperStatus", "GPON"). "." . $index);
					$rcGponPONPortAllocIdLeft = $session->get($snmp_obj->get_pon_oid("rcGponPONPortAllocIdLeft", "GPON"). "." . $index);

					if ($ifAdminStatus == "1") {
						$ifAdminStatus = "<font color=green>Up</font>";
					}else if ($ifAdminStatus == "2") {
						$ifAdminStatus = "<font color=red>Down</font>";
					}
					if ($rcGponPONPortOperStatus == "1") {
						$rcGponPONPortOperStatus = "<font color=green>Up</font>";
					}else if ($rcGponPONPortOperStatus == "2") {
						$rcGponPONPortOperStatus = "<font color=red>Down</font>";
					}
					if ($rcGponPONPortSFPOperStatus == "1") {
						$rcGponPONPortSFPOperStatus = "<font color=green>ok</font>";
					}else if ($rcGponPONPortSFPOperStatus == "2") {
						$rcGponPONPortSFPOperStatus = "<font color=red>tx_fault</font>";
					}else if ($rcGponPONPortSFPOperStatus == "3") {
						$rcGponPONPortSFPOperStatus = "Unknown";
					}
					
					$rcGponPONPortAllocIdLeft = 256 - $rcGponPONPortAllocIdLeft;
					$pon_port_info[$index] = array($ifDescr, $ifAdminStatus, $rcGponPONPortOperStatus, $rcGponPONPortSFPOperStatus, $rcGponPONPortAllocIdLeft, $rcGponPONPortRegisteredONUNumber);		
				}
			
				?>
				<div class="table-responsive">
					<table class="table table-bordered table-condensed table-hover">
						<thead>
							<tr>
								<th>ID</th>
								<th>Description</th>
								<th>Admin</th>
								<th>State</th>
								<th>SFP</th>
								<th>Created ONUs</th>
								<th>Online ONUs</th>
							</tr>  
						</thead>
				<?php
				$data = array();
				foreach ($pon_port_info as $index => $data) {
					print "<tr  align=center><td>" . $index . "</td>";
					foreach ($data as $d) {
						print "<td>" . $d . "</td>";
					}
					print "</tr>";
				}
				print "</table></div>";		
			}
		}	
	}
	if ($type == "graphs"){
		try {
			$result = $db->query("SELECT OLT.ID, OLT.NAME as OLT_NAME, MODEL, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE from OLT LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID where OLT.ID = '$olt_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ip_address = $row['IP_ADDRESS'];
			$olt_name = $row['OLT_NAME'];
			$ro = $row['RO'];
			$rw = $row['RW'];
			$olt_type = $row['TYPE'];
		}
		$snmp_obj = new snmp_oid();
		snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
		snmp_set_quick_print(TRUE);
		snmp_set_enum_print(TRUE);
		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		$session = new SNMP(SNMP::VERSION_2C, $ip_address, $ro, 1000000);
		$status = $session->get($snmp_obj->get_pon_oid("olt_status_oid", "OLT"));
		if ($status) {
			$dot3StatsIndex = $snmp_obj->get_pon_oid("dot3StatsIndex", "OLT");
			$output = $session->walk($dot3StatsIndex);
			print "<div class=\"text-center\"><div class=\"table-responsive col-lg-11\"><table class=\"table text-center \"><tr>";
			$end = "0";
			foreach ($output as $oid => $index) {
				$ifDescr = str_replace("\"", "", $session->get($snmp_obj->get_pon_oid("ifDescr", "OLT"). "." . $index));
				$rrd_name = dirname(__FILE__) . "/rrd/" . $ip_address . "_". $index . "_traffic.rrd";
				$opts = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=b/s", "--title=$ifDescr",
				"DEF:inoctets=$rrd_name:input:AVERAGE",
				"DEF:outoctets=$rrd_name:output:AVERAGE",
				"CDEF:inbits=inoctets,8,*",
				"CDEF:outbits=outoctets,8,*",
				"AREA:inbits#00FF00:In traffic",
				"LINE1:outbits#0000FF:Out traffic\\r",
				"GPRINT:inbits:MAX:IN Max\: %6.2lf%Sbps",
				"COMMENT:  ",
				"GPRINT:inbits:AVERAGE:Avg\: %6.2lf%Sbps",
				"COMMENT:  ",
				"GPRINT:inbits:LAST:Last\: %6.2lf%Sbps\\r",
				"COMMENT:\\n",
				"GPRINT:outbits:MAX:OUT Max\: %6.2lf%Sbps",
				"COMMENT:  ",
				"GPRINT:outbits:AVERAGE:Avg\: %6.2lf%Sbps",
				"COMMENT:  ",
				"GPRINT:outbits:LAST:Last\: %6.2lf%Sbps\\r"
				);
		
				$rrd_traffic_url = $ip_address . "_" . $index . "_traffic.gif";
				$rrd_traffic = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $index . "_traffic.gif";		
				$ret = rrd_graph($rrd_traffic, $opts);
				if( !is_array($ret) )
				{
					$err = rrd_error();
					echo "rrd_graph() ERROR: $err\n";
				}
				
				print "<td><p onClick=\"graph_olt('". $ip_address . "', '" . $index . "', '" . $ifDescr . "');\"><img src=\"rrd/" . $rrd_traffic_url . "\"></img></p></td>";
				$end++;
				if ($end == "2") {
					$end = "0";
					print "</tr><tr>";
				}
			}	
			$rrd_name_temp = dirname(__FILE__) . "/rrd/" . $ip_address . "_temp.rrd";
			$opts = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=Temperature (°C)", "--title=OLT Temperature",
				"DEF:temp=$rrd_name_temp:temp:AVERAGE",
				"LINE2:temp#FF0000:Temp",
				"GPRINT:temp:MAX:Max\: %8.0lf°C",
				"COMMENT:  ",
				"GPRINT:temp:MIN:Min\: %8.0lf°C",
				"COMMENT:  ",
				"GPRINT:temp:LAST:Last\: %8.0lf°C\\r"				
			);
		
			$rrd_temp_url = $ip_address . "_temp.gif";
			$rrd_temp = dirname(__FILE__) . "/rrd/" . $ip_address . "_temp.gif";		
			$ret = rrd_graph($rrd_temp, $opts);
			if( !is_array($ret) )
			{
				$err = rrd_error();
				echo "rrd_graph() ERROR: $err\n";
			}
			print "<td><img src=\"rrd/" . $rrd_temp_url . "\"></img></td>";
			$end++;
			if ($end == "2") {
				$end = "0";
				print "</tr><tr>";
			}
			$rrd_name_cpu = dirname(__FILE__) . "/rrd/" . $ip_address . "_cpu.rrd";
			$opts = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=Utilization %", "--title=OLT CPU");
			$snmp_obj = new snmp_oid();
			$olt_cpu_oid = $snmp_obj->get_pon_oid("olt_cpu_oid", "OLT");
			snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
			snmp_set_quick_print(TRUE);
			snmp_set_enum_print(TRUE);
			snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
			$session = new SNMP(SNMP::VERSION_1, $ip_address, $ro);
			$cpus = $session->walk($olt_cpu_oid);
			$olt_cpu = "";
			foreach ($cpus as $cpu_oid => $cpu) {
				$slot = str_replace($olt_cpu_oid, '', substr($cpu_oid, 0, -1));
				$slot = str_replace('.','',$slot);
				array_push($opts, "DEF:cpu$slot=$rrd_name_cpu:cpu$slot:AVERAGE");
			}
			foreach ($cpus as $cpu_oid => $cpu) {
				$slot = str_replace($olt_cpu_oid, '', substr($cpu_oid, 0, -1));
				$slot = str_replace('.','',$slot);
				$color = "#" . substr(md5(mt_rand()), 0, 6);
				array_push($opts,
					"LINE2:cpu$slot$color:CPU$slot",
					"GPRINT:cpu$slot:MAX:Max\: %8.0lf%%",
					"COMMENT:  ",
					"GPRINT:cpu$slot:MIN:Min\: %8.0lf%%",
					"COMMENT:  ",
					"GPRINT:cpu$slot:LAST:Last\: %8.0lf%% \\r"				
				);
			}
			$rrd_cpu_url = $ip_address . "_cpu.gif";
			$rrd_cpu = dirname(__FILE__) . "/rrd/" . $ip_address . "_cpu.gif";		
			$ret = rrd_graph($rrd_cpu, $opts);
			if( !is_array($ret) )
			{
				$err = rrd_error();
				echo "rrd_graph() ERROR: $err\n";
			}
			print "<td><img src=\"rrd/" . $rrd_cpu_url . "\"></img></td>";
			print "</tr>";
			print "<table></div></div>";
		}
	}
}
?>

