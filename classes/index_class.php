<?php
include ("db_connect_class.php");

class index {
	private $onu_id;
	public $name;
	private $olt_id;
	private $pon_id;
	public $address;
	public $pon_port;
	public $egn;
	public $sn;
	public $rf_state;
	private $submit;
	
	function __construct() {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$this->onu_id = isset($_POST['onu_id'])	? $this->test_input($_POST['onu_id']) : null;
			$this->name = isset($_POST['name'])	? $this->test_input($_POST['name']) : null;
			$this->address = isset($_POST['address'])	? $this->test_input($_POST['address']) : null;
			$this->olt_id = isset($_POST['olt_id'])	? $this->test_input($_POST['olt_id']) : null;
			$this->pon_id = isset($_POST['pon_id'])	? $this->test_input($_POST['pon_id']) : null;
			$this->pon_port = isset($_POST['pon_port'])	? $this->test_input($_POST['pon_port']) : null;
			$this->egn = isset($_POST['egn'])	? $this->test_input($_POST['egn']) : null;
			$this->sn = isset($_POST['sn']) ? $this->test_input($_POST['sn']) : null;
			$this->submit = isset($_POST['SUBMIT'])	? $this->test_input($_POST['SUBMIT']) : null;
		
		
		}
		if ($_SERVER["REQUEST_METHOD"] == "GET") {
			$this->onu_id = isset($_GET['id'])	? $this->test_input($_GET['id']) : null;
		}
	}	
	function getSubmit() {
		return $this->submit;
	}
	
	function getName() {
		return $this->name;
	}
	function getAddress() {
		return $this->address;
	}
	function getEgn() {
		return $this->egn;
	}
	function getSn() {
		return $this->sn;
	}
	function getOnu_id() {
		return $this->onu_id;
	}
		
	function getOlt_id() {
		return $this->olt_id;
	}
	
	function getPon_id() {
		return $this->pon_id;
	}

	function getOlt_name() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT NAME from OLT WHERE ID=" . $this->olt_id);
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}

		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			return $row{'NAME'};
		}
	}
	
	function getPon_data() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT PON.ID, PON.NAME, PON.OLT, PON.SLOT_ID, PON.PORT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.ID, CARDS_MODEL.PON_TYPE from PON LEFT JOIN CARDS_MODEL ON PON.CARDS_MODEL_ID=CARDS_MODEL.ID WHERE PON.ID=" . $this->pon_id);
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}	
		$row = $result->fetch(PDO::FETCH_ASSOC);
		return $row ;	
	}
	
	
	
	function get_from_olt() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT * from OLT");
		} catch (PDOException $e) {
			$error =  "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;		
	}
	function build_table() {
		if ($this->submit == "LOAD")
			$where = "PON.ID='" . $this->pon_id ."' and OLT.ID='" . $this->olt_id . "'";
		if ($this->submit == "SEARCH") {
			if(!empty($this->name))
				$where = "CUSTOMERS.NAME LIKE '%$this->name%'";
			if(!empty($this->egn))
				$where = "CUSTOMERS.EGN = '$this->egn'";
			if(!empty($this->sn)) 
				$where = "CUSTOMERS.SN = '$this->sn'";
			if(!empty($this->address)) 
				$where = "CUSTOMERS.ADDRESS LIKE '%$this->address%'";
		}
		if ($this->submit == "UNASSIGNED")
			$where = "CUSTOMERS.OLT is NULL or CUSTOMERS.PON_PORT is NULL";

		
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME, CUSTOMERS.ADDRESS, SN, SERVICES.NAME as SERVICE_NAME, OLT.NAME as OLT_NAME, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO as RO, OLT_MODEL.TYPE as TYPE, PON.NAME as PON_NAME, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON.CARDS_MODEL_ID, CARDS_MODEL.PON_TYPE, PON_ONU_ID from CUSTOMERS LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID WHERE " . $where ." order by PON_ONU_ID");
		} catch (PDOException $e) {
			$error =  "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
			
	} 
	
	function calc_last_online($last_online){
		$last_online = str_replace('Hex-STRING: ', '', $last_online);
		$loa = explode(' ', $last_online);
		$year = $loa[0] . $loa[1];
		$year = hexdec($year);
		$month = hexdec($loa[2]);
		$day = hexdec($loa[3]);
		$hour = hexdec($loa[4]);
		$hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
		$minute = hexdec($loa[5]);
		$minute = str_pad($minute, 2, '0', STR_PAD_LEFT);	
		$last_online = $year . "-". $month . "-". $day . "  " . $hour . ":" . $minute ;
		return $last_online;
	}
	
	function get_rx_power($id) {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT RX_POWER from ONU_RX_POWER where CUSTOMERS_ID = $id");
		} catch (PDOException $e) {
			$error =  "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			return $row{'RX_POWER'};
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