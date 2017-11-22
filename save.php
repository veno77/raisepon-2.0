<?php
include ("common.php");

$ip_address = $ro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ip_address = test_input($_POST["ip_address"]);
    $rw = test_input($_POST["rw"]);

	$save_oid = '1.3.6.1.4.1.8886.1.2.1.1.0';
	$session = new SNMP(SNMP::VERSION_2C, $ip_address, $rw, 2000000, 3);
        $session->set($save_oid, 'i', '2');
       	if ($session->getError()) {
       		exit(var_dump($session->getError()));
	} else {

	print "OLT Configuration SAVED succesfully!";
	}

}


?>
