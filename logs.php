<?php
//include ("common.php");
session_start();
if (!isset($_SESSION["id"]) && false == strpos($_SERVER['REQUEST_URI'], 'login.php')) {
header("Location: login.php");
}
$page = $_SERVER['PHP_SELF'];
$sec = "5";
header("Refresh: $sec; url=$page");
//header('Content-Type: text/html; charset=utf-8');
print "<link rel=\"stylesheet\" href=\"./css/bootstrap.min.css\">";
print "<script src=\"./js/jquery-3.1.1.min.js\"></script>";
print "<script src=\"./js/bootstrap.min.js\"></script>";
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<?php
$user_class = isset($_SESSION["type"]);
$cur_user_id = isset($_SESSION["id"]);

include ("navigation.php");

print "<h2><center>Logs from OLTs<center></h2>";

$my_file = "/var/log/gpon.log";
$lines = file($my_file);
$lines=str_replace("^M","",$lines);
$ii = "0";
for ($i = count($lines) - 1; $i >= 0; $i--) {
	echo $lines[$i] . '<br/>';
	$ii++;
	if ($ii > 100)
	exit();
}

function echoActiveClassIfRequestMatches($requestUri)
{
    $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");

    if ($current_file_name == $requestUri)
        echo 'class="active"';
}


?>
