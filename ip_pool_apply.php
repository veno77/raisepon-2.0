<?php
include_once("common.php");
include_once("classes/ip_pool_class.php");

if ($user_class < "9")
	exit();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$ip_pool_obj = new ip_pool();
$ip_binding_id = $ip_pool_obj->getBinding_id();
$ip_pool_obj->get_data_olt_ip_pool();  
$ip_pool_obj->apply_pool();  

}
?>