<?php
include ("db_connect_class.php");

class service_profile {
	private $id;
	public $name;
	public $ports;
	public $service_profile_id;
	private $hgu;
	private $rf;
	private $submit;
	
	function __construct() {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$this->id = isset($_POST['id'])	? $this->test_input($_POST['id']) : null;
			$this->name = isset($_POST['name'])	? $this->test_input($_POST['name']) : null;
			$this->ports = isset($_POST['ports'])	? $this->test_input($_POST['ports']) : null;
			$this->service_profile_id = isset($_POST['service_profile_id'])	? $this->test_input($_POST['service_profile_id']) : null;
			$this->hgu = isset($_POST['hgu'])	? $this->test_input($_POST['hgu']) : "No";
			$this->rf = isset($_POST['rf'])	? $this->test_input($_POST['rf']) : "No";
			$this->submit = isset($_POST['SUBMIT'])	? $this->test_input($_POST['SUBMIT']) : null;
		}
		
		if ($_SERVER["REQUEST_METHOD"] == "GET") {
			$this->service_profile_id = isset($_GET['id'])	? $this->test_input($_GET['id']) : null;
		}
	}
	
	function getId() {
		return $this->id;
	}
	
	function getName() {
		return $this->name;
	}
	
	function getPorts() {
		return $this->ports;
	}
	
	function getService_profile_id() {
		return $this->service_profile_id;
	}
	function getHgu() {
		return $this->hgu;
	}
	function getRf() {
		return $this->rf;
	}
	function getSubmit() {
		return $this->submit;
	}
	
	

	function create_service_profile() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("INSERT INTO SERVICE_PROFILE (NAME, PORTS, SERVICE_PROFILE_ID, HGU, RF) VALUES ('$this->name', '$this->ports', '$this->service_profile_id', '$this->hgu', '$this->rf')");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	 
	
	function edit_service_profile() {
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("UPDATE SERVICE_PROFILE SET NAME = '$this->name', PORTS = '$this->ports', SERVICE_PROFILE_ID = '$this->service_profile_id', HGU = '$this->hgu', RF = '$this->rf' where ID = '$this->id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function delete_service_profile() {
		
		// CHECK IF SERVICE_PROFILE IS ASSIGNED TO ANY SERVICE
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT SERVICE_PROFILE_ID from SERVICES where SERVICE_PROFILE_ID =  '$this->id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["SERVICE_PROFILE_ID"])
				$error = ("ERROR: THIS SERVICE_PROFILE IS ASSIGNED TO SERVICES, Please remove SERVICE_PROFILE from SERVICES and then try to Delete it again!");
				return $error;
		}
		
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("DELETE FROM SERVICE_PROFILE where ID='$this->id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function build_table_service_profile() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT SERVICE_PROFILE.ID, SERVICE_PROFILE.NAME, SERVICE_PROFILE.PORTS, SERVICE_PROFILE.SERVICE_PROFILE_ID, SERVICE_PROFILE.HGU, SERVICE_PROFILE.RF from SERVICE_PROFILE");
		} catch (PDOException $e) {
			$error =  "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
			
	} 
	
	
	function get_data_service_profile() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT NAME, PORTS, SERVICE_PROFILE_ID, HGU, RF from SERVICE_PROFILE where ID='$this->id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->name = $row["NAME"];
			$this->ports = $row["PORTS"];
			$this->service_profile_id = $row["SERVICE_PROFILE_ID"];
			$this->hgu = $row["HGU"];
			$this->rf = $row["RF"];
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
