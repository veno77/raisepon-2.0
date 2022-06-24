<?php
include_once("db_connect_class.php");
include_once("snmp_class.php");
class customers {
	private $customers_id;
	private $name;
	private $sn;
	private $address;
	private $egn;
	private $ports;
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
	private $auto;
	private $state;
	private $old_state;
	private $state_rf;
	private $netmask;
	private $gateway;
	private $vlan;
	private $onu_ip_address;
	private $old_onu_ip_address;
	private $page;

	function __construct() {
		if (!empty($_SERVER["REQUEST_METHOD"])) {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$this->customers_id = !empty($_POST['customers_id'])	? $this->test_input($_POST['customers_id']) : null;
				$this->name = !empty($_POST['name'])	? $this->test_input($_POST['name']) : null;
				$this->sn = !empty($_POST['sn'])	? $this->test_input($_POST['sn']) : null;
				$this->address = !empty($_POST['address'])	? $this->test_input($_POST['address']) : null;
				$this->egn = !empty($_POST['egn']) ? $this->test_input($_POST['egn']) : null;
				$this->old_ports = !empty($_POST['old_ports'])	? $this->test_input($_POST['old_ports']) : null;
				$this->service = !empty($_POST['service']) ? $this->test_input($_POST['service']) : null;
				$this->olt = !empty($_POST['olt']) ? $this->test_input($_POST['olt']) : null;
				$this->old_olt = !empty($_POST['old_olt']) ? $this->test_input($_POST['old_olt']) : null;
				$this->pon_port = !empty($_POST['pon_port']) ? $this->test_input($_POST['pon_port']) : null;
				$this->old_pon_port = !empty($_POST['old_pon_port']) ? $this->test_input($_POST['old_pon_port']) : null;
				$this->old_pon_onu_id = !empty($_POST['old_pon_onu_id']) ? $this->test_input($_POST['old_pon_onu_id']) : null;
				$this->line_profile = !empty($_POST['line_profile']) ? $this->test_input($_POST['line_profile']) : null;
				$this->service_profile = !empty($_POST['service_profile']) ? $this->test_input($_POST['service_profile']) : null;
				$this->auto = !empty($_POST['auto']) ? $this->test_input($_POST['auto']) : "NO";
				$this->state = !empty($_POST['state']) ? $this->test_input($_POST['state']) : "NO";
				$this->state_rf = !empty($_POST['state_rf']) ? $this->test_input($_POST['state_rf']) : null;
				$this->submit = !empty($_POST['SUBMIT'])	? $this->test_input($_POST['SUBMIT']) : null;
				$this->netmask = !empty($_POST['netmask']) ? $this->test_input($_POST['netmask']) : null;
				$this->gateway = !empty($_POST['gateway']) ? $this->test_input($_POST['gateway']) : null;
				$this->vlan = !empty($_POST['vlan']) ? $this->test_input($_POST['vlan']) : null;
				$this->onu_ip_address = !empty($_POST['onu_ip_address']) ? $this->test_input($_POST['onu_ip_address']) : null;
				$this->old_onu_ip_address = !empty($_POST['old_onu_ip_address']) ? $this->test_input($_POST['old_onu_ip_address']) : null;
				$this->page = !empty($_POST['page']) ? $this->test_input($_POST['page']) : null;
				$this->old_state = !empty($_POST['old_state']) ? $this->test_input($_POST['old_state']) : "NO";

			}
		
			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				$this->olt_id = !empty($_GET['id'])	? $this->test_input($_GET['id']) : null;
			}
		}
	}
	function getOnu_ip_address() {
		return $this->onu_ip_address;
	}
	function getSubmit() {
		return $this->submit;
	}
	
	function setSubmit($submit) {
		$this->submit = $submit;
	}
	
	function getCustomers_id() {
		return $this->customers_id;
	}
	
	function setCustomer_id($customers_id) {
		$this->customers_id = $customers_id;
	}
	
		
	function getPon_type() {
		return $this->pon_type;
	}
	function getService() {
		return $this->service;
	}
	function setService($service) {
		$this->service = $service;
	}
	function getOldservice() {
		return $this->old_service;
	}
	function getName() {
		return $this->name;
	}
	function setName($name) {
		$this->name = $name;
	}
	function getSn() {
		return $this->sn;
	}
	function setSn($sn) {
		$this->sn = $sn;
	}
	function getAddress() {
		return $this->address;
	}
	function setAddress($address) {
		$this->address = $address;
	}
	function getEgn() {
		return $this->egn;
	}
	function setEgn($egn) {
		$this->egn = $egn;
	}
	function getOld_ports() {
		return $this->old_ports;
	}
	function getOld_state() {
		return $this->old_state;
	}
	function getOlt() {
		return $this->olt;
	} 
	function setOlt($olt) {
		$this->olt = $olt;
	}
	function getOld_olt() {
		return $this->old_olt;
	}
	function setOld_olt($old_olt) {
		$this->old_olt = $old_olt;
	}
	function getPon_port() {
		return $this->pon_port;
	}
	function setPon_port($pon_port) {
		$this->pon_port = $pon_port;
	}
	function getOld_pon_port() {
		return $this->old_pon_port;
	}
	function setOld_pon_port($old_pon_port) {
		$this->old_pon_port = $old_pon_port;
	}
	function getOld_pon_onu_id() {
		return $this->old_pon_onu_id;
	}
	function getAuto() {
		return $this->auto;
	}
	function setAuto($auto) {
		$this->auto = $auto;
	}
	function getState() {
		return $this->state;
	}
	function setState($state) {
		$this->state = $state;
	}
	function getState_rf() {
		return $this->state_rf;
	}
	function setState_rf($state_rf) {
		$this->state_rf = $state_rf;
	}
	function getOld_onu_ip_address() {
		return $this->old_onu_ip_address;
	}
	function getPage() {
		return $this->page;
	}
	function add_customer() {
		$pon_onu_id = $this->find_next_onu_id();
		if (!empty($this->olt)) {
			//FIND FREE IP if DEFINED IP_POOL to OLT
			try {
				$conn = db_connect::getInstance();
				$result = $conn->db->query("SELECT IP_POOL_ID from OLT_IP_POOLS where OLT_ID='$this->olt' AND SERVICE_ID = '$this->service'");
			} catch (PDOException $e) {
				$error = "Connection 1 Failed:" . $e->getMessage() . "\n";
				return $error;
			}
			if ($result->rowCount() == 0){
				try {
					$conn = db_connect::getInstance();
					$result = $conn->db->query("SELECT IP_POOL_ID from OLT_IP_POOLS where OLT_ID='$this->olt' AND SERVICE_ID IS NULL");
				} catch (PDOException $e) {
					$error = "Connection 1 Failed:" . $e->getMessage() . "\n";
					return $error;
				}
			}
			
			if ($result->rowCount() == 0){
				$this->onu_ip_address = "NULL";
			}else{
				while ($row = $result->fetch(PDO::FETCH_ASSOC)) {				
					$ip_pool_id = $row['IP_POOL_ID'];
					$this->find_free_ip($ip_pool_id);
				} 
			}
		} else {
			$this->onu_ip_address = "NULL";
		}
		// CHECK Serial Number for duplicates
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT SN from CUSTOMERS where SN = '$this->sn'");
		} catch (PDOException $e) {
			$error = "Connection 3 Failed:" . $e->getMessage() . "\n";
	        return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["SN"]) {
				$error ="DUPLICATE SERIAL NUMBER";
				return $error;
			}
        }
		//ADD CUSTOMER IN DATABASE
		if (!empty($this->egn)) {
			$egn = "'" . $this->egn . "'";
		}else{
			$egn = "NULL";
		}
		if (!empty($this->service)) {
			$service = "'" . $this->service . "'";
		}else{
			$service = "NULL";
			$this->onu_ip_address = "NULL";
		}
		if (!empty($this->olt)) {
			$olt = "'" . $this->olt . "'";
		}else{
			$olt = "NULL";
		}
		if (!empty($this->pon_port)) {
			$pon_port = "'" . $this->pon_port . "'";
		}else{
			$pon_port = "NULL";
		}
		if (!empty($this->pon_port)) {
			$pon_port = "'" . $this->pon_port . "'";
		}else{
			$pon_port = "NULL";
		}
		if (!empty($this->state_rf)) {
			$state_rf = "'" . $this->state_rf . "'";
		}else{
			$state_rf = "NULL";
		}
		
		if($this->onu_ip_address != "NULL") {
			$onu_ip_address = "'" . $this->onu_ip_address . "'";
		} else {
			$onu_ip_address = $this->onu_ip_address;
		}
		
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("INSERT INTO CUSTOMERS (NAME, ADDRESS, EGN, OLT, PON_PORT, PON_ONU_ID, SERVICE, SN, IP_ADDRESS, AUTO, STATE, STATE_RF) VALUES ('$this->name', '$this->address', $egn, $olt, $pon_port, $pon_onu_id, $service, '$this->sn', $onu_ip_address, '$this->auto', '$this->state', $state_rf)");
		} catch (PDOException $e) {
			$error = "Connection 4 Failed:" . $n . "__" . $e->getMessage() . "\n";
			return $error;
		}

		//ADD_ONU_VIA_SNMP
		if (!empty($this->olt) && !empty($this->pon_port)) {
			$error = $this->add_onu_via_snmp($this->sn);
			if (!empty($error)) {
				return $error;
			}
			//SET NAME
			$error = $this->set_name();
			if (!empty($error)) {
				return $error;
			}
			//CREATE RRD
			//TRAFFIC RRD
			$error = $this->onu_traffic_rrd();
			if (!empty($error)) {
				return $error;
			}
			//ONU ETH PORTS RRD
			$error = $this->onu_eth_ports_rrd();
			if (!empty($error)) {
				return $error;
			}
			
			// ONU POWER RRD
			$error = $this->onu_power_rrd(); 
			if (!empty($error)) {
				return $error;
			}
		}
	}
	 
	
	function edit_customer() {
		if ($this->olt == $this->old_olt && $this->pon_port == $this->old_pon_port) {
			if (!empty($this->old_pon_onu_id)) {
				$pon_onu_id = $this->old_pon_onu_id ;
			}else{
				$pon_onu_id = "NULL";
			}
		} else {
			// FIND FREE ONU_ID
			$pon_onu_id = $this->find_next_onu_id();
		}
		if (!empty($this->olt)) {
			//FIND FREE IP if DEFINED IP_POOL to OLT
			try {
				$conn = db_connect::getInstance();
				$result = $conn->db->query("SELECT IP_POOL_ID from OLT_IP_POOLS where OLT_ID='$this->olt' AND SERVICE_ID = '$this->service'");
			} catch (PDOException $e) {
				$error = "Connection 1 Failed:" . $e->getMessage() . "\n";
				return $error;
			}
			if ($result->rowCount() == 0){
				try {
					$conn = db_connect::getInstance();
					$result = $conn->db->query("SELECT IP_POOL_ID from OLT_IP_POOLS where OLT_ID='$this->olt' AND SERVICE_ID IS NULL");
				} catch (PDOException $e) {
					$error = "Connection 1 Failed:" . $e->getMessage() . "\n";
					return $error;
				}
			}
			
			if ($result->rowCount() == 0){
				$this->onu_ip_address = "NULL";
			}else{
				while ($row = $result->fetch(PDO::FETCH_ASSOC)) {		
					$ip_pool_id = $row['IP_POOL_ID'];
					$this->find_free_ip($ip_pool_id);
				} 
			}
		} else {
			$this->onu_ip_address = "NULL";
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
	
		if (!empty($this->egn)) {
			$egn = "'" . $this->egn . "'";
		}else{
			$egn = "NULL";
		}
		if (!empty($this->service)) {
			$service = "'" . $this->service . "'";
		}else{
			$service = "NULL";
			$this->onu_ip_address = "NULL";
		}
		if (!empty($this->olt)) {
			$olt = "'" . $this->olt . "'";
		}else{
			$olt = "NULL";
		}
		if (!empty($this->pon_port)) {
			$pon_port = "'" . $this->pon_port . "'";
		}else{
			$pon_port = "NULL";
		}
		if (!empty($this->state_rf)) {
			$state_rf = "'" . $this->state_rf . "'";
		}else{
			$state_rf = "NULL";
		}
		
		if($this->onu_ip_address != "NULL") {
			$onu_ip_address = "'" . $this->onu_ip_address . "'";
		} else {
			$onu_ip_address = $this->onu_ip_address;
		}
		
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("UPDATE CUSTOMERS SET NAME = '$this->name', ADDRESS = '$this->address', EGN = $egn, OLT = $olt, PON_PORT = $pon_port, PON_ONU_ID = $pon_onu_id, SN = '$this->sn', IP_ADDRESS = $onu_ip_address, SERVICE = $service, AUTO = '$this->auto', STATE = '$this->state', STATE_RF = $state_rf  where ID = '$this->customers_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;	
		}
		//CHANGE NAME
		$error = $this->set_name();
		if (!empty($error)) {
				return $error;
			}
		//CHANGE STATE
		if ($this->old_state != $this->state) {
			$error = $this->set_state();
			if (!empty($error)) {
				return $error;
				try {
					$conn = db_connect::getInstance();
					$result = $conn->db->query("UPDATE CUSTOMERS SET STATE = '$this->old_state' where ID = '$this->customers_id'");
				} catch (PDOException $e) {
					$error = "Connection Failed:" . $e->getMessage() . "\n";
					return $error;	
				}
			}
		}
			
		//RECREATE ONU IF OLT and PON port CHANGED
		if (($this->old_olt != $this->olt) && ($this->old_pon_port != $this->pon_port)	) {
			$error = $this->delete_onu_via_snmp();
			if (!empty($error)) {
				return $error;
			}
			
		//UNLINK OLD RRD
		//	array_map('unlink', glob(dirname(dirname(__FILE__)) . "/rrd/" . $this->sn . "*"));
			sleep(1);
			$error = $this->add_onu_via_snmp($this->sn);
			if (!empty($error)) {
				return $error;
			}
		} 	
	}
	
	
	function delete_customer() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME, ADDRESS, EGN, SN, OLT from CUSTOMERS where ID='$this->customers_id'");
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
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("DELETE FROM ONU_RX_POWER where CUSTOMERS_ID='$this->customers_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		
		//DESTROY ONU in OLT
		
	
		$error = $this->delete_onu_via_snmp();
		if (!empty($error)) {
			return $error;
		}
		//DELETE RRD FILES
		array_map('unlink', glob(dirname(dirname(__FILE__)) . "/rrd/" . $this->sn . "*"));

	}
	
	function check_Sn($sn) {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, SN from CUSTOMERS where SN = '$sn'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["SN"])
			return $row["ID"];
		}
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
		if (isset($this->customers_id)) 
			$where = "CUSTOMERS.ID='" . $this->customers_id . "'";
		if (isset($this->sn)) 
			$where = "CUSTOMERS.SN='" . $this->sn . "'";
		
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME, ADDRESS, EGN, PON_ONU_ID, OLT, PON_PORT, SN, IP_ADDRESS, SERVICE, AUTO, STATE, STATE_RF, SERVICE_PROFILE.PORTS from CUSTOMERS LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN SERVICE_PROFILE on SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID where " . $where);
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->customers_id = $row["ID"];
			$this->name = $row["NAME"];
			$this->address = $row["ADDRESS"];
			$this->egn = $row["EGN"];
			$this->old_pon_onu_id = $row["PON_ONU_ID"];
			$this->old_olt = $row["OLT"];
			$this->old_pon_port = $row["PON_PORT"];
			$this->sn = $row["SN"];
			$this->old_service = $row["SERVICE"];
			$this->old_ports = $row["PORTS"];
			$this->auto = $row["AUTO"];
			$this->old_state = $row["STATE"];
			$this->service = $row["SERVICE"];
			$this->state_rf = $row["STATE_RF"];
			$this->old_onu_ip_address = $row["IP_ADDRESS"];
		}	
	}
	
	
	function get_data() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME, ADDRESS, EGN, SN, SERVICE, AUTO, STATE, STATE_RF from CUSTOMERS");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		return $result;
		
	}
	
	function update_history($rsn, $cur_user_id) {
		// ADD DATETIME in HISTORY
		if ($rsn == "add") {
			$reason = "Added New Customer";
		} elseif ($rsn == "edit") {
			$reason = "Edit Customer";
		} elseif ($rsn == "reboot") {
			$reason = "Reboot ONU";
		} else {
			$reason = $rsn;
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
	function get_Olt_models() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME from OLT ");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
	}
	function get_Pon_port($olt, $slot, $port) {
		
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID from PON WHERE OLT=" . $olt . " AND SLOT_ID=" . $slot . " AND PORT_ID=" . $port);
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
	}
	function check_Auto($sn) {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, AUTO from CUSTOMERS where SN = '$sn'");
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
		if (!isset($this->old_olt))
			$this->old_olt = $this->olt;
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
			$result = $conn->db->query("SELECT OLT.ID, INET_NTOA(IP_ADDRESS) as IP_ADDRESS, MODEL, RO, OLT_MODEL.TYPE as TYPE from OLT LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		$all_olt_illegal = array();
		foreach ($rows as $row) {
			$snmp_obj = new snmp_oid();
			snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
			snmp_set_quick_print(TRUE);
			snmp_set_enum_print(TRUE);
			snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
			$session = new SNMP(SNMP::VERSION_2C, $row["IP_ADDRESS"], $row["RO"], 100000);
			$status = $session->get($snmp_obj->get_pon_oid("olt_status_oid", "OLT"));
			if ($status) {
				if (($row['TYPE'] == "EPON") || ($row['TYPE'] == "XPON")) {
					//EPON
					$illegal_onu_mac_address_oid = $snmp_obj->get_pon_oid("illegal_onu_mac_address_oid", "EPON");
					$illegal_onu_login_time_oid = $snmp_obj->get_pon_oid("illegal_onu_login_time_oid", "EPON");
					$output = @$session->walk($illegal_onu_mac_address_oid);
					if ($output) {
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
				if (($row['TYPE'] == "GPON") || ($row['TYPE'] == "XPON")) {
					//GPON
					$illegal_onu_sn_oid = $snmp_obj->get_pon_oid("illegal_onu_sn_oid", "GPON");
					$illegal_onu_login_time_oid = $snmp_obj->get_pon_oid("illegal_onu_login_time_oid", "GPON");
					snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
					snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
					snmp_set_quick_print(TRUE);
					snmp_set_enum_print(TRUE);
					$session = new SNMP(SNMP::VERSION_2C, $row["IP_ADDRESS"], $row["RO"], 100000);
					$output = @$session->walk($illegal_onu_sn_oid);
					if ($output) {
						$one_olt = array();
						foreach ($output as $sn_oid => $sn_value) {
							$sn = preg_replace("/[^A-Za-z0-9 ]/", '', $sn_value);
							$sn_array = str_split($sn_value);
							$new_sn_array = array();
							foreach ($sn_array as $sn_arr) {
								$sn_arr = ord($sn_arr);
								array_push($new_sn_array, $sn_arr);
							}
							$sn_array = implode(".", $new_sn_array);
							$pon_interface = str_replace(".", "", str_replace($sn_array, "", str_replace($illegal_onu_sn_oid, "", $sn_oid)));
							$pon_port = bindec(substr(decbin($pon_interface), -6));
							$slot = bindec(substr(decbin($pon_interface), 0, -6));
							snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
							$session = new SNMP(SNMP::VERSION_2C, $row["IP_ADDRESS"], $row["RO"], 100000);
							//$time = $session->get($illegal_onu_login_time_oid . "." . $pon_interface . "." . $sn_array);
							$search = array(" ", "\"");
							$time = str_replace($search, "", $session->get($illegal_onu_login_time_oid . "." . $pon_interface . "." . $sn_array));
							$year = hexdec(substr($time, 0, 4));
							$month = str_pad(hexdec(substr($time, 4,2)), 2, "0", STR_PAD_LEFT);
							$day = str_pad(hexdec(substr($time, 6,2)), 2, "0", STR_PAD_LEFT);
							$hour = str_pad(hexdec(substr($time, 8,2)), 2, "0", STR_PAD_LEFT);
							$minute = str_pad(hexdec(substr($time, 10,2)), 2, "0", STR_PAD_LEFT);
							$seconds = str_pad(hexdec(substr($time, 12,2)), 2, "0", STR_PAD_LEFT);
							$time = $year . "-" . $month . "-" . $day . "," . $hour . ":" . $minute . ":" . $seconds;
							array_push($one_olt, array($sn, $slot, $pon_port, $time));
						}
						$all_olt_illegal[$row["ID"]] = $one_olt;				
					}
				}
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
	
	 function delete_onu_via_snmp() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT INET_NTOA(IP_ADDRESS) as IP_ADDRESS, RW from OLT where OLT.ID='$this->old_olt'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
					$this->old_olt_ip_address = $row["IP_ADDRESS"];
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
		if (!empty($pon_interface) && !empty($slot_id)) {
			$this->old_big_onu_id = $this->type2id($slot_id, $pon_interface, $this->old_pon_onu_id);
			$snmp_obj = new snmp_oid();
			$destroy_oid = $snmp_obj->get_pon_oid("row_status_oid", $pon_type) . "." . $this->old_big_onu_id;
			$session = new SNMP(SNMP::VERSION_2C, $this->old_olt_ip_address, $olt_rw);
			$session->set($destroy_oid,'i', '6');
			if ($session->getError()) {
				$error = var_dump($session->getError());
				return $error;
			}
			$session->close();
		}
	}
	
	function set_ip($id){
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("UPDATE CUSTOMERS SET IP_ADDRESS='$this->onu_ip_address' WHERE ID='$id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			exit($error);
		}
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT CUSTOMERS.ID as C_ID, CUSTOMERS.NAME, CUSTOMERS.SN, CUSTOMERS.STATE, CUSTOMERS.STATE_RF, CUSTOMERS.AUTO, PON_ONU_ID, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, CUSTOMERS.SERVICE, SERVICES.LINE_PROFILE_ID, SERVICES.SERVICE_PROFILE_ID, OLT.ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RW as RW, PON.ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE, LINE_PROFILE.LINE_PROFILE_ID, SERVICE_PROFILE.SERVICE_PROFILE_ID, SERVICE_PROFILE.PORTS as PORTS from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN LINE_PROFILE on SERVICES.LINE_PROFILE_ID=LINE_PROFILE.ID LEFT JOIN SERVICE_PROFILE on SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where CUSTOMERS.ID = $id");
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
		$session = new SNMP(SNMP::VERSION_2C, $this->olt_ip_address, $olt_rw);
		$index = $slot_id * 10000000 + $port_id * 100000 + $this->pon_onu_id;
		$rcGponOnuNetIpAddr_oid = $snmp_obj->get_pon_oid("rcGponOnuNetIpAddr", "GPON") . "." . $index . ".1";
		$onu_ip_address = long2ip($this->onu_ip_address);
		$session->set($rcGponOnuNetIpAddr_oid, 'a', $onu_ip_address);
		$rcGponOnuNetIpMask_oid = $snmp_obj->get_pon_oid("rcGponOnuNetIpMask", "GPON") . "." . $index . ".1";
		$session->set($rcGponOnuNetIpMask_oid, 'a', $this->netmask);
		$rcGponOnuNetDefaultGateway_oid = $snmp_obj->get_pon_oid("rcGponOnuNetDefaultGateway", "GPON") . "." . $index . ".1";
		$session->set($rcGponOnuNetDefaultGateway_oid, 'a', $this->gateway);
		$rcGponOnuNetVlan_oid = $snmp_obj->get_pon_oid("rcGponOnuNetVlan", "GPON") . "." . $index . ".1";
		$session->set($rcGponOnuNetVlan_oid, 'i', $this->vlan);
	}
	
	function add_onu_via_snmp($sn) {
		// PREPARE SNMPSET TO ADD ONU
        try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT CUSTOMERS.ID as C_ID, CUSTOMERS.NAME, CUSTOMERS.SN, CUSTOMERS.STATE, CUSTOMERS.STATE_RF, CUSTOMERS.AUTO, PON_ONU_ID, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, CUSTOMERS.SERVICE, SERVICES.LINE_PROFILE_ID, SERVICES.SERVICE_PROFILE_ID, OLT.ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RW as RW, PON.ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE, LINE_PROFILE.LINE_PROFILE_ID, SERVICE_PROFILE.SERVICE_PROFILE_ID, SERVICE_PROFILE.PORTS as PORTS from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN LINE_PROFILE on SERVICES.LINE_PROFILE_ID=LINE_PROFILE.ID LEFT JOIN SERVICE_PROFILE on SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where CUSTOMERS.SN = '$sn'");
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
			$this->state = $row["STATE"];
			$this->auto = $row["AUTO"];
			$this->state_rf = $row["STATE_RF"];
		}
		$snmp_obj = new snmp_oid();
		$index = $slot_id * 10000000 + $port_id * 100000 + $this->pon_onu_id;
		$this->big_onu_id = $this->type2id($slot_id, $port_id, $this->pon_onu_id);
		$sn_oid = $snmp_obj->get_pon_oid("onu_sn_oid", $this->pon_type) . "." . $this->big_onu_id;
		$line_profile_oid = $snmp_obj->get_pon_oid("line_profile_oid", $this->pon_type) . "." . $this->big_onu_id;
		$service_profile_oid = $snmp_obj->get_pon_oid("service_profile_oid", $this->pon_type) . "." . $this->big_onu_id;
		$row_status_oid = $snmp_obj->get_pon_oid("row_status_oid", $this->pon_type) . "." . $this->big_onu_id;
		$dtype_oid = $snmp_obj->get_pon_oid("dtype_oid", $this->pon_type) . "." . $this->big_onu_id;
		$state_oid = $snmp_obj->get_pon_oid("onu_active_state_oid", $this->pon_type) . "." . $this->big_onu_id;
		//EXECUTE SNMPSET TO ADD ONU
		$session = new SNMP(SNMP::VERSION_2C, $this->olt_ip_address, $olt_rw);
		if ($this->pon_type == "GPON") {
			if (!empty($service_profile_id)) {
				$session->set(array($sn_oid, $line_profile_oid, $service_profile_oid, $row_status_oid), array('s', 'i', 'i', 'i'), array($this->sn, $line_profile_id, $service_profile_id, '4')); 
			} else {
				$session->set(array($sn_oid, $row_status_oid), array('s', 'i'), array($this->sn, '4'));
			}
		/*	if ($this->state == "NO") {
				$session->set($state_oid, 'i', '2');
			}else{
				$session->set($state_oid, 'i', '1');
			} */
			//SET IP_ADDRESS if configured.
			if ($this->onu_ip_address != "NULL") {
				$rcGponOnuNetIpAddr_oid = $snmp_obj->get_pon_oid("rcGponOnuNetIpAddr", $this->pon_type) . "." . $index . ".1";
				$onu_ip_address = long2ip($this->onu_ip_address);
				$session->set($rcGponOnuNetIpAddr_oid, 'a', $onu_ip_address);
				$rcGponOnuNetIpMask_oid = $snmp_obj->get_pon_oid("rcGponOnuNetIpMask", $this->pon_type) . "." . $index . ".1";
				$session->set($rcGponOnuNetIpMask_oid, 'a', $this->netmask);
				$rcGponOnuNetDefaultGateway_oid = $snmp_obj->get_pon_oid("rcGponOnuNetDefaultGateway", $this->pon_type) . "." . $index . ".1";
				$session->set($rcGponOnuNetDefaultGateway_oid, 'a', $this->gateway);
				$rcGponOnuNetVlan_oid = $snmp_obj->get_pon_oid("rcGponOnuNetVlan", $this->pon_type) . "." . $index . ".1";
				$session->set($rcGponOnuNetVlan_oid, 'i', $this->vlan);
			}
		}
		if ($this->pon_type == "EPON") {
			$sn = "0x" . $this->sn;	
			if (!empty($service_profile_id)) {
				if ($this->state == "YES")
					$session->set(array($sn_oid, $line_profile_oid, $service_profile_oid, $dtype_oid, $row_status_oid, $state_oid), array('x', 'i', 'i', 'i', 'i', 'i'), array($sn, $line_profile_id, $service_profile_id, '0', '4', '1')); 
				if ($this->state == "NO")
					$session->set(array($sn_oid, $line_profile_oid, $service_profile_oid, $dtype_oid, $row_status_oid, $state_oid), array('x', 'i', 'i', 'i', 'i', 'i'), array($sn, $line_profile_id, $service_profile_id, '0', '4', '2')); 
			} else {
				if ($this->state == "YES")
					$session->set(array($sn_oid, $dtype_oid, $row_status_oid, $state_oid), array('x', 'i', 'i', 'i'), array($sn, '0', '4', '1')); 
				if ($this->state == "NO")
					$session->set(array($sn_oid, $dtype_oid, $row_status_oid, $state_oid), array('x', 'i', 'i', 'i'), array($sn, '0', '4', '2')); 
			}

		}
		
		if ($session->getError()) {
			try {
				$conn = db_connect::getInstance();
				$result = $conn->db->query("DELETE FROM CUSTOMERS where ID='$this->customers_id'");
			} catch (PDOException $e) {
				$error = "Connection Failed:" . $e->getMessage() . "\n";
				return $error;
			}
			$error = var_dump($session->getError());
			return $error;
		}
		

		
		//SET RF STATE
		if (!empty($this->state_rf)) {
			if ($this->pon_type == "EPON")
				$index_rf = $slot_id * 10000000 + $port_id * 100000 + $this->pon_onu_id * 1000 + 162;						
			if ($this->pon_type == "GPON") {
				if ($this->pon_onu_id < 100) {
					$index_rf = $slot_id * 10000000 + $port_id * 100000 + $this->pon_onu_id * 1000 + 1;		
				}else{
					$index_rf = (3<<28)+($slot_id * 10000000 + $port_id * 100000 + ($this->pon_onu_id%100) * 1000 + 1);
				}
			}
			$onu_rf_status_oid = $snmp_obj->get_pon_oid("onu_rf_status_oid", $this->pon_type) . "." . $index_rf;
			snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
			$session = new SNMP(SNMP::VERSION_2C, $this->olt_ip_address, $olt_rw);
			$session->set($onu_rf_status_oid, 'i', $this->state_rf);
			if ($session->getError())
					return(var_dump($session->getError()));
		}
		//SET UNI STATE
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT UNI_PORT_ID, STATE FROM UNI where CUSTOMER_ID = '$this->customers_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {	
			if ($this->pon_type == "EPON")
				$index_uni = $slot_id * 10000000 + $port_id * 100000 + $this->pon_onu_id * 1000 + $row['UNI_PORT_ID'];						
			if ($this->pon_type == "GPON") {
				if ($this->pon_onu_id < 100) {
					$index_uni = $slot_id * 10000000 + $port_id * 100000 + $this->pon_onu_id * 1000 + $row['UNI_PORT_ID'];		
				}else{
					$index_uni = (3<<28)+(10000000 * $slot_id + 100000 * $port_id + 1000 * ($this->pon_onu_id%100)) + $row['UNI_PORT_ID'];
				}
			}
			$uni_port_admin_set = $snmp_obj->get_pon_oid("uni_port_admin_set_oid", $this->pon_type) . "." . $index_uni;
			snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
			$session = new SNMP(SNMP::VERSION_2C, $this->olt_ip_address, $olt_rw);
			$session->set($uni_port_admin_set, 'i', $row['STATE']);
			if ($session->getError())
				return(var_dump($session->getError()));
		}
		$session->close();
		
	}
	
	function set_name(){
		  try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT CUSTOMERS.ID as C_ID, CUSTOMERS.NAME, CUSTOMERS.SN, CUSTOMERS.STATE, CUSTOMERS.STATE_RF, CUSTOMERS.AUTO, PON_ONU_ID, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, CUSTOMERS.SERVICE, SERVICES.LINE_PROFILE_ID, SERVICES.SERVICE_PROFILE_ID, OLT.ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RW as RW, PON.ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE, LINE_PROFILE.LINE_PROFILE_ID, SERVICE_PROFILE.SERVICE_PROFILE_ID, SERVICE_PROFILE.PORTS as PORTS from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN LINE_PROFILE on SERVICES.LINE_PROFILE_ID=LINE_PROFILE.ID LEFT JOIN SERVICE_PROFILE on SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where CUSTOMERS.SN = '$this->sn'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$olt_ip_address = $row["IP_ADDRESS"];
			$olt_rw = $row["RW"];
			$port_id = $row["PORT_ID"];
			$pon_onu_id = $row["PON_ONU_ID"];
			$slot_id = $row["SLOT_ID"];
			$name = $row["NAME"];
			$pon_type = $row["PON_TYPE"];
		}
		$snmp_obj = new snmp_oid();
		$big_onu_id = $this->type2id($slot_id, $port_id, $pon_onu_id);
		$description_oid = $snmp_obj->get_pon_oid("description_oid", $pon_type) . "." . $big_onu_id;
		$session = new SNMP(SNMP::VERSION_2C, $olt_ip_address, $olt_rw);
		//SET THE NAME
		$session->set($description_oid, 's', $name);
		if ($session->getError()) {
			$error = var_dump($session->getError());
			return $error;
		}
	}
	function set_state(){
		  try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT CUSTOMERS.ID as C_ID, CUSTOMERS.NAME, CUSTOMERS.SN, CUSTOMERS.STATE, CUSTOMERS.STATE_RF, CUSTOMERS.AUTO, PON_ONU_ID, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, CUSTOMERS.SERVICE, SERVICES.LINE_PROFILE_ID, SERVICES.SERVICE_PROFILE_ID, OLT.ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RW as RW, PON.ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE, LINE_PROFILE.LINE_PROFILE_ID, SERVICE_PROFILE.SERVICE_PROFILE_ID, SERVICE_PROFILE.PORTS as PORTS from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN LINE_PROFILE on SERVICES.LINE_PROFILE_ID=LINE_PROFILE.ID LEFT JOIN SERVICE_PROFILE on SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where CUSTOMERS.SN = '$this->sn'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$olt_ip_address = $row["IP_ADDRESS"];
			$olt_rw = $row["RW"];
			$port_id = $row["PORT_ID"];
			$pon_onu_id = $row["PON_ONU_ID"];
			$slot_id = $row["SLOT_ID"];
			$state = $row["STATE"];
			$pon_type = $row["PON_TYPE"];
		}
		$snmp_obj = new snmp_oid();
		$big_onu_id = $this->type2id($slot_id, $port_id, $pon_onu_id);
		$state_oid = $snmp_obj->get_pon_oid("onu_active_state_oid", $pon_type) . "." . $big_onu_id;
		$session = new SNMP(SNMP::VERSION_2C, $olt_ip_address, $olt_rw);
		//SET STATE
		if ($this->state == "NO") {
			$session->set($state_oid, 'i', '2');
			if ($session->getError()) {
			$error = var_dump($session->getError());
			return $error;
			}
		}
		if ($this->state == "YES"){
			$session->set($state_oid, 'i', '1');
			if ($session->getError()) {
				$error = var_dump($session->getError());
				return $error;
			}
		}
	}
	function onu_traffic_rrd() {
		$traffic = array("traffic", "unicast", "broadcast", "multicast");
		foreach ($traffic as $tr) {
			$rrd_name = dirname(dirname(__FILE__)) . "/rrd/" . $this->sn . "_" . $tr . ".rrd";
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
	
	function onu_eth_ports_rrd() {
		//ETHERNET PORTS RRD
		for ($i=1; $i <= $this->ports; $i++) {
			$rrd_name = dirname(dirname(__FILE__)) . "/rrd/" . $this->sn . "_" . $i . ".rrd";
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
	
	function onu_power_rrd() {
		$rrd_name = dirname(dirname(__FILE__)) . "/rrd/" . $this->sn . "_power.rrd";
		$opts = array( "--step", "300", "--start", "0",
		   "DS:input:GAUGE:1800:U:U",
		   "DS:output:GAUGE:1800:U:U",
		   "DS:rxolt:GAUGE:1800:U:U",
           "DS:rfin:GAUGE:1800:U:U",
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

	function type2id($slot, $pon_port, $onu_id) {
        $vif = "0001";
        $slot = str_pad(decbin($slot),5, "0", STR_PAD_LEFT);
        $pon_port = str_pad(decbin($pon_port), 6, "0", STR_PAD_LEFT);
        $onu_id = str_pad(decbin($onu_id), 16, "0", STR_PAD_LEFT);
        $big_onu_id = bindec($vif . $slot . "0" . $pon_port . $onu_id);
        return $big_onu_id;
	}
	function id2type($id) {
		$bin_id = decbin($id);
		$onu_id = bindec(substr($bin_id, -16));
		$pon_port = bindec(substr($bin_id, -22, 6));
		$slot = bindec(substr($bin_id, -28, 5));
		return array($slot, $pon_port, $onu_id);
	}
	function type2ponid ($slot, $pon_port) {
        $slot = decbin($slot);
        $pon_port = str_pad(decbin($pon_port), 6, "0", STR_PAD_LEFT);
        $pon_id = bindec($slot . $pon_port);
        return $pon_id;
	}
	function update_rf_sql() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("UPDATE CUSTOMERS SET STATE_RF = '$this->rf_state' where CUSTOMERS.ID = '$this->customer_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}	
	}
	function update_rf_snmp() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME as NAME, SN, PON_ONU_ID, CUSTOMERS.SERVICE, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, CUSTOMERS.STATE_RF, OLT.ID, INET_NTOA(OLT.IP_ADDRESS)as IP_ADDRESS, OLT.NAME as OLT_NAME, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where CUSTOMERS.ID = '$this->customers_id'");
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
			$rf_val = $row['STATE_RF'];
		}
		
		if ($pon_type == "EPON")
			$index_rf = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + 162;						
		if ($pon_type == "GPON") {
			if ($pon_onu_id < 100) {
				$index_rf = $slot_id * 10000000 + $port_id * 100000 + $pon_onu_id * 1000 + 1;		
			}else{
				$index_rf = (3<<28)+($slot_id * 10000000 + $port_id * 100000 + ($pon_onu_id%100) * 1000 + 1);
			}
		}
		
		$snmp_obj = new snmp_oid();
		$onu_rf_status_oid = $snmp_obj->get_pon_oid("onu_rf_status_oid", $pon_type) . "." . $index_rf;
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$session = new SNMP(SNMP::VERSION_2C, $ip_address, $rw);
		$set_rf = $session->set($onu_rf_status_oid, 'i', $rf_val);
	}
	
	
	function get_changed() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, PON_ONU_ID from CUSTOMERS  where CHANGED='YES'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		$rows = $result->fetchAll();
		return $rows;	
	}
	function get_Service_name(){
		if (!empty($this->service)) {
			try {
				$conn = db_connect::getInstance();
				$result = $conn->db->query("SELECT NAME from SERVICES  where ID=$this->service");
			} catch (PDOException $e) {
				echo "Connection Failed:" . $e->getMessage() . "\n";
				exit;
			}
			$rows = $result->fetchAll();
			foreach ($rows as $row) 
			return $row["NAME"];
		}
	}
	function not_paid() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, SN from NOT_PAID");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		$rows = $result->fetchAll();
		foreach ($rows as $row) { 
			$this->sn = $row["SN"];
			try {
				$conn = db_connect::getInstance();
				$result = $conn->db->query("UPDATE CUSTOMERS SET STATE = 'NO', AUTO = 'YES' where CUSTOMERS.SN = '$this->sn'");
			} catch (PDOException $e) {
				echo "Connection Failed:" . $e->getMessage() . "\n";
				exit;
			}	
			$this->get_data_customer();

			$error = $this->edit_customer();
		
			if($error) {
				echo $error;
				exit;
			}
			try {
				$conn = db_connect::getInstance();
				$result = $conn->db->query("DELETE FROM NOT_PAID where SN='$this->sn'");
			} catch (PDOException $e) {
				echo "Connection Failed:" . $e->getMessage() . "\n";
				exit;
			}	
		}	
	}
	function find_next_onu_id() {
		// FIND FREE ONU_ID
		if (isset($this->olt) && isset($this->pon_port)) {
			try {
				$conn = db_connect::getInstance();
				$result = $conn->db->query("SELECT CARDS_MODEL.PON_TYPE, PON.OLT, PON.SLOT_ID, PON.PORT_ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RW	from CARDS_MODEL LEFT JOIN PON on PON.CARDS_MODEL_ID=CARDS_MODEL.ID LEFT JOIN OLT on OLT.ID=PON.OLT where PON.ID='$this->pon_port'");
			} catch (PDOException $e) {
				$error = "Connection Failed:" . $e->getMessage() . "\n";
				return $error;
			}
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				$this->pon_type = $row["PON_TYPE"];
				$this->olt_ip_address = $row["IP_ADDRESS"];
				$olt_rw = $row["RW"];
				$slot_id = $row["SLOT_ID"];
				$port_id = $row["PORT_ID"];
			}
			
			try {
				$conn = db_connect::getInstance();
				$result = $conn->db->query("SELECT PON_ONU_ID from CUSTOMERS where OLT='$this->olt' and PON_PORT='$this->pon_port'");
			} catch (PDOException $e) {
				$error = "Connection Failed:" . $e->getMessage() . "\n";
				return $error;
			}
			$arr2 = array();
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			array_push($arr2, $row['PON_ONU_ID']);
			}
			
			$pon_index = $this->type2ponid($slot_id, $port_id);
			$snmp_obj = new snmp_oid();
			if ($this->pon_type == "EPON"){
				$arr1 = range(1,64);
				$PONPortMinOnuIndex = $snmp_obj->get_pon_oid("rcEponPONPortMinOnuIndex", $this->pon_type) . "." . $pon_index;
			}
			if ($this->pon_type == "GPON"){
				$arr1 = range(1,128);
				$PONPortMinOnuIndex = $snmp_obj->get_pon_oid("rcGponPONPortMinOnuIndex", $this->pon_type) . "." . $pon_index;
			}
			$session = new SNMP(SNMP::VERSION_2C, $this->olt_ip_address, $olt_rw);
			$snmp_port_min_onu_index = $session->get($PONPortMinOnuIndex); 
			if(!empty($snmp_port_min_onu_index)){
				$pon_onu_id = str_replace("INTEGER: ", "", $snmp_port_min_onu_index);
			}else{
				$arr3 = array_diff($arr1,$arr2);
				$arr3 = array_filter($arr3);
				if (empty($arr3)) {
					$error = "Not Free ONU IDs";
					return $error;
				}
				$pon_onu_id = array_values($arr3)[0];
			}
		}else {
			$pon_onu_id = "NULL";
		}
		return $pon_onu_id;
	}
	function find_free_ip($ip_pool_id){
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT OLT_ID, SERVICE_ID from OLT_IP_POOLS where IP_POOL_ID='$ip_pool_id'");
		} catch (PDOException $e) {
			$error = "Connection 1 Failed:" . $e->getMessage() . "\n";
			exit($error);
		}
		$ip_address_array = array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {		
			if (!empty($row["SERVICE_ID"])) { 
				try {
					$conn = db_connect::getInstance();
					$result2 = $conn->db->query("SELECT IP_ADDRESS from CUSTOMERS where OLT='$row[OLT_ID]' and SERVICE='$row[SERVICE_ID]'");
				} catch (PDOException $e) {
					$error2 = "Connection 1 Failed:" . $e->getMessage() . "\n";
					exit($error2);
				}		
			}else{
				try {
					$conn = db_connect::getInstance();
					$result2 = $conn->db->query("SELECT IP_ADDRESS from CUSTOMERS where OLT='$row[OLT_ID]'");
				} catch (PDOException $e) {
					$error2 = "Connection 1 Failed:" . $e->getMessage() . "\n";
					exit($error2);
				}		
			}
			while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
				array_push($ip_address_array, $row2['IP_ADDRESS']);
			}

			try {
				$conn = db_connect::getInstance();
				$result3 = $conn->db->query("SELECT ID, INET_NTOA(SUBNET) as SUBNET, INET_NTOA(NETMASK) as NETMASK, START_IP, END_IP, INET_NTOA(GATEWAY) as GATEWAY, VLAN from IP_POOL where ID='$ip_pool_id'");
			} catch (PDOException $e) {
				$error3 = "Connection 1 Failed:" . $e->getMessage() . "\n";
				exit($error3);
			}		
			while ($row3 = $result3->fetch(PDO::FETCH_ASSOC)) {
				$subnet = $row3["SUBNET"];
				$this->netmask = $row3["NETMASK"];
				$start_ip = $row3["START_IP"];
				$end_ip = $row3["END_IP"];
				$this->gateway = $row3["GATEWAY"];
				$this->vlan = $row3["VLAN"];					
			}
			if (!empty($this->old_onu_ip_address)) {
				$this->onu_ip_address = $this->old_onu_ip_address;
			}else{
				$ip_pool_range = range($start_ip, $end_ip);
				$free_ips = array_diff($ip_pool_range,$ip_address_array);
				$free_ips = array_filter($free_ips);
				if (empty($free_ips)) {
					$error = "No Free IP ADDRESS";
				//	exit($error);
				}else{
					$this->onu_ip_address = array_values($free_ips)[0];
				}
			}
		}
	}
}






?>
