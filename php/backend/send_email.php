<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include '../backend/db_connect.php';

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $userEmail = trim($_POST['email']);

        if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email address.";
            exit();
        }

        $stmt = $conn->prepare("SELECT id, role FROM users WHERE email = :email");
        $stmt->bindParam(":email", $userEmail, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo "Email not found.";
            exit();
        }

        $userId = $user['id'];

        $resetToken = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $stmt = $conn->prepare("SELECT id FROM password_resets WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $stmt->execute();
        $existingReset = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingReset) {
            $stmt = $conn->prepare("UPDATE password_resets SET token = :token, expiry = :expiry WHERE user_id = :user_id");
        } else {
            $stmt = $conn->prepare("INSERT INTO password_resets (user_id, email, token, expiry) VALUES (:user_id, :email, :token, :expiry)");
            $stmt->bindParam(":email", $userEmail, PDO::PARAM_STR);
        }

        $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $stmt->bindParam(":token", $resetToken, PDO::PARAM_STR);
        $stmt->bindParam(":expiry", $expiry, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            echo "Failed to store reset token.";
            exit();
        }

        $resetLink = "http://localhost/school_bus_system/php/frontend/reset_password.php?token=" . urlencode($resetToken);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'etrack.au@gmail.com'; 
            $mail->Password = getenv('SMTP_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = 587;
            $mail->setFrom('etrack.au@gmail.com', 'E-Track System');
            $mail->addAddress($userEmail);

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

            if ($mail->send()) {
                header('Location: forgot_password.php?success=1');
                exit();
            } else {
                echo "Failed to send email.";
            }

        } catch (Exception $e) {
            error_log("Email sending error: " . $mail->ErrorInfo);
            echo "Email could not be sent. Please try again later.";
        }
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    echo "An error occurred. Please try again.";
}
?>