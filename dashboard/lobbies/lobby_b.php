<?php
session_start();
require '../../php/backend/db_connect.php'; // Ensure correct path

// Redirect if the user is not logged in or is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'admin') {
    header("Location: ../../frontend/login.php");
    exit();
}

// Get user details from session
$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$role = $_SESSION['role']; // Student, Driver, Teacher

// Extract the lobby name dynamically
$file_name = basename(__FILE__, ".php"); // Gets "lobby_a", "lobby_b", or "lobby_c"
$lobby_letter = strtoupper(substr($file_name, -1)); // Extracts 'A', 'B', or 'C'
$lobby_name = "Lobby " . $lobby_letter; // Converts to "Lobby A", "Lobby B", "Lobby C"

try {
    // Check if user already joined this lobby
    $checkQuery = "SELECT * FROM joined_users WHERE user_id = :user_id AND lobby_name = :lobby_name";
    $stmt = $conn->prepare($checkQuery);
    $stmt->execute(['user_id' => $user_id, 'lobby_name' => $lobby_name]);

    if ($stmt->rowCount() === 0) {
        // Insert the user into the joined_users table
        $insertQuery = "INSERT INTO joined_users (user_id, full_name, role, lobby_name) 
                        VALUES (:user_id, :full_name, :role, :lobby_name)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->execute([
            'user_id' => $user_id,
            'full_name' => $full_name,
            'role' => $role,
            'lobby_name' => $lobby_name
        ]);

        // Update lobby status to online
        $updateLobby = "UPDATE lobbies SET status = 'online' WHERE lobby_name = :lobby_name";
        $stmt = $conn->prepare($updateLobby);
        $stmt->execute(['lobby_name' => $lobby_name]);
    }

    // Fetch all users in this lobby
    $fetchUsersQuery = "SELECT full_name, role FROM joined_users WHERE lobby_name = :lobby_name";
    $stmt = $conn->prepare($fetchUsersQuery);
    $stmt->execute(['lobby_name' => $lobby_name]);
    $joinedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lobby_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1><?php echo $lobby_name; ?></h1>
        <p>Welcome, <strong><?php echo htmlspecialchars($full_name); ?></strong>! You have joined as a <strong><?php echo ucfirst($role); ?></strong>.</p>
        
        <h3>Users in this Lobby:</h3>
        <ul>
            <?php foreach ($joinedUsers as $user): ?>
                <li><?php echo htmlspecialchars($user['full_name']) . " - " . ucfirst($user['role']); ?></li>
            <?php endforeach; ?>
        </ul>

        <a href="../lobbies.php" class="btn btn-primary">Back to Lobbies</a>
    </div>
</body>
</html>