<?php
// show error reporting
error_reporting(E_ALL);
 
// set your default time-zone
date_default_timezone_set('Europe/Sofia');
 
// variables used for jwt
$key = "r41s3p0n";
$iss = "Raisepon";
$aud = "Raisepon";
$iat = time();
$nbf = $iat + 10;
$exp = $nbf + 3600;
?>