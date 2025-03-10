<?php
include '../backend/signup_process.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Randomized signup messages
$signup_messages = [
    "Fill the form below to create your account",
    "Let's get you started! Sign up now 🚀",
    "Welcome! Create your account in a few steps",
    "Join us today! Just a few details needed",
    "Almost there! Complete the form to sign up",
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
            height: 90vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.10);
        }
        .container {
            max-width: 550px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 0px 12px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }
        .logo {
            display: block;
            margin: 0 auto;
            width: 90px;
        }
        .signup-header {
            font-size: 35px;
            font-weight: bold;
        }
        .signup-description {
            font-size: 20px;
        }
        .input-group {
            position: relative;
        }
        .input-group .btn {
            border-radius: 0;
            border-left: none;
        }
        .form-label {
            font-weight: bold;
            text-align: left;
            display: block;
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
        <div class="container mt-5">
            <div class="text-center">
                <img src="../../assets/images/AU-logo.png" alt="AU Logo" width="150">
                <h2 class="signup-header">Create an Account</h2>
                <h2 class="signup-description"><?php echo $random_signup_message; ?></h2>
            </div>

        <!-- Show success/error messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="../backend/signup_process.php" method="POST" onsubmit="return validateForm()">
            <div class="mb-3">
                <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone Number" required>
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
                    <option value="driver">Driver</option>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Sign Up</button>
        </form>        
            <div class="text-center mt-3">
                <p>Already have an account? <a href="login.php">Log in</a></p>
                <a href="/school_bus_system/index.php" class="text-link"><i class="bi bi-house-door"></i> Home</a>
            </div>
    </body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const phoneInput = document.getElementById("phone");

        phoneInput.addEventListener("focus", function() {
            if (!phoneInput.value.startsWith("+63 ")) {
                phoneInput.value = "+63 ";
            }
        });

        phoneInput.addEventListener("input", function() {
            let rawValue = phoneInput.value.replace(/\D/g, "");

            if (rawValue.startsWith("63")) {
                rawValue = rawValue.substring(2);
            }

            let formattedValue = "+63 ";
            if (rawValue.length > 0) {
                formattedValue += rawValue.substring(0, 3);
            }
            if (rawValue.length > 3) {
                formattedValue += " " + rawValue.substring(3, 6);
            }
            if (rawValue.length > 6) {
                formattedValue += " " + rawValue.substring(6, 10);
            }

            phoneInput.value = formattedValue.trim();
        });

        phoneInput.addEventListener("keydown", function(event) {
            if ((event.key === "Backspace" || event.key === "Delete") && phoneInput.value.length <= 4) {
                event.preventDefault();
            }
        });

        phoneInput.addEventListener("blur", function() {
            if (phoneInput.value.trim() === "+63") {
                phoneInput.value = "";
            }
        });
    });

    function validateForm() {
        var phone = document.getElementById("phone").value;
        var password = document.getElementById("password").value;
        var passwordError = document.getElementById("passwordError");

        var phoneRegex = /^\+63 \d{3} \d{3} \d{4}$/;
        var passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d@#$%^&+=!*]*$/; 

        if (!phoneRegex.test(phone)) {
            alert("Invalid phone number. Please use: +63 123 456 7890");
            return false;
        }

        if (!passwordRegex.test(password) || password.length < 8) {
            passwordError.innerText = "Password must meet the required criteria.";
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