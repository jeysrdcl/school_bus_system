<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include '../backend/db_connect.php'; // Ensure database connection

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $userEmail = trim($_POST['email']);

        // Check if email exists in database
        $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE email = :email");
        $stmt->bindParam(":email", $userEmail, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo "Email not found.";
            exit();
        }

        $full_name = htmlspecialchars($user['full_name']); // Prevent XSS injection
        $userId = $user['id']; // Store user ID for future use

        // Generate a secure token
        $resetToken = bin2hex(random_bytes(32));
        $expiry = time() + 300; // Expiry time: 5 minutes (300 seconds)

        // Check if a reset token already exists for the user
        $stmt = $conn->prepare("SELECT id FROM password_resets WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $stmt->execute();
        $existingReset = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingReset) {
            // Update existing token
            $stmt = $conn->prepare("UPDATE password_resets SET token = :token, expiry = :expiry WHERE user_id = :user_id");
        } else {
            // Insert new token
            $stmt = $conn->prepare("INSERT INTO password_resets (user_id, email, token, expiry) VALUES (:user_id, :email, :token, :expiry)");
            $stmt->bindParam(":email", $userEmail, PDO::PARAM_STR);
        }

        $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $stmt->bindParam(":token", $resetToken, PDO::PARAM_STR);
        $stmt->bindParam(":expiry", $expiry, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            echo "Failed to store reset token.";
            exit();
        }

        $resetLink = "http://localhost/school_bus_system/php/frontend/reset_password.php?token=" . urlencode($resetToken);

        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'etrack.au@gmail.com'; 
            $mail->Password = getenv('SMTP_PASSWORD'); // Store password in an environment variable
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = 587;
            $mail->setFrom('etrack.au@gmail.com', 'E-Track System');
            $mail->addAddress($userEmail);

            // Email Content
            $mail->isHTML(true);
            $mail->CharSet = "UTF-8";
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
            <html>
            <head>
                <title>Password Reset Request</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px; }
                    .container { 
                        max-width: 600px; 
                        margin: 0; 
                        padding: 20px; 
                        border: 1px solid #ddd; 
                        border-radius: 10px; 
                        background: #f9f9f9; 
                    }
                    .button-container { text-align: center; margin-top: 20px; }
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
                    <p>Hello <strong>{$full_name}</strong>,</p>
                    <p>We received a request to reset your password for your <strong>E-Track System</strong> account. If you made this request, click the button below:</p>
                    
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

            $mail->AltBody = "Hello {$full_name},\n\n";
            $mail->AltBody .= "We received a request to reset your password for your E-Track System account.\n";
            $mail->AltBody .= "Click the link below to reset your password:\n\n";
            $mail->AltBody .= "{$resetLink}\n\n";
            $mail->AltBody .= "For security reasons, this link will expire in 5 minutes. If the link expires, you can request a new password reset.\n\n";
            $mail->AltBody .= "If you did not request this, ignore this email.";                

            // Send Email
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