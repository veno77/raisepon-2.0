<?php
include("db_connect_class.php");
include ("snmp_class.php");
class customers {
	private $customers_id;
	private $name;
	private $sn;
	private $address;
	private $egn;
	private $old_ports;
	private $service;
	private $old_service;
	private $olt;
	private $old_olt;
	private $pon_port;
	private $old_pon_port;
	private $old_pon_onu_id;
	private $line_profile;
	private $service_profile;
	private $submit;
	private $big_onu_id;
	private $olt_ip_address;
	private $pon_onu_id;
	private $pon_type;
	
	function __construct() {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$this->customers_id = !empty($_POST['customers_id'])	? $this->test_input($_POST['customers_id']) : null;
			$this->name = !empty($_POST['name'])	? $this->test_input($_POST['name']) : null;
			$this->sn = !empty($_POST['sn'])	? $this->test_input($_POST['sn']) : null;
			$this->address = !empty($_POST['address'])	? $this->test_input($_POST['address']) : null;
			$this->egn = !empty($_POST['egn']) ? $this->test_input($_POST['egn']) : 0;
			$this->old_ports = !empty($_POST['old_ports'])	? $this->test_input($_POST['old_ports']) : null;
			$this->service = !empty($_POST['service']) ? $this->test_input($_POST['service']) : null;
			$this->olt = !empty($_POST['olt']) ? $this->test_input($_POST['olt']) : null;
			$this->old_olt = !empty($_POST['old_olt']) ? $this->test_input($_POST['old_olt']) : null;
			$this->pon_port = !empty($_POST['pon_port']) ? $this->test_input($_POST['pon_port']) : null;
			$this->old_pon_port = !empty($_POST['old_pon_port']) ? $this->test_input($_POST['old_pon_port']) : null;
			$this->old_pon_onu_id = !empty($_POST['old_pon_onu_id']) ? $this->test_input($_POST['old_pon_onu_id']) : null;
			$this->line_profile = !empty($_POST['line_profile']) ? $this->test_input($_POST['line_profile']) : null;
			$this->service_profile = !empty($_POST['service_profile']) ? $this->test_input($_POST['service_profile']) : null;
			$this->submit = !empty($_POST['SUBMIT'])	? $this->test_input($_POST['SUBMIT']) : null;
		}
		
		if ($_SERVER["REQUEST_METHOD"] == "GET") {
			$this->olt_id = !empty($_GET['id'])	? $this->test_input($_GET['id']) : null;
		}
		
	}
	
	function getSubmit() {
		return $this->submit;
	}
	function getCustomers_id() {
		return $this->customers_id;
	}
	function getPon_type() {
		return $this->pon_type;
	}
	function getService() {
		return $this->service;
	}
	function getOldservice() {
		return $this->old_service;
	}
	function getName() {
		return $this->name;
	}
	
	function getSn() {
		return $this->sn;
	}
	
	function getAddress() {
		return $this->address;
	}
	function getEgn() {
		return $this->egn;
	}
	function getOld_ports() {
		return $this->old_ports;
	}
	function getOlt() {
		return $this->olt;
	}
	function getOld_olt() {
		return $this->old_olt;
	}
	function getPon_port() {
		return $this->pon_port;
	}
	function getOld_pon_port() {
		return $this->old_pon_port;
	}
	function getOld_pon_onu_id() {
		return $this->old_pon_onu_id;
	}

	
	function add_customer() {
		// FIND FREE ID
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT PON_ONU_ID from CUSTOMERS where OLT='$this->olt' and PON_PORT='$this->pon_port'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$arr2 = array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        	array_push($arr2, $row{'PON_ONU_ID'});
		}
		$arr1 = range(1,128);
		$arr3 = array_diff($arr1,$arr2);
		$arr3 = array_filter($arr3);
		if (empty($arr3)) {
			$error = "Not Free ONU IDs";
			return $error;
		}
		$pon_onu_id = array_values($arr3)[0];
		
		// CHECK Serial Number for duplicates
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT SN from CUSTOMERS where SN = '$this->sn'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
	        return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["SN"]) {
				$error ="DUPLICATE SERIAL NUMBER";
				return $error;
			}
        }
		//ADD CUSTOMER IN DATABASE
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("INSERT INTO CUSTOMERS (NAME, ADDRESS, EGN, OLT, PON_PORT, PON_ONU_ID, SERVICE, SN) VALUES ('$this->name', '$this->address', '$this->egn', '$this->olt', '$this->pon_port', '$pon_onu_id', '$this->service', '$this->sn')");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		//ADD_ONU_VIA_SNMP
		$error = $this->add_onu_via_snmp($this->sn);
		if (!empty($error)) {
			return $error;
		}
		//CREATE RRD
		//TRAFFIC RRD
		$error = $this->onu_traffic_rrd($this->olt_ip_address, $this->big_onu_id);
		if (!empty($error)) {
			return $error;
		}
		//ONU ETH PORTS RRD
		$error = $this->onu_eth_ports_rrd($this->ports, $this->olt_ip_address, $this->big_onu_id);
		if (!empty($error)) {
			return $error;
		}
		
		// ONU POWER RRD
		$error = $this->onu_power_rrd($this->olt_ip_address, $this->big_onu_id); 
		if (!empty($error)) {
			return $error;
		}
	}
	 
	
	function edit_customer() {
		if ($this->olt == $this->old_olt && $this->pon_port == $this->old_pon_port) {
			$pon_id = $this->old_pon_onu_id ;
		} else {
			// FIND FREE ONU_ID
			try {
				$conn = db_connect::getInstance();
				$result = $conn->db->query("SELECT PON_ONU_ID from CUSTOMERS where OLT='$this->olt' and PON_PORT='$this->pon_port'");
        	} catch (PDOException $e) {
        		$error = "Connection Failed:" . $e->getMessage() . "\n";
        		return $error;
        	}
        	$arr2 = array();
        	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        	array_push($arr2, $row{'PON_ONU_ID'});
        	}
        	$arr1 = range(1,128);
        	$arr3 = array_diff($arr1,$arr2);
        	$pon_id = array_values($arr3)[0];
		}
		
		// CHECK Serial Number for duplicates
			try {
				$conn = db_connect::getInstance();
				$result = $conn->db->query("SELECT SN from CUSTOMERS where SN = '$this->sn' and ID != '$this->customers_id'");
			} catch (PDOException $e) {
				$error = "Connection Failed:" . $e->getMessage() . "\n";
				return $error;
			}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["SN"])
			$error = "DUPLICATE SERIAL NUMBER";
			return $error;
		}
		// UPDATE CUSTOMER	
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("UPDATE CUSTOMERS SET NAME = '$this->name', ADDRESS = '$this->address', EGN = '$this->egn', OLT = '$this->olt', PON_PORT = '$this->pon_port', PON_ONU_ID = '$pon_id', SN = '$this->sn', SERVICE = '$this->service' where ID = '$this->customers_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}

		//DELETE OLD ONU in OLT VIA SNMP
		$error = $this->delete_onu_via_snmp();
		if (!empty($error)) {
			return $error;
		}
		sleep(1);
		//ADD_ONU_VIA_SNMP
		$error = $this->add_onu_via_snmp($this->sn);
		if (!empty($error)) {
			return $error;
		}
		
		//RENAME OLD TRAFFIC RRD FILES
		if ($this->olt != $this->old_olt || $this->pon_port != $this->old_pon_port) {
			$traffic = array("traffic", "unicast", "broadcast", "multicast", "power");
			foreach ($traffic as $tr) {
				$old_rrd_file = dirname(dirname(__FILE__)) . "/rrd/" . $old_olt_ip_address . "_" . $this->old_big_onu_id . "_" . $tr . ".rrd";
				$new_rrd_file = dirname(__FILE__) . "/rrd/" . $this->olt_ip_address . "_" . $this->big_onu_id . "_" . $tr . ".rrd";
				rename($old_rrd_file, $new_rrd_file);
			}
		}
		//ONU ETH PORTS RRD
		if ($this->ports != $this->old_ports) {
			for ($i=1; $i <= $this->old_ports; $i++) {
				$old_rrd_file = dirname(__FILE__) . "/rrd/" . $old_olt_ip_address . "_" . $this->old_big_onu_id . "_ethernet_" . $i . ".rrd";
				unlink($old_rrd_file);
			}
			
			$error = $this->onu_eth_ports_rrd($this->ports, $this->olt_ip_address, $this->big_onu_id);
			if (!empty($error)) {
				return $error;
			}
		}
        // ONU POWER RRD
		$error = $this->onu_power_rrd($this->olt_ip_address, $this->big_onu_id); 
		if (!empty($error)) {
			return $error;
		}

	}
	
	function delete_customer() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME, ADDRESS, EGN, SN from CUSTOMERS where ID='$this->customers_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$id = $row["ID"];
			$name = $row["NAME"];
			$address = $row["ADDRESS"];
			$egn = $row["EGN"];
			$sn = $row["SN"];
		}
		//DELETE CUSTOMER FROM DATABASE
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("DELETE FROM CUSTOMERS where ID='$this->customers_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		//DESTROY ONU in OLT
		//DELETE OLD ONU in OLT VIA SNMP
		$error = $this->delete_onu_via_snmp();
		if (!empty($error)) {
			return $error;
		}
		//DELETE RRD FILES
		array_map('unlink', glob(dirname(dirname(__FILE__)) . "/rrd/" . $this->olt_ip_address . "_" . $this->big_onu_id . "*"));

	}
	
	function build_table_olt() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT OLT.ID, OLT.NAME, OLT.MODEL, INET_NTOA(IP_ADDRESS) as IP_ADDRESS, RO, RW, OLT_MODEL.NAME as OLT_NAME,OLT_MODEL.TYPE as TYPE from OLT LEFT JOIN OLT_MODEL on OLT.MODEL = OLT_MODEL.ID");
		} catch (PDOException $e) {
			$error =  "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
			
	} 
	
	
	function get_data_customer() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME, ADDRESS, EGN, PON_ONU_ID, OLT, PON_PORT, SN, SERVICE, SERVICE_PROFILE.PORTS from CUSTOMERS LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN SERVICE_PROFILE on SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID where CUSTOMERS.ID='$this->customers_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->name = $row["NAME"];
			$this->address = $row["ADDRESS"];
			$this->egn = $row["EGN"];
			$this->old_pon_onu_id = $row["PON_ONU_ID"];
			$this->pon_port = $row["PON_PORT"];
			$this->old_olt = $row["OLT"];
			$this->old_pon_port = $row["PON_PORT"];
			$this->sn = $row["SN"];
			$this->old_service = $row["SERVICE"];
			$this->old_ports = $row["PORTS"];
		}	
		
		
	}
	function update_history($rsn, $cur_user_id) {
		// ADD DATETIME in HISTORY
		if ($rsn == "add") {
			$reason = "Added New Customer";
		} elseif ($rsn == "edit") {
			$reason = "Edit Customer";
		} else {
			$reason = "";
		}
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("INSERT INTO HISTORY (CUSTOMERS_ID, DATE, ACTION, SN, USER_ID) VALUES ('$this->customers_id', NOW(), '$reason', '$this->sn', '$cur_user_id')");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	
	}
	function get_Olt_model($olt) {
		if (!empty($this->olt)) {
			$where = "WHERE ID =" . $this->olt;
		} elseif ($olt) {
			$where = "WHERE ID =" . $olt;
		}else{
			$where = "";
		}
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME from OLT " . $where);
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
	}
	  
	function get_Onu_models() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT * from ONU");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
	}
	
	
	
	function get_Pon_ports() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME, SLOT_ID, PORT_ID from PON where OLT='$this->old_olt' order by SLOT_ID, PORT_ID");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;	
	}
	function get_Illegal_onus() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, INET_NTOA(IP_ADDRESS) as IP_ADDRESS, RO from OLT");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		$all_olt_illegal = array();
		foreach ($rows as $row) {
			$snmp_obj = new snmp_oid();
			$illegal_onu_mac_address_oid = $snmp_obj->get_pon_oid("illegal_onu_mac_address_oid", "EPON");
			$illegal_onu_login_time_oid = $snmp_obj->get_pon_oid("illegal_onu_login_time_oid", "EPON");
			
			snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
			snmp_set_quick_print(TRUE);
			snmp_set_enum_print(TRUE);
			snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
			$session = new SNMP(SNMP::VERSION_2C, $row["IP_ADDRESS"], $row["RO"], 100000);
			$status = $session->get($snmp_obj->get_pon_oid("olt_status_oid", "OLT"));
			if ($status) {
				$output = $session->walk($illegal_onu_mac_address_oid);
				$one_olt = array();
				foreach ($output as $mac_oid => $mac_address) {
					$mac_address_arr = explode(" ", $mac_address);
					$new_mac_addr_arr = array();
					foreach ($mac_address_arr as $mac_addr) {
						$mac_addr = hexdec($mac_addr);
						array_push($new_mac_addr_arr, $mac_addr);
					}
					array_pop($new_mac_addr_arr);
					$mac_address_bin = implode(".", $new_mac_addr_arr);
					$search = array(" ", "\"");
					$mac_address = str_replace($search, "", $mac_address);
					$pon_interface = str_replace(".", "", str_replace($mac_address_bin, "", str_replace($illegal_onu_mac_address_oid, "", $mac_oid)));
					$pon_port = bindec(substr(decbin($pon_interface), -6));
					$slot = bindec(substr(decbin($pon_interface), 0, -6));
					$session = new SNMP(SNMP::VERSION_2C, $row["IP_ADDRESS"], $row["RO"]);
					$time = str_replace($search, "", $session->get($illegal_onu_login_time_oid . "." . $pon_interface . "." . $mac_address_bin));
					$year = hexdec(substr($time, 0, 4));
					$month = str_pad(hexdec(substr($time, 4,2)), 2, "0", STR_PAD_LEFT);
					$day = str_pad(hexdec(substr($time, 6,2)), 2, "0", STR_PAD_LEFT);
					$hour = str_pad(hexdec(substr($time, 8,2)), 2, "0", STR_PAD_LEFT);
					$minute = str_pad(hexdec(substr($time, 10,2)), 2, "0", STR_PAD_LEFT);
					$seconds = str_pad(hexdec(substr($time, 12,2)), 2, "0", STR_PAD_LEFT);
					$time = $year . "-" . $month . "-" . $day . "," . $hour . ":" . $minute . ":" . $seconds;
					
					array_push($one_olt, array($mac_address, $slot, $pon_port, $time));
				}
				$all_olt_illegal[$row["ID"]] = $one_olt;
			}	
		}
		
		return $all_olt_illegal;

		
	}
	function get_Service() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME from SERVICES");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;	
	}
	
	private function delete_onu_via_snmp() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT INET_NTOA(IP_ADDRESS) as IP_ADDRESS, RW from OLT where OLT.ID='$this->old_olt'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
					$this->olt_ip_address = $row["IP_ADDRESS"];
					$olt_rw = $row["RW"];
		}
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT PORT_ID, SLOT_ID, CARDS_MODEL.PON_TYPE from PON LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where PON.ID='$this->old_pon_port'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$pon_interface = $row["PORT_ID"];
			$slot_id = $row["SLOT_ID"];
			$pon_type = $row["PON_TYPE"];
		}
		$this->big_onu_id = type2id($slot_id, $pon_interface, $this->old_pon_onu_id);
		$snmp_obj = new snmp_oid();
		$destroy_oid = $snmp_obj->get_pon_oid("row_status_oid", $pon_type) . "." . $this->big_onu_id;
        $session = new SNMP(SNMP::VERSION_2C, $this->olt_ip_address, $olt_rw);
		$session->set($destroy_oid,'i', '6');
	    if ($session->getError()) {
			$error = var_dump($session->getError());
			return $error;
		}
		$session->close();
	}
	private function add_onu_via_snmp($sn) {
		// PREPARE SNMPSET TO ADD ONU
        try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT CUSTOMERS.ID as C_ID, CUSTOMERS.NAME, CUSTOMERS.SN, PON_ONU_ID, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, CUSTOMERS.SERVICE, SERVICES.LINE_PROFILE_ID, SERVICES.SERVICE_PROFILE_ID, OLT.ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RW as RW, PON.ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE, LINE_PROFILE.LINE_PROFILE_ID, SERVICE_PROFILE.SERVICE_PROFILE_ID, SERVICE_PROFILE.PORTS as PORTS from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN LINE_PROFILE on SERVICES.LINE_PROFILE_ID=LINE_PROFILE.ID LEFT JOIN SERVICE_PROFILE on SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where CUSTOMERS.SN = '$sn'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->olt_ip_address = $row["IP_ADDRESS"];
			$olt_rw = $row["RW"];
			$port_id = $row["PORT_ID"];
			$this->pon_onu_id = $row["PON_ONU_ID"];
			$this->sn = $row["SN"];
			$slot_id = $row["SLOT_ID"];
			$this->ports = $row["PORTS"];
			$line_profile_id = $row["LINE_PROFILE_ID"];
			$service_profile_id = $row["SERVICE_PROFILE_ID"];
			$this->name = $row["NAME"];
			$this->customers_id = $row["C_ID"];
			$this->pon_type = $row["PON_TYPE"];

		}
		$snmp_obj = new snmp_oid();
		$this->big_onu_id = type2id($slot_id, $port_id, $this->pon_onu_id);
		$sn_oid = $snmp_obj->get_pon_oid("onu_sn_oid", $this->pon_type) . "." . $this->big_onu_id;
		$line_profile_oid = $snmp_obj->get_pon_oid("line_profile_oid", $this->pon_type) . "." . $this->big_onu_id;
		$service_profile_oid = $snmp_obj->get_pon_oid("service_profile_oid", $this->pon_type) . "." . $this->big_onu_id;
		$row_status_oid = $snmp_obj->get_pon_oid("row_status_oid", $this->pon_type) . "." . $this->big_onu_id;
		$dtype_oid = $snmp_obj->get_pon_oid("dtype_oid", $this->pon_type) . "." . $this->big_onu_id;
		$status_oid = $snmp_obj->get_pon_oid("status_oid", $this->pon_type) . "." . $this->big_onu_id;
		$description_oid = $snmp_obj->get_pon_oid("description_oid", $this->pon_type) . "." . $this->big_onu_id;

		//EXECUTE SNMPSET TO ADD ONU
		$session = new SNMP(SNMP::VERSION_2C, $this->olt_ip_address, $olt_rw);
		if ($this->pon_type == "GPON")
		$session->set(array($sn_oid, $line_profile_oid, $service_profile_oid, $row_status_oid), array('s', 'i', 'i', 'i'), array($this->sn, $line_profile_id, $service_profile_id, '4')); 
		if ($this->pon_type = "EPON") {
		$sn = "0x" . $this->sn;	
		$session->set(array($sn_oid, $line_profile_oid, $service_profile_oid, $dtype_oid, $row_status_oid, $status_oid), array('x', 'i', 'i', 'i', 'i', 'i'), array($sn, $line_profile_id, $service_profile_id, '0', '4', '1')); 
		}
		if ($session->getError()) {
			try {
				$conn = db_connect::getInstance();
				$result = $conn->db->query("DELETE FROM CUSTOMERS where ID='$customer_id'");
			} catch (PDOException $e) {
				$error = "Connection Failed:" . $e->getMessage() . "\n";
				return $error;
			}
			$error = var_dump($session->getError());
			return $error;
		}
		$session->set($description_oid, 's', $this->name);
		if ($session->getError()) {
			$error = var_dump($session->getError());
			return $error;
		}
		$session->close();
		
	}
	function onu_traffic_rrd($olt_ip_address, $big_onu_id) {
		$traffic = array("traffic", "unicast", "broadcast", "multicast");
		foreach ($traffic as $tr) {
			$rrd_name = dirname(dirname(__FILE__)) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_" . $tr . ".rrd";
			$opts = array( "--step", "300", "--start", "0",
			   "DS:input:DERIVE:600:0:U",
			   "DS:output:DERIVE:600:0:U",
			   "RRA:AVERAGE:0.5:1:600",
			   "RRA:AVERAGE:0.5:6:700",
			   "RRA:AVERAGE:0.5:24:775",
			   "RRA:AVERAGE:0.5:288:797",
			   "RRA:MAX:0.5:1:600",
			   "RRA:MAX:0.5:6:700",
			   "RRA:MAX:0.5:24:775",
			   "RRA:MAX:0.5:288:797"
			);
			$ret = rrd_create($rrd_name, $opts);

			if( $ret == 0 )
			{
				$err = rrd_error();
				return $err;
			}
		}
	}
	
	function onu_eth_ports_rrd($ports, $olt_ip_address, $big_onu_id) {
		//ETHERNET PORTS RRD
		for ($i=1; $i <= $ports; $i++) {
			$rrd_name = dirname(dirname(__FILE__)) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_ethernet_" . $i . ".rrd";
			$opts = array( "--step", "300", "--start", "0",
			   "DS:input:DERIVE:600:0:U",
			   "DS:output:DERIVE:600:0:U",
			   "RRA:AVERAGE:0.5:1:600",
			   "RRA:AVERAGE:0.5:6:700",
			   "RRA:AVERAGE:0.5:24:775",
			   "RRA:AVERAGE:0.5:288:797",
			   "RRA:MAX:0.5:1:600",
			   "RRA:MAX:0.5:6:700",
			   "RRA:MAX:0.5:24:775",
			   "RRA:MAX:0.5:288:797"
			);
			$ret = rrd_create($rrd_name, $opts);

			if( $ret == 0 )
			{
				$err = rrd_error();
				return $err;
			}	
		}
	
	}
	
	function onu_power_rrd($olt_ip_address, $big_onu_id) {
		$rrd_name = dirname(dirname(__FILE__)) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_power.rrd";
		$opts = array( "--step", "300", "--start", "0",
		   "DS:input:GAUGE:600:U:U",
		   "DS:output:GAUGE:600:U:U",
		   "DS:rxolt:GAUGE:600:U:U",
           "DS:rfin:GAUGE:600:U:U",
		   "RRA:AVERAGE:0.5:1:600",
		   "RRA:AVERAGE:0.5:6:700",
		   "RRA:AVERAGE:0.5:24:775",
		   "RRA:AVERAGE:0.5:288:797",
		   "RRA:MAX:0.5:1:600",
		   "RRA:MAX:0.5:6:700",
		   "RRA:MAX:0.5:24:775",
		   "RRA:MAX:0.5:288:797"
		);
		$ret = rrd_create($rrd_name, $opts);

		if( $ret == 0 )
		{
			$err = rrd_error();
			return $err;
		}
	}
	function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
	}


	
}




?>
