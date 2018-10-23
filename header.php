<?php


print "<head>";
print "<link rel=\"stylesheet\" href=\"./css/bootstrap.min.css\">";
print "<script src=\"./js/jquery-3.3.1.min.js\"></script>";
print "<script src=\"./js/bootstrap.min.js\"></script>";
print "<script src=\"./js/gpon.js\"></script>";
print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>";
print "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">";
print "<style>body{padding-top: 70px;}</style>";
print "</head>";

//session_cache_limiter('private_no_expire');
session_start();
if (!isset($_SESSION["id"]) && false == strpos($_SERVER['REQUEST_URI'], 'login.php')) {
//	header("Location: login.php");
	echo "<script>location='login.php'</script>";
}

$user_class = isset($_SESSION["type"]) ? $_SESSION["type"] : null;
$cur_user_id = isset($_SESSION["id"]) ? $_SESSION["id"] : null;



?>