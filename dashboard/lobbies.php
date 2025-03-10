<?php
session_start();
require '../php/backend/db_connect.php'; // Ensure this file connects to the database

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'admin') {
    header("Location: ../frontend/login.php");
    exit();
}

// Access user details from the session
$full_name = $_SESSION['full_name'] ?? 'Guest';
$profile_picture = $_SESSION['profile_picture'] ?? '../assets/images/Default-PFP.jpg';

// Fetch online and offline lobbies
try {
    $query = "SELECT id, lobby_name, status FROM lobbies";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $lobbies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Query Failed: " . $e->getMessage());
}

// Separate online and offline lobbies
$onlineLobbies = [];
$offlineLobbies = [];
foreach ($lobbies as $lobby) {
    if ($lobby['status'] === 'online') {
        $onlineLobbies[] = $lobby;
    } else {
        $offlineLobbies[] = $lobby;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Bus System - Lobbies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('../assets/images/AU-EEC.jpg') no-repeat center center fixed;
            background-size: cover;
            background-color: #0056b3;
        }
        .navbar {
            background-color: rgba(0, 86, 179, 0.95) !important;
        }
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        .navbar-brand img {
            height: 60px;
            margin-right: 10px;
        }
        .nav-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .sidebar {
            width: 250px;
            background: #004aad;
            padding: 20px;
            height: 100vh;
            position: fixed;
            left: 0;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 10px;
            font-size: 1.2rem;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
        }
        .sidebar ul li a:hover {
            background: #003380;
        }
        .container {
            margin-left: 270px;
            padding: 20px;
        }
        .lobby-box {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .join-btn {
            padding: 8px 15px;
            border: none;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            background: green;
        }
        .offline {
            background-color: #f8d7da;
        }
        .online {
            background-color: #d4edda;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="http://localhost/school_bus_system/index.php">
            <img src="../assets/images/AU-logo.png" alt="AU Logo">
            Arellano University - Elisa Esguerra Campus
        </a>
        <div class="ms-auto nav-icons">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle nav-profile" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <span><?php echo htmlspecialchars($full_name); ?></span>
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                    <li><a class="dropdown-item text-danger" href="../php/backend/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<div class="sidebar">
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="index.php">Site Home</a></li>
        <li><a href="lobbies.php">Lobby</a></li>
    </ul>
</div>

<!-- Content -->
<div class="container mt-5">
    <h1 class="mb-4">Lobbies</h1>

    <!-- Online Lobbies -->
    <h2 class="text-success">Online Lobbies</h2>
    <?php if (!empty($onlineLobbies)): ?>
        <?php foreach ($onlineLobbies as $lobby): ?>
            <div class="alert online d-flex justify-content-between">
                <span><?php echo htmlspecialchars($lobby['lobby_name']); ?> (Online)</span>
                <a href="lobbies/<?php echo strtolower(str_replace(' ', '_', $lobby['lobby_name'])); ?>.php" class="btn btn-success">Join</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No online lobbies available.</p>
    <?php endif; ?>

    <!-- Offline Lobbies -->
    <h2 class="text-danger mt-4">Offline Lobbies</h2>
    <?php if (!empty($offlineLobbies)): ?>
        <?php foreach ($offlineLobbies as $lobby): ?>
            <div class="alert offline d-flex justify-content-between">
                <span><?php echo htmlspecialchars($lobby['lobby_name']); ?> (Offline)</span>
                <a href="lobbies/<?php echo strtolower(str_replace(' ', '_', $lobby['lobby_name'])); ?>.php" class="btn btn-success">Join</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No offline lobbies available.</p>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>