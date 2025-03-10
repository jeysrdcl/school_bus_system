<?php
include '../../php/backend/session.php';
include '../../php/backend/db_connect.php';

// Start session if not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure only admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Define permanent lobbies
$permanentLobbies = [
    ['id' => 1, 'lobby_name' => 'Lobby A', 'status' => 'offline', 'created_at' => '2025-03-06'],
    ['id' => 2, 'lobby_name' => 'Lobby B', 'status' => 'offline', 'created_at' => '2025-03-06'],
    ['id' => 3, 'lobby_name' => 'Lobby C', 'status' => 'offline', 'created_at' => '2025-03-06']
];

// Ensure permanent lobbies are stored in the database
foreach ($permanentLobbies as $lobby) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM lobbies WHERE id = :id");
    $stmt->execute(['id' => $lobby['id']]);
    $exists = $stmt->fetchColumn();

    if (!$exists) {
        $stmt = $conn->prepare("INSERT INTO lobbies (id, lobby_name, status, created_at) VALUES (:id, :lobby_name, :status, :created_at)");
        $stmt->execute([
            'id' => $lobby['id'],
            'lobby_name' => $lobby['lobby_name'],
            'status' => $lobby['status'],
            'created_at' => $lobby['created_at']
        ]);
    }
}

// Fetch unique lobbies (avoid duplicate permanent lobbies)
try {
    $stmt = $conn->prepare("SELECT * FROM lobbies ORDER BY created_at DESC");
    $stmt->execute();
    $dbLobbies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Replace merging logic to avoid duplicates
$lobbyIds = array_column($dbLobbies, 'id'); // Get all lobby IDs from the database
$uniqueLobbies = [];

foreach ($permanentLobbies as $lobby) {
    if (!in_array($lobby['id'], $lobbyIds)) {
        $uniqueLobbies[] = $lobby;
    }
}

// Combine database lobbies with non-duplicate permanent ones
$lobbies = array_merge($dbLobbies, $uniqueLobbies);

$full_name = $_SESSION['full_name'] ?? 'Admin';
$profile_picture = $_SESSION['profile_picture'] ?? '../../assets/images/Default-PFP.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby Management</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

  <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('../../assets/images/AU-EEC.jpg') no-repeat center center fixed;
            background-size: cover;
            background-color: #0056b3;
            position: relative;
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
        .navbar-nav .nav-link {
            color: white !important;
            font-size: 1.1rem;
        }
        .nav-icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .nav-icons i {
            font-size: 1.4rem;
            color: white;
            cursor: pointer;
            position: relative;
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
            padding-top: 10px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 10;
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
        .content {
            margin-left: 260px;
            padding: 20px;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="http://localhost/school_bus_system/index.php">
            <img src="../../assets/images/AU-logo.png" alt="AU Logo">
            Arellano University - Elisa Esguerra Campus
        </a>
        <div class="ms-auto">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown">
                    <span><?php echo htmlspecialchars($full_name); ?></span>
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" width="40" height="40" style="border-radius: 50%;">
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item text-danger" href="../../php/backend/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<div class="sidebar">
    <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="users.php">Users</a></li>
        <li><a href="lobby.php" class="active">Lobbies</a></li>
        <li><a href="reports.php">Reports</a></li>
        <li><a href="data_logs.php">Data Logs</a></li>
    </ul>
</div>

<!-- Content -->
<div class="content">
    <h2>Lobby Management</h2>

    <div class="table-container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Lobby Name</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lobbies as $lobby): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($lobby['id']); ?></td>
                        <td><?php echo htmlspecialchars($lobby['lobby_name']); ?></td>
                        <td>
                            <?php 
                                // If a user joins, change status to 'online'
                                echo ($lobby['status'] === 'online') ? 'Online' : 'Offline'; 
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($lobby['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
