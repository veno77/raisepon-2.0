<?php
include_once("db_connect_class.php");
class olt {
	private $olt_id;
	private $name;
	public $olt_model;
	public $snmp_community_ro;
	public $olt_ip_address;
	public $olt_old_ip;
	public $snmp_community_rw;
	public $backup_id;
	private $submit;
	
	function __construct() {
		if (!empty($_SERVER["REQUEST_METHOD"])) {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$this->olt_id = isset($_POST['olt_id'])	? $this->test_input($_POST['olt_id']) : null;
				$this->name = isset($_POST['name'])	? $this->test_input($_POST['name']) : null;
				$this->olt_model = isset($_POST['olt_model'])	? $this->test_input($_POST['olt_model']) : null;
				$this->snmp_community_ro = isset($_POST['ro'])	? $this->test_input($_POST['ro']) : null;
				$this->olt_ip_address = isset($_POST['ip_address'])	? $this->test_input($_POST['ip_address']) : null;
				$this->olt_old_ip = isset($_POST['old_ip'])	? $this->test_input($_POST['old_ip']) : null;
				$this->snmp_community_rw = isset($_POST['rw']) ? $this->test_input($_POST['rw']) : null;
				$this->backup_id = isset($_POST['backup_id']) ? $this->test_input($_POST['backup_id']) : null;
				$this->submit = isset($_POST['SUBMIT'])	? $this->test_input($_POST['SUBMIT']) : null;
			}
			
			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				$this->olt_id = isset($_GET['id'])	? $this->test_input($_GET['id']) : null;
			}
		}
	}
	
	function getSubmit() {
		return $this->submit;
	}
	function getOlt_id() {
		return $this->olt_id;
	}
	
	function getName() {
		return $this->name;
	}
	
	function getOlt_model() {
		return $this->olt_model;
	}
	
	function getSnmp_community_ro() {
		return $this->snmp_community_ro;
	}
	function getOlt_ip_address() {
		return $this->olt_ip_address;
	}
	function getOlt_old_ip() {
		return $this->olt_old_ip;
	}
	function getSnmp_community_rw() {
		return $this->snmp_community_rw;
	}
	function getBackup_id() {
		return $this->backup_id;
	}
	function setBackup_id($backup_id) {
		$this->backup_id = $backup_id;
	}
	function create_olt() {
		// CHECK IP ADDRESS for DUPLICATES
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT IP_ADDRESS from OLT where IP_ADDRESS = INET_ATON('$this->olt_ip_address')");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
	        exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["IP_ADDRESS"])
				$error = "ERROR!  DUPLICATE IP ADDRESS!";
			return $error;
		}
		if (!empty($this->backup_id)) {
			$backup_id = "'" . $this->backup_id . "'";
		}else{
			$backup_id = "NULL";
		}
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("INSERT INTO OLT (NAME, MODEL, IP_ADDRESS, RO, RW, BACKUP_ID) VALUES ('$this->name', '$this->olt_model', INET_ATON('$this->olt_ip_address'), '$this->snmp_community_ro', '$this->snmp_community_rw', $backup_id)");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		//CREATE RRD
		foreach (range(1, 18) as $port_number) {
	        $rrd_name = dirname(dirname(__FILE__)) . "/rrd/" . $this->olt_ip_address . "_" . $port_number . "_traffic.rrd";
	        $opts = array( "--step", "300", "--start", 0,
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
			if( $ret == 0 ){
				$err = rrd_error();
				return $err;
			}
			$rrd_name = dirname(dirname(__FILE__)) . "/rrd/" . $this->olt_ip_address . "_" . $port_number . "_broadcast.rrd";
	        $opts = array( "--step", "300", "--start", 0,
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
		
			if( $ret == 0 ){
				$err = rrd_error();
				return $err;
			}	
		}
	}
	 
	
	function edit_olt() {
		
			// CHECK IP ADDRESS for DUPLICATES
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT IP_ADDRESS from OLT where IP_ADDRESS = INET_ATON('$this->olt_ip_address') AND ID <> $this->olt_id");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["IP_ADDRESS"])
				$error = "ERROR!  DUPLICATE IP ADDRESS!";
				return $error;
		}
		
		if (!empty($this->backup_id)) {
			$backup_id = "'" . $this->backup_id . "'";
		}else{
			$backup_id = "NULL";
		}
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("UPDATE OLT SET NAME = '$this->name', MODEL = '$this->olt_model', IP_ADDRESS = INET_ATON('$this->olt_ip_address'), RO = '$this->snmp_community_ro', RW = '$this->snmp_community_rw', BACKUP_ID = $backup_id where ID = '$this->olt_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		
		//CREATE RRD
		if ($this->olt_old_ip != $this->olt_ip_address) {
			foreach (range(1, 18) as $port_number) {
				$old_rrd_file = dirname(dirname(__FILE__)) . "/rrd/" . $this->olt_old_ip . "_" . $port_number . "_traffic.rrd";
				unlink($old_rrd_file);
				$rrd_name = dirname(dirname(__FILE__)) . "/rrd/" . $this->olt_ip_address . "_" . $port_number . "_traffic.rrd";
				$opts = array( "--step", "300", "--start", 0,
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
				if( $ret == 0 ) {
					$err = rrd_error();
					return $err;
				}    
				$old_rrd_file = dirname(dirname(__FILE__)) . "/rrd/" . $this->olt_old_ip . "_" . $port_number . "_broadcast.rrd";
				unlink($old_rrd_file);
				$rrd_name = dirname(dirname(__FILE__)) . "/rrd/" . $this->olt_ip_address . "_" . $port_number . "_broadcast.rrd";
				$opts = array( "--step", "300", "--start", 0,
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

				if( $ret == 0 ) {
					$err = rrd_error();
					return $err;
				}    
			}
		}
	}
	
	function delete_olt() {
		
		// CHECK IF ONU IS ASSIGNED TO ANY CUSTOMER
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT OLT from CUSTOMERS where OLT =  '$this->olt_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["OLT"])
				$error = ("ERROR: OLT IS ASSIGNED TO CUSTOMERS, Please remove OLT from customers to Delete it!!");
				return $error;
		}
		//DELETE OLT from Database
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("DELETE FROM OLT where ID='$this->olt_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		
		//DELETE PON Ports associated with this OLT
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("DELETE FROM PON where OLT='$this->olt_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		
		//DELETE RRD files
		foreach (range(1, 18) as $port_number) {
			$old_rrd_file = dirname(dirname(__FILE__)) . "/rrd/" . $this->olt_old_ip . "_" . $port_number . "_traffic.rrd";
			unlink($old_rrd_file);
			$old_rrd_file = dirname(dirname(__FILE__)) . "/rrd/" . $this->olt_old_ip . "_" . $port_number . "_broadcast.rrd";
			unlink($old_rrd_file);
		}
	}
	
	function build_table_olt() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT OLT.ID, OLT.NAME, OLT.MODEL, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, RO, RW, OLT_MODEL.NAME as OLT_NAME,OLT_MODEL.TYPE as TYPE, OLT.BACKUP_ID, BACKUP.NAME as BACKUP_NAME from OLT LEFT JOIN OLT_MODEL on OLT.MODEL = OLT_MODEL.ID LEFT JOIN BACKUP on OLT.BACKUP_ID = BACKUP.ID");
		} catch (PDOException $e) {
			$error =  "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
			
	} 
	
	
	function get_data_olt() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME, MODEL, INET_NTOA(IP_ADDRESS) as IP_ADDRESS, RO, RW, BACKUP_ID from OLT where ID='$this->olt_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->olt_id =  $row["ID"];
			$this->name = $row["NAME"];
			$this->olt_ip_address = $row["IP_ADDRESS"];
			$this->snmp_community_ro = $row["RO"];
			$this->snmp_community_rw = $row["RW"];
			$this->olt_model = $row["MODEL"];
			$this->backup_id = $row["BACKUP_ID"];
		}	
		
		
	}
	function get_data_backup() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME, INET_NTOA(IP_ADDRESS) as IP_ADDRESS, USERNAME, PASSWORD, DIRECTORY from BACKUP where ID='$this->backup_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;	
	}
	function get_Olt_model() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT * from OLT_MODEL");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
	}
	function get_Backup() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT * from BACKUP");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
	}
	function save_olt() {
		$this->get_data_olt();
		$save_oid = '1.3.6.1.4.1.8886.1.2.1.1.0';
		$session = new SNMP(SNMP::VERSION_2C, $this->olt_ip_address, $this->snmp_community_rw, 2000000, 3);
        $session->set($save_oid, 'i', '2');
       	if ($session->getError()) {
       		exit(var_dump($session->getError()));
		}
	}
	function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
	}
	function backup_status($olt_id, $reason) {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT OLT from BACKUP_STATUS where OLT = $olt_id");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row{'OLT'}) {
				try {
					$conn = db_connect::getInstance();
					$conn->db->query("UPDATE BACKUP_STATUS SET DATE = NOW(), REASON = $reason where OLT = $olt_id");
				} catch (PDOException $e) {
					$error = "Connection Failed:" . $e->getMessage() . "\n";
					return $error;
				}
				return;
			}
		}
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("INSERT INTO BACKUP_STATUS (OLT, DATE, REASON) VALUES ($olt_id, NOW(), $reason)");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	

}




?>
