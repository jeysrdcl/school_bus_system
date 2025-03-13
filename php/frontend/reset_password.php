<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../backend/db_connect.php";

// Validate token and email parameters
if (!isset($_GET['token']) || empty($_GET['token']) || !isset($_GET['email']) || empty($_GET['email'])) {
    $_SESSION['error'] = "Invalid or expired token, please request a new one.";
    header("Location: forgot_password.php");
    exit();
}

$token = trim($_GET['token']);
$email = trim($_GET['email']);

// Debug: Log token and email from URL
error_log("Token from URL: " . $token);
error_log("Email from URL: " . $email);

// Fetch token details from the database
$stmt = $conn->prepare("
    SELECT pr.token, pr.expiry 
    FROM password_resets pr
    INNER JOIN users u ON pr.email = u.email
    WHERE u.email = :email
");
$stmt->bindParam(":email", $email, PDO::PARAM_STR);
$stmt->execute();
$reset = $stmt->fetch(PDO::FETCH_ASSOC);

// Debug: Log fetched reset data
error_log("Fetched reset data: " . print_r($reset, true));

// Verify token using password_verify()
if (!$reset || !password_verify($token, $reset['token'])) {
    error_log("Token verification failed. Token from URL: " . $token . ", Hashed token from DB: " . $reset['token']);

    // Delete the invalid token
    $delete_token = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
    $delete_token->bindParam(':token', $reset['token'], PDO::PARAM_STR);
    $delete_token->execute();

    $_SESSION['error'] = "Invalid or expired token, please request a new one.";
    header("Location: forgot_password.php");
    exit();
}

// Check if the token has expired
if (strtotime($reset['expiry']) < time()) {
    error_log("Token expired. Expiry: " . $reset['expiry'] . ", Current time: " . time());

    // Delete the expired token
    $delete_token = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
    $delete_token->bindParam(':token', $reset['token'], PDO::PARAM_STR);
    $delete_token->execute();

    $_SESSION['error'] = "Your password reset link has expired. Please request a new one.";
    header("Location: forgot_password.php");
    exit();
}

// Generate CSRF token for form submission
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle password reset form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid CSRF token.";
        header("Location: reset_password.php?token=" . urlencode($token) . "&email=" . urlencode($email));
        exit();
    }

    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validate password fields
    if (empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $_SESSION['error'] = "Password must be at least 8 characters.";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $update = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
        $update->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $update->bindParam(':email', $email, PDO::PARAM_STR);
        $update->execute();

        if ($update->rowCount() > 0) {
            // Delete the used token
            $delete_token = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
            $delete_token->bindParam(':token', $reset['token'], PDO::PARAM_STR);
            $delete_token->execute();

            // Set success message and redirect to login.php
            $_SESSION['success'] = "Your password has been reset successfully!";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to update password. Try again.";
        }
    }

    // Redirect back to the reset password page with error messages
    header("Location: reset_password.php?token=" . urlencode($token) . "&email=" . urlencode($email));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
        .form-label {
            font-weight: bold;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .text-link {
            color: #0d6efd;
            text-decoration: none;
        }
        .text-link:hover {
            text-decoration: underline;
        }
        .input-group .btn {
            border-radius: 0;
            border-left: none;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="container">
        <div class="text-center">
            <img src="../../assets/images/AU-logo.png" alt="Arellano University Logo" width="115">
            <h3 class="mt-3 fw-bold">Reset Your Password</h3>
            <p class="form-text">Please create a new password.</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <div class="mb-3 input-group">
                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Enter new password" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('new_password', 'eyeIconNew')">
                    <i id="eyeIconNew" class="bi bi-eye"></i>
                </button>
            </div>

            <div class="mb-3 input-group">
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm new password" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password', 'eyeIconConfirm')">
                    <i id="eyeIconConfirm" class="bi bi-eye"></i>
                </button>
            </div>

            <button type="submit" class="btn btn-dark w-100 mt-4 fw-bold">Reset Password</button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php" class="text-link"><i class="bi bi-arrow-left"></i> Back to Login</a>
        </div>
    </div>

    <script>
        // JavaScript function to toggle password visibility
        function togglePassword(inputId, eyeIconId) {
            const input = document.getElementById(inputId);
            const eyeIcon = document.getElementById(eyeIconId);

            if (input.type === "password") {
                input.type = "text";
                eyeIcon.classList.replace("bi-eye", "bi-eye-slash");
            } else {
                input.type = "password";
                eyeIcon.classList.replace("bi-eye-slash", "bi-eye");
            }
        }
    </script>
</body>
</html>