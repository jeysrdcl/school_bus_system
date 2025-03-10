<?php
include '../../php/backend/db_connect.php';

// Start session only if it's not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect back to users list
        header("Location: users.php?success=deleted");
        exit();
    } catch (PDOException $e) {
        die("Error deleting user: " . $e->getMessage());
    }
} else {
    header("Location: users.php");
    exit();
}
?>