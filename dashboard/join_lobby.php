<?php
session_start();
require '../php/backend/db_connect.php'; // Database connection

// Check if the user is logged in and is not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
    header("Location: lobbies.php");
    exit();
}

// Validate lobby ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Lobby ID.");
}

$lobby_id = $_GET['id'];

try {
    // Update the lobby status to "online"
    $query = "UPDATE lobbies SET status = 'online' WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $lobby_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect back to lobbies page
    header("Location: lobbies.php");
    exit();
} catch (PDOException $e) {
    die("Database Query Failed: " . $e->getMessage());
}
?>