<?php
include_once("db_connect_class.php");
class pon {
	private $pon_id;
	private $name;
	public $olt;
	public $slot_id;
	public $port_id;
	public $cards_model_id;
	private $submit;
	private $ip_address;
	
	function __construct() {
		if (!empty($_SERVER["REQUEST_METHOD"])) {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$this->pon_id = isset($_POST['pon_id'])	? $this->test_input($_POST['pon_id']) : null;
				$this->name = isset($_POST['name'])	? $this->test_input($_POST['name']) : null;
				$this->olt = isset($_POST['olt'])	? $this->test_input($_POST['olt']) : null;
				$this->slot_id = isset($_POST['slot_id'])	? $this->test_input($_POST['slot_id']) : null;
				$this->port_id = isset($_POST['port_id'])	? $this->test_input($_POST['port_id']) : null;
				$this->cards_model_id = isset($_POST['cards_model_id'])	? $this->test_input($_POST['cards_model_id']) : null;
				$this->submit = isset($_POST['SUBMIT'])	? $this->test_input($_POST['SUBMIT']) : null;
			}
			
			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				$this->pon_id = isset($_GET['id'])	? $this->test_input($_GET['id']) : null;
			}
		}
	}
	
	function getSubmit() {
		return $this->submit;
	}
	function getPon_id() {
		return $this->pon_id;
	}
	
	function getName() {
		return $this->name;
	}
	
	function getOlt() {
		return $this->olt;
	}
	
	function getSlot_id() {
		return $this->slot_id;
	}
	function getPort_id() {
		return $this->port_id;
	}
	function getCards_model_id() {
		return $this->cards_model_id;
	}
	
	function create_pon() {
		// CHECK PORT NUMBER with CARD MAXIMUM SUPPORTED
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT PORTS from CARDS_MODEL where ID = $this->cards_model_id");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
	        exit;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($this->port_id > $row["PORTS"]) {
				$error = "ERROR!  EXCEEDED CARD SUPPORTED PORTS LIMIT!";
				return $error;
			}
		}
		
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("INSERT INTO PON (NAME, OLT, SLOT_ID, PORT_ID, CARDS_MODEL_ID) VALUES ('$this->name', '$this->olt', '$this->slot_id', '$this->port_id' , '$this->cards_model_id')");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		
			
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT  INET_NTOA(IP_ADDRESS) as IP_ADDRESS, OLT_MODEL.TYPE from OLT LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID where OLT.ID='$this->olt'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ip_address = $row['IP_ADDRESS'] ;
			$type = $row['TYPE'];
		}
		
		//CREATE RRD
		$traffic = array("traffic", "unicast", "broadcast", "multicast");
		foreach ($traffic as $tr) {
			$rrd_name = dirname(dirname(__FILE__)) . "/rrd/" . $ip_address . "_" . $this->type2ponid($this->slot_id,$this->port_id) . "_" . $tr . ".rrd";
			$opts = array("--step", "300", "--start", "0",
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
	 
	
	function edit_pon() {
		
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("UPDATE PON SET NAME = '$this->name' where ID = '$this->pon_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
	}
	
	function delete_pon() {
		
		// CHECK IF PON IS ASSIGNED TO ANY CUSTOMER
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT PON_PORT from CUSTOMERS where PON_PORT =  '$this->pon_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["PON_PORT"])
				$error = ("PON PORT IS ASSIGNED TO CUSTOMERS, Please remove PON PORT from customers to Delete it!");
				return $error;
		}
		//DELETE PON from Database
		try {
			$conn = db_connect::getInstance();
			$conn->db->query("DELETE FROM PON where ID='$this->pon_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		
		try {
                        $conn = db_connect::getInstance();
                        $result = $conn->db->query("SELECT  INET_NTOA(IP_ADDRESS) as IP_ADDRESS, OLT_MODEL.TYPE from OLT LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID where OLT.ID='$this->olt'");
                } catch (PDOException $e) {
                        $error = "Connection Failed:" . $e->getMessage() . "\n";
                        return $error;
                }

                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $ip_address = $row['IP_ADDRESS'] ;
                        $type = $row['TYPE'];
                }
	
		//DELETE RRD files
		$traffic = array("traffic", "unicast", "broadcast", "multicast");
		foreach ($traffic as $tr) {
			$rrd_name = dirname(dirname(__FILE__)) . "/rrd/" . $ip_address . "_" . $this->type2ponid($this->slot_id,$this->port_id) . "_" . $tr . ".rrd";
			unlink($rrd_name);
		}
	}
	
	function build_table_pon() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT PON.ID, PON.NAME as NAME, OLT, SLOT_ID, PORT_ID, CARDS_MODEL_ID, CARDS_MODEL.NAME as CARDS_MODEL_NAME  from PON LEFT JOIN OLT on PON.OLT=OLT.ID LEFT JOIN CARDS_MODEL on PON.CARDS_MODEL_ID=CARDS_MODEL.ID where OLT.ID='$this->olt' order by SLOT_ID, PORT_ID");
		} catch (PDOException $e) {
			$error =  "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
			
	} 
	
	
	function get_data_pon() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT ID, NAME, OLT, SLOT_ID, PORT_ID, CARDS_MODEL_ID from PON where ID='$this->pon_id'");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->pon_id =  $row["ID"];
			$this->name = $row["NAME"];
			$this->slot_id = $row["SLOT_ID"];
			$this->port_id = $row["PORT_ID"];
			$this->cards_model_id = $row["CARDS_MODEL_ID"];
			$this->olt = $row["OLT"];
		}	
		
		
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
	
	function get_Cards_model() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT * from CARDS_MODEL");
		} catch (PDOException $e) {
			$error = "Connection Failed:" . $e->getMessage() . "\n";
			return $error;
		}
		$rows = $result->fetchAll();
		return $rows;
	}
	
	function getOlt_name() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT NAME from OLT WHERE ID=" . $this->olt);
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}

		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			return $row['NAME'];
		}
	}
	
	function getCards_model_name() {
		try {
			$conn = db_connect::getInstance();
			$result = $conn->db->query("SELECT NAME from CARDS_MODEL WHERE ID=" . $this->cards_model_id);
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}

		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			return $row['NAME'];
		}
	}
	function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
	}
	
	function type2ponid ($slot, $pon_port) {
        $slot = decbin($slot);
        $pon_port = str_pad(decbin($pon_port), 6, "0", STR_PAD_LEFT);
        $pon_id = bindec($slot . $pon_port);
        return $pon_id;
	}
	function type3ponid ($slot, $pon_port) {
		$interface_type_id = "1010";
		$system_id = "00";
        $slot = str_pad(decbin($slot), 5, "0", STR_PAD_LEFT);
        $pon_port = str_pad(decbin($pon_port), 6, "0", STR_PAD_LEFT);
        $tail = "000000000000000";
        $pon_id =bindec($interface_type_id . $system_id . $slot . $pon_port . $tail) + 1;       
        return $pon_id;
	}	
	
}




?>
