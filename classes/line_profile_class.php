<?php
include ("db_connect_class.php");

class line_profile {
	private $id;
	public $name;
	public $line_profile_id;
	private $submit;
	
	function __construct() {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$this->id = isset($_POST['id'])	? $this->test_input($_POST['id']) : null;
			$this->name = isset($_POST['name'])	? $this->test_input($_POST['name']) : null;
			$this->line_profile_id = isset($_POST['line_profile_id'])	? $this->test_input($_POST['line_profile_id']) : null;
			$this->submit = isset($_POST['SUBMIT'])	? $this->test_input($_POST['SUBMIT']) : null;
		}
		
		if ($_SERVER["REQUEST_METHOD"] == "GET") {
			$this->line_profile_id = isset($_GET['id'])	? $this->test_input($_GET['id']) : null;
		}
	}
	
	function getId() {
		return $this->id;
	}
	
	function getName() {
		return $this->name;
	}
	
	
	function getLine_profile_id() {
		return $this->line_profile_id;
	}
	
	function getSubmit() {
		return $this->submit;
	}
	
	

	function create_line_profile() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("INSERT INTO LINE_PROFILE (NAME, LINE_PROFILE_ID) VALUES ('$this->name', '$this->line_profile_id')");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	 
	
	function edit_line_profile() {
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("UPDATE LINE_PROFILE SET NAME = '$this->name', LINE_PROFILE_ID = '$this->line_profile_id' where ID = '$this->id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function delete_line_profile() {
		
		// CHECK IF line_profile IS ASSIGNED TO ANY SERVICE
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT LINE_PROFILE_ID from SERVICES where LINE_PROFILE_ID =  '$this->id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["LINE_PROFILE_ID"])
				$error = ("ERROR: THIS line_profile IS ASSIGNED TO SERVICES, Please remove line_profile from SERVICES and then try to Delete it again!");
				return $error;
		}
		
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("DELETE FROM LINE_PROFILE where ID='$this->id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function build_table_line_profile() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("select ID, NAME, LINE_PROFILE_ID from LINE_PROFILE");
		} catch (PDOException $e) {
			$error =  "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
			
	} 
	
	
	function get_data_line_profile() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT NAME, LINE_PROFILE_ID from LINE_PROFILE where ID='$this->id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->name = $row["NAME"];
			$this->line_profile_id = $row["LINE_PROFILE_ID"];

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
