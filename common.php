<?php
//session_cache_limiter('private_no_expire');
session_start();
if (!isset($_SESSION["id"]) && false == strpos($_SERVER['REQUEST_URI'], 'login.php')) {
//	header("Location: login.php");
	echo "<script>location='login.php'</script>";
}

$user_class = isset($_SESSION["type"]);
$cur_user_id = isset($_SESSION["id"]);

$pon_dropdown = array();

ini_set('display_errors','off');

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

function type2id($slot, $pon_port, $onu_id) {
        $vif = "0001";
        $slot = str_pad(decbin($slot),5, "0", STR_PAD_LEFT);
        $pon_port = str_pad(decbin($pon_port), 6, "0", STR_PAD_LEFT);
        $onu_id = str_pad(decbin($onu_id), 16, "0", STR_PAD_LEFT);
        $big_onu_id = bindec($vif . $slot . "0" . $pon_port . $onu_id);
        return $big_onu_id;
}

function type2ponid ($slot, $pon_port) {
        $slot = decbin($slot);
        $pon_port = str_pad(decbin($pon_port), 6, "0", STR_PAD_LEFT);
        $pon_id = bindec($slot . $pon_port);
        return $pon_id;
}

function echoActiveClassIfRequestMatches($requestUri)
{
    $current_file_name = basename($_SERVER['PHP_SELF'], ".php");

	if ($current_file_name == $requestUri)
        echo 'class="active"';
	
}

?>
