<?php

class db_connect {
	private static $mysql_user = "raisepon";
	private static $mysql_pass = "r41sepon";
	public $db;
	private static $instance;
	private function __construct() {
		try {
			$this->db = new PDO('mysql:host=localhost;dbname=raisepon;charset=utf8', self::$mysql_user, self::$mysql_pass);
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
	
	public static function getUsername() {
		return self::$mysql_user;
	}
	public static function getPassword() {
		return self::$mysql_pass;
	}
}


?>
