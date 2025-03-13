<?php


include '../../php/backend/session.php';
include '../../php/backend/db_connect.php';

// Start session only if it's not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$system = "/school_bus_system//";
$directory = $_SERVER['DOCUMENT_ROOT'] . $system;
// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Access user details from the session
$full_name = $_SESSION['full_name'] ?? 'Admin';
$profile_picture = $_SESSION['profile_picture'] ?? '../../assets/images/Default-PFP.jpg';

// Fetch users from the database using PDO
try {
    $stmt = $conn->prepare("SELECT id, full_name, email, role FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Inventory</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
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
                <img src="../../assets/images/AU-logo.png" alt="AU Logo" height="50">
                Arellano University - Elisa Esguerra Campus
            </a>
            <div class="ms-auto">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="profileDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span><?php echo htmlspecialchars($full_name); ?></span>
                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" width="40"
                            height="40" class="rounded-circle">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item text-danger" href="../../php/backend/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <?php include $directory . '/php/frontend/sidebar_component.php'; ?>

    <!-- Content -->
    <div class="content">
        <?php include $directory . '/php/frontend/add_bus_form.php'; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>