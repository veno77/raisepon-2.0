<?php
include ("db_connect_class.php");

class onu {
	private $onu_id;
	public $name;
	public $ports;
	public $rf;
	public $pse;
	public $hgu;
	public $pon_type;
	private $submit;
	
	function __construct() {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$this->onu_id = isset($_POST['onu_id'])	? $this->test_input($_POST['onu_id']) : null;
			$this->name = isset($_POST['name'])	? $this->test_input($_POST['name']) : null;
			$this->ports = isset($_POST['ports'])	? $this->test_input($_POST['ports']) : null;
			$this->rf = isset($_POST['rf'])	? $this->test_input($_POST['rf']) : "0";
			$this->pse = isset($_POST['pse'])	? $this->test_input($_POST['pse']) : "0";
			$this->hgu = isset($_POST['hgu'])	? $this->test_input($_POST['hgu']) : "0";
			$this->pon_type = isset($_POST['pon_type']) ? $this->test_input($_POST['pon_type']) : null;
			$this->submit = isset($_POST['SUBMIT'])	? $this->test_input($_POST['SUBMIT']) : null;
		}
		
		if ($_SERVER["REQUEST_METHOD"] == "GET") {
			$this->onu_id = isset($_GET['id'])	? $this->test_input($_GET['id']) : null;
		}
	}
	
	function getSubmit() {
		return $this->submit;
	}
	function getOnu_id() {
		return $this->onu_id;
	}
	
	function getName() {
		return $this->name;
	}
	
	function getPorts() {
		return $this->ports;
	}
	
	function getRf() {
		return $this->rf;
	}
	function getPse() {
		return $this->pse;
	}
	function getHgu() {
		return $this->hgu;
	}
	function getPon_type() {
		return $this->pon_type;
	}
	function create_onu() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("INSERT INTO ONU (NAME, PORTS, RF, PSE, HGU, pon_type) VALUES ('$this->name', '$this->ports', '$this->rf', '$this->pse', '$this->hgu', '$this->pon_type')");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	 
	
	function edit_onu() {
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("UPDATE ONU SET NAME = '$this->name', PORTS = '$this->ports', RF = '$this->rf', PSE = '$this->pse', HGU = '$this->hgu', PON_TYPE = '$this->pon_type' where ID = '$this->onu_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function delete_onu() {
		
		// CHECK IF ONU IS ASSIGNED TO ANY CUSTOMER
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ONU_MODEL from CUSTOMERS where ONU_MODEL =  '$this->onu_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["ONU_MODEL"])
				$error = ("ERROR: THIS ONU TYPE IS ASSIGNED TO CUSTOMERS, Please remove ONU from customers and then try to Delete it!");
				return $error;
		}
		
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("DELETE FROM ONU where ID='$this->onu_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function build_table_onu() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME, PORTS, RF, PSE, HGU, PON_TYPE from ONU");
		} catch (PDOException $e) {
			$error =  "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
			
	} 
	
	
	function get_data_onu() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME, PORTS, RF, PSE, HGU, PON_TYPE from ONU where ID='$this->onu_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->onu_id =  $row["ID"];
			$this->name = $row["NAME"];
			$this->ports = $row["PORTS"];
			$this->rf = $row["RF"];
			$this->pse = $row["PSE"];
			$this->hgu = $row["HGU"];
			$this->pon_type = $row["PON_TYPE"];
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
