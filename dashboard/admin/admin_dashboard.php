<?php
include '../../php/backend/session.php';

$system = "/school_bus_system//";
$directory = $_SERVER['DOCUMENT_ROOT'] . $system;
// Start session only if it's not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Access user details from the session
$full_name = $_SESSION['full_name'] ?? 'Admin';
$profile_picture = $_SESSION['profile_picture'] ?? '../../assets/images/Default-PFP.jpg';

// Array of random messages
$messages = [
    "Welcome back, Admin! Ready to manage the system?",
    "Keep up the great work! The system is in good hands!",
    "Don't forget to check user reports and updates!",
    "Your dashboard is your control center. Use it wisely!",
    "Admin privilege comes with responsibility. Keep going!",
    "Always ensure the system runs smoothly for the users!",
    "Your leadership makes this system better every day!",
    "Stay sharp, stay secure, and manage efficiently!",
    "Great power, great responsibility—you're doing great!",
    "Another day of improving the system. Keep it up!"
];

// Pick a single random message
$random_message = $messages[array_rand($messages)];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
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
        .message-box {
            margin-top: 10px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 8px;
            max-width: 600px;
            font-size: 20px;
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
                    <li><a class="dropdown-item text-danger" href="../../php/backend/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<?php include $directory .'/php/frontend/sidebar_component.php'; ?>

<!-- Content -->
<div class="content">
    <h1>Welcome, <strong><?php echo htmlspecialchars($full_name); ?>!</strong></h1>

    <!-- Role Message -->
    <p style="font-size: 18px; color: #333; font-weight: bold;">You are logged in as an Admin.</p>

    <!-- Randomized Message -->
    <div class="message-box">
        <p><?php echo $random_message; ?></p>
    </div>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
</script>
</body>
</html>