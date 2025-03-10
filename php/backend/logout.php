<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the homepage (index.php)
header("Location: http://localhost/school_bus_system/index.php");
exit();
?>