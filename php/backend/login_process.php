<?php
session_start();
include '../backend/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: ../frontend/login.php");
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_id = $user['id'];
            $hashed_password = $user['password'];
            $role = $user['role'];

            if (password_verify($password, $hashed_password)) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $role;

                if (isset($_POST['remember_me'])) {
                    setcookie("user_email", $email, time() + (86400 * 30), "/", "", true, true);
                }

                switch ($role) {
                    case 'admin':
                        header("Location: ../../dashboard/admin/admin_dashboard.php");
                        break;
                    case 'student':
                    case 'teacher':
                    case 'driver':
                        header("Location: ../../dashboard/dashboard.php");
                        break;
                    default:
                        $_SESSION['error'] = "Unauthorized access!";
                        header("Location: ../frontend/login.php");
                        exit();
                }
            } else {
                $_SESSION['error'] = "Incorrect password!";
                header("Location: ../frontend/login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "No account found with that email!";
            header("Location: ../frontend/login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: ../frontend/login.php");
        exit();
    }
}
header("Location: ../frontend/login.php");
exit();