<?php

class db_connect {
	private $mysql_user = "root";
	private $mysql_pass = "blahsux12321";
	public $db;
	private static $instance;
	private function __construct() {
		try {
			$this->db = new PDO('mysql:host=localhost;dbname=gpon;charset=utf8', $this->mysql_user, $this->mysql_pass);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			echo 'Connection Failed: ' . $e->getMessage();
			exit;
		}
		
	}
	
	 public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }
	
}


?>
