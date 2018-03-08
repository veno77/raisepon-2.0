<?php
snmp_set_oid_numeric_print(TRUE);
snmp_set_quick_print(TRUE);
snmp_set_enum_print(TRUE); 
$session = new SNMP(SNMP::VERSION_2C, '192.168.102.97', 'public');
$output = $session->walk("1.3.6.1.4.1.8886.18.3.1.2.2");
$array = Array();
$a = Array();
foreach ($output as $k => $v) {
    $keys = explode(".", $k);
//    $current = array($keys[0] => array($keys[1]=>array($keys[2]=>$v)));
 foreach ($keys as $key) {
		$a = $a.[$key];
        }
	print_r($a);
    $array = array_replace_recursive($array, $current);
}

print_r($array);

?>
