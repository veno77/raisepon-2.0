<?php

header("Location: login.php");

include ("common.php");

// Begin the session

// Unset all of the session variables.
session_unset();

// Destroy the session.
session_destroy();

?>

