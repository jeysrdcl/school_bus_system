<?php
session_start();
include '../backend/db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

$show_form = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $_SESSION['error'] = "Please provide an email.";
        $_SESSION['input_value'] = $email;
        header("Location: forgot_password.php");
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT id, email FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_id = $user['id'];

            $token = bin2hex(random_bytes(25));
            $hashed_token = password_hash($token, PASSWORD_DEFAULT);
            $expiry = time() + 300;

            $insert_stmt = $conn->prepare("INSERT INTO password_resets (user_id, email, token, expiry) VALUES (:user_id, :email, :token, :expiry)");
            $insert_stmt->bindParam(':user_id', $user_id);
            $insert_stmt->bindParam(':email', $email);
            $insert_stmt->bindParam(':token', $hashed_token);
            $insert_stmt->bindParam(':expiry', $expiry);

            if ($insert_stmt->execute()) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'etrack.au@gmail.com';
                    $mail->Password = 'wceqbpdoenccpvbw';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('etrack.au@gmail.com', 'E-Track System');
                    $mail->addAddress($email);

                    $resetLink = 'http://localhost/school_bus_system/php/frontend/reset_password.php?email=' . urlencode($email) . '&token=' . urlencode($token);

                    $mail->isHTML(true);
                    $mail->CharSet = "UTF-8";
                    $mail->Subject = 'Password Reset Request';
                    $mail->Body = "
                    <html>
                    <head>
                        <title>Password Reset Request</title>
                        <style>
                            body { 
                                font-family: Arial, sans-serif; 
                                line-height: 1.6; 
                                color: #333; 
                                margin: 0; 
                                padding: 0; 
                            }
                            .container { 
                                width: 100%; 
                                max-width: 600px; 
                                margin: 20px 0; /* Removes auto centering */
                                padding: 20px; 
                                border: 1px solid #ddd; 
                                border-radius: 10px; 
                                background: #f9f9f9;
                                text-align: left; /* Ensures left alignment */
                            }
                            .button-container { margin-top: 20px; }
                            .button {
                                background-color: #007bff; 
                                color: #fff; 
                                padding: 12px 18px; 
                                text-decoration: none; 
                                border-radius: 5px; 
                                font-weight: bold;
                                display: inline-block;
                            }
                            .footer { font-size: 12px; color: #777; margin-top: 20px; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <p>Hello, <strong>Teacher!</strong></p>
                            <p>We received a request to reset your password for your <strong>MonJeep</strong> account. If you made this request, click the button below:</p>
                            
                            <div class='button-container'>
                                <a href='{$resetLink}' class='button' target='_blank'>Reset Password</a>
                            </div>

                            <p>If you did not request this reset, please ignore this email. Your account remains secure.</p>
                            <p>For security reasons, this link will expire in <strong>5 minutes</strong>. If the link expires, you can request a new password reset.</p>
                            <p>Need help? Contact our support team.</p>
                            <p class='footer'>E-Track System Team</p>
                        </div>
                    </body>
                    </html>";

                    $mail->send();
                    $_SESSION['success'] = "Password reset link sent to your email.";
                    $show_form = false;
                } catch (Exception $e) {
                    $_SESSION['error'] = "Email could not be sent.";
                }
            } else {
                $_SESSION['error'] = "Error generating password reset link.";
            }
        } else {
            $_SESSION['error'] = "No account found with that email.";
            $_SESSION['input_value'] = $email;
            header("Location: forgot_password.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    header("Location: forgot_password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
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
        .header {
            font-size: 22px;
            font-weight: bold;
        }
        .form-label {
            font-weight: bold;
        }
        .form-text {
            font-size: 14px;
        }
        .btn-reset {
            width: 100%;
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
            <h2 class="header">Password Reset</h2>
            <p class="form-text">Provide your email to recover your password.</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if ($show_form): ?>
        <form action="forgot_password.php" method="POST">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Enter your email" value="<?= htmlspecialchars($_SESSION['input_value'] ?? '') ?>" required>
            </div>
            <button type="submit" class="btn btn-primary btn-reset">Send Reset Link</button>
        </form>
        <?php endif; ?>
        <div class="text-center mt-3">
            <p>Remember your password? <a href="login.php">Sign in here</a></p>
            <a href="/school_bus_system/index.php" class="text-link"><i class="bi bi-house-door"></i> Home</a>
        </div>
    </div>
</body>
</html>