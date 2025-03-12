<?php
include '../backend/signup_process.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(0);

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$signup_messages = [
    "Fill the form below to create your account",
    "Let's get you started! Sign up now 🚀",
    "Welcome! Create your account in a few steps",
    "Join us today! Just a few details needed",
    "Start your journey with us! Sign up now"
];
$random_signup_message = $signup_messages[array_rand($signup_messages)];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
       body {
            background: url('../../assets/images/AU-EEC.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            position: relative;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.1);
        }
        .container {
            width: 500px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            z-index: 1;
        }
        .logo {
            display: block;
            margin: 0 auto;
            width: 90px;
        }
        .signup-header {
            font-size: 28px;
            font-weight: bold;
        }
        .signup-description {
            font-size: 18px;
        }
        .input-group .btn {
            border-radius: 0;
            border-left: none;
        }
        .text-link {
            color: #0d6efd;
            text-decoration: none;
        }
        .text-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="container">
        <div class="text-center">
            <img src="../../assets/images/AU-logo.png" alt="AU Logo" class="logo">
            <h2 class="signup-header">Create an Account</h2>
            <h2 class="signup-description"><?php echo $random_signup_message; ?></h2>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="../backend/signup_process.php" method="POST" onsubmit="return validateForm()">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3 input-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required oninput="validatePassword()">
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                    <i id="eyeIcon" class="bi bi-eye"></i>
                </button>
            </div>
            <small id="passwordError" class="text-danger"></small>

            <div class="mb-3">
                <label for="role" class="form-label">Select Role</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="" disabled selected>Select your role</option>
                    <option value="teacher">Teacher</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Sign Up</button>
        </form>        
        <div class="text-center mt-3">
            <p>Already have an account? <a href="login.php">Log in</a></p>
            <a href="/school_bus_system/index.php" class="text-link"><i class="bi bi-house-door"></i> Home</a>
        </div>
    </div>
</body>
<script>
    function validateForm() {
        var password = document.getElementById("password").value;
        var passwordError = document.getElementById("passwordError");

        var passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/; 

        if (!passwordRegex.test(password)) {
            passwordError.innerText = "Password must be at least 8 characters and include letters & numbers.";
            return false;
        }
        return true;
    }
    function togglePassword() {
        var passwordField = document.getElementById("password");
        var eyeIcon = document.getElementById("eyeIcon");

        if (passwordField.type === "password") {
            passwordField.type = "text";
            eyeIcon.classList.replace("bi-eye", "bi-eye-slash");
        } else {
            passwordField.type = "password";
            eyeIcon.classList.replace("bi-eye-slash", "bi-eye");
        }
    }
</script>
</html>