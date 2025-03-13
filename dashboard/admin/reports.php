<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Unauthorized access. Redirecting...";
    header("Refresh:2; url=../../frontend/login.php");
    exit();
}


$system = "/school_bus_system/";
$directory = $_SERVER['DOCUMENT_ROOT'] . $system;

$role = ucfirst($_SESSION['role']) ?? 'Admin';
$profile_picture = htmlspecialchars($_SESSION['profile_picture'] ?? '../../assets/images/Default-PFP.jpg');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('../../assets/images/AU-EEC.jpg') no-repeat center center fixed;
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
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                <img src="../../assets/images/AU-logo.png" alt="AU Logo">
                Arellano University - Elisa Esguerra Campus
            </a>
            <div class="ms-auto nav-icons">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle nav-profile" type="button" id="profileDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo $profile_picture; ?>" alt="Profile Picture">
                        <span class="role-badge"><?php echo $role; ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item text-danger" href="../../php/backend/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <?php include $directory . '/php/frontend/sidebar_component.php'; ?>

    <div class="content">
        <h1>Reports</h1>
        <p style="font-size: 18px; color: #fff; font-weight: bold;">View and manage system reports.</p>
        <div class="message-box">
            <p style="color: #000 !important;">This is where reports will be displayed.</p>
        </div>


        <div class="container">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-student-logs-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-student-logs" type="button" role="tab" aria-controls="pills-student-logs"
                        aria-selected="true">Student Logs</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-bus-logs-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-bus-logs" type="button" role="tab" aria-controls="pills-bus-logs"
                        aria-selected="false">Bus Logs</button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-student-logs" role="tabpanel"
                    aria-labelledby="pills-student-logs-tab">
                    <?php include $directory . 'php/frontend/report_components/student_logs.php'; ?>
                </div>
                <div class="tab-pane fade" id="pills-bus-logs" role="tabpanel" aria-labelledby="pills-bus-logs-tab">
                    <?php include $directory . 'php/frontend/report_components/bus_logs.php'; ?>
                </div>
            </div>
        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>