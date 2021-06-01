<?php
include ("db_connect_class.php");

class ip_pool {
	private $id;
	private $binding_id;
	private $olt_id;
	private $service_id;
	public $subnet;
	public $netmask;
	public $start_ip;
	private $end_ip;
	private $gateway;
	private $vlan;
	private $submit;
	
	function __construct() {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$this->id = isset($_POST['id'])	? $this->test_input($_POST['id']) : null;
			$this->binding_id = isset($_POST['binding_id'])	? $this->test_input($_POST['binding_id']) : null;
			$this->olt_id = isset($_POST['olt_id'])	? $this->test_input($_POST['olt_id']) : null;
			$this->service_id = isset($_POST['service_id'])	? $this->test_input($_POST['service_id']) : null;
			$this->subnet = isset($_POST['subnet'])	? $this->test_input($_POST['subnet']) : null;
			$this->netmask = isset($_POST['netmask'])	? $this->test_input($_POST['netmask']) : null;
			$this->start_ip = isset($_POST['start_ip'])	? $this->test_input($_POST['start_ip']) : null;
			$this->end_ip = isset($_POST['end_ip'])	? $this->test_input($_POST['end_ip']) : null;
			$this->gateway = isset($_POST['gateway'])	? $this->test_input($_POST['gateway']) : null;
			$this->vlan = isset($_POST['vlan'])	? $this->test_input($_POST['vlan']) : null;
			$this->submit = isset($_POST['SUBMIT'])	? $this->test_input($_POST['SUBMIT']) : null;
		}	
	}
	
	function getBinding_id() {
		return $this->binding_id;
	}
	function getOlt_id() {
		return $this->olt_id;
	}
	function getService_id() {
		return $this->service_id;
	}
	function getId() {
		return $this->id;
	}
	function getSubnet() {
		return $this->subnet;
	}
	
	function getNetmask() {
		return $this->netmask;
	}
	
	function getStart_ip() {
		return $this->start_ip;
	}
	function getEnd_ip() {
		return $this->end_ip;
	}
	function getGateway() {
		return $this->gateway;
	}
	function getVlan() {
		return $this->vlan;
	}
	function getSubmit() {
		return $this->submit;
	}
	
	

	function create_ip_pool() {
		
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("INSERT INTO IP_POOL (SUBNET, NETMASK, START_IP, END_IP, GATEWAY, VLAN) VALUES (INET_ATON('$this->subnet'), INET_ATON('$this->netmask'), INET_ATON('$this->start_ip'), INET_ATON('$this->end_ip'), INET_ATON('$this->gateway'), '$this->vlan')");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	 
	
	function edit_ip_pool() {
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("UPDATE IP_POOL SET SUBNET = INET_ATON('$this->subnet'), NETMASK = INET_ATON('$this->netmask'), START_IP = INET_ATON('$this->start_ip'), END_IP = INET_ATON('$this->end_ip'), GATEWAY = INET_ATON('$this->gateway'), VLAN = '$this->vlan' where ID = '$this->id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function delete_ip_pool() {
		
		// CHECK IF ip_pool IS ASSIGNED TO ANY OLT
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT IP_POOL_ID from OLT_IP_POOLS where IP_POOL_ID =  '$this->id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["IP_POOL_ID"])
				$error = ("ERROR: THIS ip_pool IS ASSIGNED TO OLT, Please remove ip_pool from OLT and then try to Delete it again!");
				return $error;
				
		}
		
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("DELETE FROM IP_POOL where ID='$this->id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function create_binding() {
		//Check if Binding to this OLT and SERVICE already exists
		if ($this->service_id == "") {
			$where = "OLT_ID =  '$this->olt_id' AND SERVICE_ID IS NULL";
		}else{
			$where = "OLT_ID = '$this->olt_id' AND SERVICE_ID = '$this->service_id'";
		}
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT OLT_ID, SERVICE_ID from OLT_IP_POOLS where $where");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["OLT_ID"])
				$error = ("ERROR: THIS OLT and this SERVICE have been ASSIGNED pool ALREADY, Please remove any existing bindings and try to create again!");
				return $error;	
		}
		if ($this->service_id == "") {
			$service_id = "NULL";
		}else{
			$service_id = "'$this->service_id'";
		}
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("INSERT INTO OLT_IP_POOLS (OLT_ID, IP_POOL_ID, SERVICE_ID) VALUES ('$this->olt_id', '$this->id', $service_id)");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	 
	
	function edit_binding() {
		//Check if Binding to this OLT and SERVICE already exists
		if ($this->service_id == "") {
			$where = "OLT_ID =  '$this->olt_id' AND SERVICE_ID IS NULL";
		}else{
			$where = "OLT_ID = '$this->olt_id' AND SERVICE_ID = '$this->service_id'";
		}
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT OLT_ID, SERVICE_ID from OLT_IP_POOLS where $where");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["OLT_ID"])
				$error = ("ERROR: THIS OLT and this SERVICE have been ASSIGNED pool ALREADY, Please remove any existing bindings and try to create again!");
				return $error;	
		}
		if ($this->service_id == "") {
			$service_id = "NULL";
		}else{
			$service_id = "'$this->service_id'";
		}
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("UPDATE OLT_IP_POOLS SET OLT_ID = '$this->olt_id', IP_POOL_ID = '$this->id', SERVICE_ID = $service_id where ID = '$this->binding_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function delete_binding() {
	/*	
		// CHECK IF IP_ADDRESS from this POOL is assigned to the OLT.
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT IP_POOL_ID from OLT_IP_POOLS where IP_POOL_ID =  '$this->id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["IP_POOL_ID"])
				$error = ("ERROR: THIS ip_pool IS ASSIGNED TO OLT, Please remove ip_pool from OLT and then try to Delete it again!");
				return $error;
		}
	*/	
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("DELETE FROM OLT_IP_POOLS where ID='$this->binding_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function build_table_ip_pool() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, INET_NTOA(SUBNET) as SUBNET, INET_NTOA(NETMASK) as NETMASK, INET_NTOA(START_IP) as START_IP, INET_NTOA(END_IP) as END_IP, INET_NTOA(GATEWAY) as GATEWAY, VLAN from IP_POOL");
		} catch (PDOException $e) {
			$error =  "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
			
	} 
	
	function build_table_olt_ip_pool() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT OLT_IP_POOLS.ID as BINDING_ID, OLT_IP_POOLS.OLT_ID, OLT_IP_POOLS.IP_POOL_ID, OLT_IP_POOLS.SERVICE_ID, OLT.ID, OLT.NAME as OLT_NAME, IP_POOL.ID, INET_NTOA(IP_POOL.SUBNET) as SUBNET, INET_NTOA(IP_POOL.NETMASK) as NETMASK, SERVICES.ID, SERVICES.NAME as SERVICES_NAME from OLT_IP_POOLS LEFT JOIN OLT on OLT_IP_POOLS.OLT_ID = OLT.ID LEFT JOIN IP_POOL on OLT_IP_POOLS.IP_POOL_ID=IP_POOL.ID LEFT JOIN SERVICES on OLT_IP_POOLS.SERVICE_ID=SERVICES.ID");
		} catch (PDOException $e) {
			$error =  "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
			
	} 
	function get_data_ip_pool() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, INET_NTOA(SUBNET) as SUBNET, INET_NTOA(NETMASK) as NETMASK, INET_NTOA(START_IP) as START_IP, INET_NTOA(END_IP) as END_IP, INET_NTOA(GATEWAY) as GATEWAY, VLAN from IP_POOL where ID='$this->id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->subnet = $row["SUBNET"];
			$this->netmask = $row["NETMASK"];
			$this->start_ip = $row["START_IP"];
			$this->end_ip = $row["END_IP"];
			$this->gateway = $row["GATEWAY"];
			$this->vlan = $row["VLAN"];
		}		
	}
	function get_IP_pools() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, INET_NTOA(SUBNET) as SUBNET, INET_NTOA(NETMASK) as NETMASK from IP_POOL ");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
	}
	function get_data_olt_ip_pool() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT OLT_IP_POOLS.ID as BINDING_ID, OLT_IP_POOLS.OLT_ID as OLT_ID, OLT_IP_POOLS.IP_POOL_ID as IP_POOL_ID, OLT_IP_POOLS.SERVICE_ID as SERVICE_ID from OLT_IP_POOLS where ID='$this->binding_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->olt_id = $row["OLT_ID"];
			$this->id = $row["IP_POOL_ID"];
			$this->service_id = $row["SERVICE_ID"];
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
