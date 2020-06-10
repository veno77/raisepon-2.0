<?php
include_once("db_connect_class.php");
class backup {
	private $id;
	private $name;
	public $ip_address;
	public $username;
	public $password;
	public $directory;
	public $email_id;
	public $email;
	private $submit;
	
	function __construct() {
		if (!empty($_SERVER["REQUEST_METHOD"])) {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$this->id = isset($_POST['id'])	? $this->test_input($_POST['id']) : null;
				$this->name = isset($_POST['name'])	? $this->test_input($_POST['name']) : null;
				$this->ip_address = isset($_POST['ip_address'])	? $this->test_input($_POST['ip_address']) : null;
				$this->username = isset($_POST['username'])	? $this->test_input($_POST['username']) : null;
				$this->password = isset($_POST['password'])	? $this->test_input($_POST['password']) : null;
				$this->directory = isset($_POST['directory'])	? $this->test_input($_POST['directory']) : null;
				$this->email_id = isset($_POST['email_id'])	? $this->test_input($_POST['email_id']) : null;
				$this->email = isset($_POST['email'])	? $this->test_input($_POST['email']) : null;
				$this->submit = isset($_POST['SUBMIT'])	? $this->test_input($_POST['SUBMIT']) : null;
			}
		
			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				$this->id = isset($_GET['id'])	? $this->test_input($_GET['id']) : null;
			}
		}
	}
	
	function getSubmit() {
		return $this->submit;
	}
	function getId() {
		return $this->id;
	}
	
	function getName() {
		return $this->name;
	}
	
	function getIp_address() {
		return $this->ip_address;
	}
	
	function getUsername() {
		return $this->username;
	}
	function getPassword() {
		return $this->password;
	}
	function getDirectory() {
		return $this->directory;
	}
	function getEmailid() {
		return $this->email_id;
	}
	function getEmail() {
		return $this->email;
	}
	function create() {		
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("INSERT INTO BACKUP (NAME, IP_ADDRESS, USERNAME, PASSWORD, DIRECTORY) VALUES ('$this->name', INET_ATON('$this->ip_address'), '$this->username', '$this->password', '$this->directory')");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	 
	
	function edit() {		
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("UPDATE BACKUP SET NAME = '$this->name', IP_ADDRESS = INET_ATON('$this->ip_address'), USERNAME = '$this->username', PASSWORD = '$this->password', DIRECTORY = '$this->directory' where ID = '$this->id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function delete() {
		
		// CHECK IF BACKUP FTP IS ASSIGNED TO ANY OLT
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT BACKUP from OLT where BACKUP =  '$this->id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["BACKUP"])
				$error = ("ERROR: THIS BACKUP FTP IS ASSIGNED TO OLT, Please remove it from OLT config and then delete it!!");
				return $error;
		}
		//DELETE FTP from Database
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("DELETE FROM BACKUP where ID='$this->id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}

	function create_email() {	
		// CHECK EMAIL for DUPLICATES
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT EMAIL from BACKUP_EMAIL where EMAIL = '$this->email'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
	        exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["EMAIL"])
				$error = "ERROR!  DUPLICATE EMAIL!";
			return $error;
		}	
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("INSERT INTO BACKUP_EMAIL (EMAIL) VALUES ('$this->email')");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	 
	
	function edit_email() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT EMAIL from BACKUP_EMAIL where EMAIL = '$this->email'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
	        exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["EMAIL"])
				$error = "ERROR!  DUPLICATE EMAIL!";
			return $error;
		}		
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("UPDATE BACKUP_EMAIL SET EMAIL = '$this->email' where ID = '$this->email_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function delete_email() {
		//DELETE EMAIL from Database
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("DELETE FROM BACKUP_EMAIL where ID='$this->email_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function build_table() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME, INET_NTOA(IP_ADDRESS) as IP_ADDRESS, USERNAME, PASSWORD, DIRECTORY from BACKUP");
		} catch (PDOException $e) {
			$error =  "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
			
	} 
	
	function build_table_email() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, EMAIL from BACKUP_EMAIL");
		} catch (PDOException $e) {
			$error =  "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
			
	}
	function get_data() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME, INET_NTOA(IP_ADDRESS) as IP_ADDRESS, USERNAME, PASSWORD, DIRECTORY from BACKUP where ID='$this->id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->id =  $row["ID"];
			$this->name = $row["NAME"];
			$this->ip_address = $row["IP_ADDRESS"];
			$this->username = $row["USERNAME"];
			$this->password = $row["PASSWORD"];
			$this->directory = $row["DIRECTORY"];
		}		
	}
	
	function get_username(){
		$conn = db_connect::getInstance();
		$sql_username = $conn->getUsername();
		return $sql_username;
	}
	
	function get_data_email() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, EMAIL from BACKUP_EMAIL where ID='$this->email_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->email_id =  $row["ID"];
			$this->email = $row["EMAIL"];
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
