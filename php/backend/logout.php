<?php
session_start();
$_SESSION = array();
session_destroy();

header("Location: http://localhost/school_bus_system/index.php");
exit();
?>