<?php
include ("db_connect_class.php");

class services {
	private $service_id;
	public $name;
	public $line_profile_id;
	public $line_profile_name;
	public $service_profile_id;
	public $service_profile_name;
	private $submit;
	
	function __construct() {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$this->service_id = isset($_POST['service_id'])	? $this->test_input($_POST['service_id']) : null;
			$this->name = isset($_POST['name'])	? $this->test_input($_POST['name']) : null;
			$this->line_profile_id = isset($_POST['line_profile_id'])	? $this->test_input($_POST['line_profile_id']) : null;
			$this->service_profile_id = isset($_POST['service_profile_id'])	? $this->test_input($_POST['service_profile_id']) : null;
			$this->submit = isset($_POST['SUBMIT'])	? $this->test_input($_POST['SUBMIT']) : null;
		}
		
		if ($_SERVER["REQUEST_METHOD"] == "GET") {
			$this->service_id = isset($_GET['id'])	? $this->test_input($_GET['id']) : null;
		}
	}
	
	function getLine_profile_name() {
		return $this->line_profile_name;
	}
	
	function getService_profile_name() {
		return $this->service_profile_name;
	}
	
	function getSubmit() {
		return $this->submit;
	}
	function getService_id() {
		return $this->service_id;
	}
	
	function getName() {
		return $this->name;
	}
	
	function getLine_profile_id() {
		return $this->line_profile_id;
	}
	
	function getService_Profile_id() {
		return $this->service_profile_id;
	}

	function create_service() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("INSERT INTO SERVICES (NAME, LINE_PROFILE_ID, SERVICE_PROFILE_ID) VALUES ('$this->name', '$this->line_profile_id', '$this->service_profile_id')");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	 
	
	function edit_service() {
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("UPDATE SERVICES SET NAME = '$this->name', LINE_PROFILE_ID = '$this->line_profile_id', SERVICE_PROFILE_ID = '$this->service_profile_id' where ID = '$this->service_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function delete_service() {
		
		// CHECK IF SERVICE IS ASSIGNED TO ANY CUSTOMER
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT SERVICE from CUSTOMERS where SERVICE =  '$this->service_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["SERVICE"])
				$error = ("ERROR: THIS SERVICE IS ASSIGNED TO CUSTOMERS, Please remove SERVICE from customers and then try to Delete it!");
				return $error;
		}
		
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("DELETE FROM SERVICES where ID='$this->service_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function build_table_services() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT SERVICES.ID, SERVICES.NAME, SERVICES.LINE_PROFILE_ID, SERVICES.SERVICE_PROFILE_ID, LINE_PROFILE.NAME as LINE_PROFILE_NAME, SERVICE_PROFILE.NAME as SERVICE_PROFILE_NAME from SERVICES LEFT JOIN LINE_PROFILE ON SERVICES.LINE_PROFILE_ID=LINE_PROFILE.ID LEFT JOIN SERVICE_PROFILE ON SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID");
		} catch (PDOException $e) {
			$error =  "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
			
	} 
	
	
	function get_data_services() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT SERVICES.ID, SERVICES.NAME, SERVICES.LINE_PROFILE_ID, SERVICES.SERVICE_PROFILE_ID, LINE_PROFILE.NAME as LINE_PROFILE_NAME, SERVICE_PROFILE.NAME as SERVICE_PROFILE_NAME from SERVICES LEFT JOIN LINE_PROFILE ON SERVICES.LINE_PROFILE_ID=LINE_PROFILE.ID LEFT JOIN SERVICE_PROFILE ON SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID where SERVICES.ID='$this->service_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->service_id =  $row["ID"];
			$this->name = $row["NAME"];
			$this->line_profile_id = $row["LINE_PROFILE_ID"];
			$this->service_profile_id = $row["SERVICE_PROFILE_ID"];
			$this->line_profile_name = $row["LINE_PROFILE_NAME"];
			$this->service_profile_name = $row["SERVICE_PROFILE_NAME"];

		}	
		
		
	}
	
	function get_Line_profile_info() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME from LINE_PROFILE");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
	}
	
	function get_Service_profile_info() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME from SERVICE_PROFILE");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
	}
	
	function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
	}


	
}




?>
