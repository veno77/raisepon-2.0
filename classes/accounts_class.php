<?php
include ("db_connect_class.php");

class accounts {
	private $account_id;
	private $username;
	private $password;
	private $type;
	private $submit;
	private $form_token;
	
	function __construct() {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$this->account_id = isset($_POST['account_id'])	? $this->test_input($_POST['account_id']) : null;
			$this->username = isset($_POST['username'])	? $this->test_input($_POST['username']) : null;
			$this->password = isset($_POST['password'])	? $this->test_input($_POST['password']) : null;
			$this->type = isset($_POST['type'])	? $this->test_input($_POST['type']) : null;
			$this->submit = isset($_POST['SUBMIT'])	? $this->test_input($_POST['SUBMIT']) : null;
			$this->form_token = isset($_POST['form_token'])	? $this->test_input($_POST['form_token']) : null;
		}
		
		if ($_SERVER["REQUEST_METHOD"] == "GET") {
			$this->account_id = isset($_GET['account_id'])	? $this->test_input($_GET['account_id']) : null;
		}
	}
	
	function getUsername() {
		return $this->username;
	}
	function getPassword() {
		return $this->password;
	}
	function getType() {
		return $this->type;
	}
	
	function getSubmit() {
		return $this->submit;
	}
	function getAccount_id() {
		return $this->account_id;
	}
	function getForm_token() {
		return $this->form_token;
	}

	function create() {
		$this->password = sha1( $this->password );
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("INSERT INTO ACCOUNTS (USERNAME, PASSWORD, TYPE) VALUES ('$this->username', '$this->password', '$this->type')");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	 
	
	function edit() {
		if ($this->password!=null) {
			$this->password = sha1( $this->password );
			try {
				$conn = db_connect::getInstance();
				$conn->db->query("UPDATE ACCOUNTS SET USERNAME = '$this->username', PASSWORD = '$this->password', TYPE = '$this->type' where ID = '$this->account_id'");
			} catch (PDOException $e) {
				$error = "Connection Failed:" . $e->getMessage() . "\n";
				return $error;
			}
		}
		if ($this->password==null) {
			try {
				$conn = db_connect::getInstance();
				$conn->db->query("UPDATE ACCOUNTS SET USERNAME = '$this->username', TYPE = '$this->type' where ID = '$this->account_id'");
			} catch (PDOException $e) {
				$error = "Connection Failed:" . $e->getMessage() . "\n";
				return $error;
			}
		}
	}
	
	function delete() {
		
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("DELETE FROM ACCOUNTS where ID='$this->account_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function build_table() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, USERNAME, TYPE from ACCOUNTS");
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
			$result = $conn->db->query("SELECT USERNAME, TYPE from ACCOUNTS where ID='$this->account_id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->username = $row["USERNAME"];
			$this->type = $row["TYPE"];
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
