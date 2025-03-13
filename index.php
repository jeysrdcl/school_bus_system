<?php
session_start();
// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    header("Location: http://localhost/school_bus_system/dashboard/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Track System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* Background styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('assets/images/AU-EEC.jpg') no-repeat center center fixed;
            background-size: cover;
            background-color: #0056b3; /* Fallback color */
        }

        /* Navbar styling */
        .navbar {
            background-color: rgba(0, 86, 179, 0.95) !important; /* Darker blue for better contrast */
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            height: 60px; /* Increased size */
            margin-right: 10px;
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-size: 1.1rem;
        }

        /* Buttons */
        .btn-login {
            background-color: #28a745; /* Green */
            color: white;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 5px;
        }

        .btn-signup {
            background-color: #ff7f00; /* Orange */
            color: white;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 5px;
        }

        .btn-login:hover {
            background-color: #218838;
        }

        .btn-signup:hover {
            background-color: #e86f00;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; /* Align content to the top */
            text-align: center;
            color: white;
            background: rgba(0, 0, 0, 0.4);
            width: 100%;
            padding-top: 100px; /* Increase this to move it higher */
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px; /* Reduce space below heading */
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 15px; /* Reduce space below paragraph */
            margin-top: -10px; /* Move button slightly higher */
        }

        .btn-custom {
            font-size: 1.2rem;
            padding: 10px 20px;
            font-weight: bold;
        }

        /* About Section */
        .about {
            position: absolute;
            top: 50%; /* Adjust based on placement in the image */
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            width: 60%;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 50px; /* Adds space between the section and the footer */
        }

        .about h2 {
            color: #004aad;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .about p {
            color: #333;
            font-size: 1.2rem;
            line-height: 1.5;
        }

        /* Footer Styling */
        .footer {
            background-color: #004aad;
            color: white;
            padding: 40px 20px;
        }

        .footer a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .footer img {
            width: 24px;
            margin-right: 10px;
        }

        .footer .social-icons {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="assets/images/AU-logo.png" alt="AU Logo">
                Arellano University - Elisa Esguerra Campus
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-login ms-2" href="php/frontend/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-signup ms-2" href="php/frontend/signup.php">Sign Up</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Welcome to the E-Track System!</h1>
            <p>Efficient and secure student transport tracking.</p>
            <a href="php/frontend/signup.php" class="btn btn-warning btn-custom">Get Started</a>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <h2>About Our System</h2>
        <p>The E-Track System is designed to provide secure and efficient student transport tracking. With real-time monitoring and easy access for parents, staff, and students, we aim to enhance safety and reliability in school transportation.</p>
    </section>

    <!-- Footer Section -->
    <footer class="footer py-4">
        <div class="container">
            <div class="row">
                <!-- Left Side: Logo, Address, Contact Info -->
                <div class="col-md-6 d-flex align-items-start">
                    <div class="ms-3">
                        <h2>ARELLANO UNIVERSITY</h2>
                        <p>Gen. Luna corner Esguerra St., Bayan-Bayanan, Malabon City</p>

                        <div class="contact-info">
                            <p>8-932-5209 - Trunkline</p>
                            <p>8-374-5764 - Bursar's Office</p>
                            <p><strong>Email:</strong> <a href="mailto:hs.elisaesguerra@arellano.edu.ph">hs.elisaesguerra@arellano.edu.ph</a></p>
                            <p><strong>Facebook:</strong> <a href="https://facebook.com/ElisaEsguerraCampus" target="_blank">AU - Elisa Esguerra Campus</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>