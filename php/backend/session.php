<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Redirect to login if not logged in
    exit();
}

function formatPhoneNumber($phone) {
    return preg_replace("/(\d{4})(\d{3})(\d{4})/", "$1 $2 $3", $phone);
}
?>