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
$profile_picture = $_SESSION['profile_picture'] ?? '../../assets/images/Default-PFP.jpg';
$role = ucfirst($_SESSION['role']); // Student, Driver, Teacher

// Extract the lobby name dynamically
$file_name = basename(__FILE__, ".php"); // "lobby_a", "lobby_b", "lobby_c"
$lobby_letter = strtoupper(substr($file_name, -1)); // Gets 'A', 'B', 'C'
$lobby_name = "Lobby " . $lobby_letter;

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
            'role' => strtolower($role),
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

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('../../assets/images/AU-EEC.jpg') no-repeat center center fixed;
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
        .message-box {
            margin-top: 10px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 8px;
            max-width: 600px;
            font-size: 18px;
            color: #333;
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
        <li><a href="../dashboard.php">Dashboard</a></li>
        <li><a href="../index.php">Site Home</a></li>
        <li><a href="../lobbies.php">Lobby</a></li>
    </ul>
</div>

<!-- Content -->
<div class="content">
    <h1><?php echo $lobby_name; ?></h1>
    <p style="font-size: 18px; color: #333; font-weight: bold;">
        You have joined as a <strong><?php echo $role; ?></strong>.
    </p>

    <h3>Users in this Lobby:</h3>
    <ul>
        <?php foreach ($joinedUsers as $user): ?>
            <li><?php echo htmlspecialchars($user['full_name']) . " - " . ucfirst($user['role']); ?></li>
        <?php endforeach; ?>
    </ul>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>