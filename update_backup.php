<?php
include_once("classes/db_connect_class.php");
include ("classes/olt_class.php");
include ("classes/snmp_class.php");
include ("classes/backup_class.php");

$olt_obj = new olt(); 
$rows = $olt_obj->build_table_olt(); 
foreach ($rows as $row) {
	if ($row['BACKUP_ID'] != "NULL") {
		$olt_obj->setBackup_id($row['BACKUP_ID']);
		$rows_backup = $olt_obj->get_data_backup();
		foreach ($rows_backup as $row_backup) {
			$snmp_obj = new snmp_oid();
			snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
			$session = new SNMP(SNMP::VERSION_2C, $row['IP_ADDRESS'], $row['RW'], 100000, 2);
			$status = $session->get($snmp_obj->get_pon_oid("olt_status_oid", "OLT"));
			if ($status) {
				$raisecomOnlineUpgradeV2Protocol = $snmp_obj->get_pon_oid("raisecomOnlineUpgradeV2Protocol", "OLT") . ".10000000.0";
				$raisecomOnlineUpgradeV2OprType = $snmp_obj->get_pon_oid("raisecomOnlineUpgradeV2OprType", "OLT") . ".10000000.0";
				$raisecomOnlineUpgradeV2FileType = $snmp_obj->get_pon_oid("raisecomOnlineUpgradeV2FileType", "OLT") . ".10000000.0";
				$raisecomOnlineUpgradeV2ServerAddress = $snmp_obj->get_pon_oid("raisecomOnlineUpgradeV2ServerAddress", "OLT") . ".10000000.0";
				$raisecomOnlineUpgradeV2FileName = $snmp_obj->get_pon_oid("raisecomOnlineUpgradeV2FileName", "OLT") . ".10000000.0";
				$raisecomOnlineUpgradeV2UserName = $snmp_obj->get_pon_oid("raisecomOnlineUpgradeV2UserName", "OLT") . ".10000000.0";
				$raisecomOnlineUpgradeV2UserPassword = $snmp_obj->get_pon_oid("raisecomOnlineUpgradeV2UserPassword", "OLT") . ".10000000.0";
				$raisecomOnlineUpgradeV2NotificationOnCompletion = $snmp_obj->get_pon_oid("raisecomOnlineUpgradeV2NotificationOnCompletion", "OLT") . ".10000000.0";
				$raisecomOnlineUpgradeV2FailCause = $snmp_obj->get_pon_oid("raisecomOnlineUpgradeV2FailCause", "OLT") . ".10000000.0";
				$raisecomOnlineUpgradeV2EntryRowStatus = $snmp_obj->get_pon_oid("raisecomOnlineUpgradeV2EntryRowStatus", "OLT") . ".10000000.0";
				$session->set(array($raisecomOnlineUpgradeV2Protocol, $raisecomOnlineUpgradeV2OprType, $raisecomOnlineUpgradeV2FileType, $raisecomOnlineUpgradeV2ServerAddress, $raisecomOnlineUpgradeV2FileName, $raisecomOnlineUpgradeV2UserName, $raisecomOnlineUpgradeV2UserPassword, $raisecomOnlineUpgradeV2NotificationOnCompletion, $raisecomOnlineUpgradeV2EntryRowStatus), array('i', 'i', 'i', 'a', 's', 's', 's', 'i', 'i'), array('2', '1', '3', $row_backup['IP_ADDRESS'], $row_backup['DIRECTORY'] . $row['NAME'] . "_" . date("Y-m-d-H-i") . ".conf", $row_backup['USERNAME'], $row_backup['PASSWORD'], '1', '4')); 
			}
			sleep(30);
			$reason = $session->get($raisecomOnlineUpgradeV2EntryRowStatus);
			$olt_id = $row['ID'];
			$error = $olt_obj->backup_status($olt_id, $reason);
			if ($error)
				echo $error;
		}
	}	
}
$sql_username = db_connect::getUsername();
$sql_password = db_connect::getPassword();
$date = date("Y-m-d-H-i");
$name = $date . "_raisepon.sql";
$filename = "/tmp/" . $date . "_raisepon.sql";
exec("/usr/local/bin/mysqldump -u " . $sql_username . " -p\"" . $sql_password . "\" raisepon > " . $filename);	
$backup_obj = new backup();
$rows = $backup_obj->build_table_email(); 
foreach ($rows as $row) {
	if ($row['ID'] != "NULL") {
		$from_email = "raisepon@raisepon.org";
		$reply_to_email = "raisepon@raisepon.org";
		$recipient_email = $row['EMAIL'];	
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		$encoded_content = chunk_split(base64_encode($contents)); 
		$boundary = md5("random");
		$file_type = filetype($filename);
		$message = "Raisepon Database Backup";
		//header 
		$headers = "MIME-Version: 1.0\r\n"; // Defining the MIME version 
		$headers .= "From:".$from_email."\r\n"; // Sender Email 
		$headers .= "Reply-To: ".$reply_to_email."\r\n"; // Email addrress to reach back 
		$headers .= "Content-Type: multipart/mixed;"; // Defining Content-Type 
		$headers .= "boundary = $boundary\r\n"; //Defining the Boundary 
		
		//plain text  
		$body = "--$boundary\r\n"; 
		$body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n"; 
		$body .= "Content-Transfer-Encoding: base64\r\n\r\n";  
		$body .= chunk_split(base64_encode($message));  
		//attachment 
		$body .= "--$boundary\r\n"; 
		$body .="Content-Type:".$file_type."; name=".$name."\r\n"; 
		$body .="Content-Disposition: attachment; filename=".$name."\r\n"; 
		$body .="Content-Transfer-Encoding: base64\r\n"; 
		$body .="X-Attachment-Id: ".rand(1000, 99999)."\r\n\r\n";  
		$body .= $encoded_content; // Attaching the encoded file with email 
		$sentMailResult = mail($recipient_email, "Raisepon Database Backup", $body, $headers); 
		if($sentMailResult )  
		{ 
		   echo "File Sent Successfully."; 
		} 
		else
		{ 
		   die("Sorry but the email could not be sent. Please check the log files!"); 
		} 
	}
}
unlink($filename); // delete the file after attachment sent. 

?>
