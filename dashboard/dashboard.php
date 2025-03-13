<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

if ($_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher') {
    header("Location: ../frontend/login.php");
    exit();
}

$profile_picture = $_SESSION['profile_picture'] ?? '../assets/images/Default-PFP.jpg';

$messages = [
    "How are you doing? Ready to get home safely?",
    "Hope you had a great day! Stay safe on your way home!",
    "Don't forget to check your belongings before leaving!",
    "A safe journey home is a happy journey!",
    "Remember to follow traffic rules and stay alert!",
    "Take care and have a relaxing evening!",
    "See you tomorrow! Have a wonderful rest of the day!",
    "Enjoy your time at home and rest well!",
    "Stay safe and don't forget to hydrate!",
    "Another day, another step closer to your goals!"
];

$random_message = $messages[array_rand($messages)];

// Set correct role display
$roleMessage = "You are logged in as a Teacher.";
if ($_SESSION['role'] === 'admin') {
    $roleMessage = "You are logged in as an Admin.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Bus System - Dashboard</title>
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
            text-align: center;
        }
        .message-box {
            margin: 10px auto;
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

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="http://localhost/school_bus_system/index.php">
            <img src="../assets/images/AU-logo.png" alt="AU Logo">
            Arellano University - Elisa Esguerra Campus
        </a>
        <div class="ms-auto nav-icons">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle nav-profile" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item text-danger" href="../php/backend/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="sidebar">
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="index.php">Site Home</a></li>
        <li><a href="../dashboard/lobbies.php">Lobby</a></li>
    </ul>
</div>

<div class="content">
    <h1>Welcome, <strong>Guest!</strong></h1> <!-- Removed full_name -->
    <p style="font-size: 18px; color: #333; font-weight: bold;"><?php echo $roleMessage; ?></p>

    <div class="message-box">
        <p><?php echo $random_message; ?></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>