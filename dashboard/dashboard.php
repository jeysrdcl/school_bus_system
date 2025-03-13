<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

if ($_SESSION['role'] === 'admin') {
    header("Location: ../dashboard/admin/admin_dashboard.php");
    exit();
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher') {
    header("Location: ../frontend/login.php");
    exit();
}

$role = ucfirst($_SESSION['role']) ?? 'Unknown';
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('../assets/images/AU-EEC.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            overflow: hidden;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 86, 179, 0.8), rgba(0, 86, 179, 0.5)); 
            backdrop-filter: blur(5px); 
            z-index: -1;
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
        .role-badge {
            font-size: 16px;
            font-weight: bold;
            color: white;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 12px;
            border-radius: 15px;
            margin-right: 15px;
        }
        .sidebar {
            width: 250px;
            background: rgba(0, 74, 173, 0.9); 
            padding: 20px;
            padding-top: 10px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 20;
            backdrop-filter: blur(5px); 
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
            transition: background 0.3s ease;
        }
        .sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.1); 
        }
        .content {
            margin-left: 260px;
            padding: 20px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .message-box {
            margin: 10px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95); 
            border-radius: 12px;
            max-width: 600px;
            font-size: 20px;
            color: #000 !important; 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); 
            animation: fadeIn 1s ease-in-out; 
            border: 1px solid rgba(0, 0, 0, 0.1); 
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h1 {
            font-size: 2.5rem;
            color: #fff; 
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); 
        }
        p {
            font-size: 1.2rem;
            color: #fff; 
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body>
    <div class="overlay"></div>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="http://localhost/school_bus_system/index.php">
                <img src="../assets/images/AU-logo.png" alt="AU Logo">
                Arellano University - Elisa Esguerra Campus
            </a>
            <div class="ms-auto nav-icons">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle nav-profile" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo $profile_picture; ?>" alt="Profile Picture">
                        <span class="role-badge"><?php echo $role; ?></span>
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
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        </ul>
    </div>

    <div class="content">
        <h1>Welcome, <strong><?php echo $role; ?>!</strong></h1>
        <p style="font-size: 18px; color: #fff; font-weight: bold;">You are logged in as a <?php echo $role; ?>.</p>
        <div class="message-box">
            <p style="color: #000 !important;"><?php echo $random_message; ?></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>