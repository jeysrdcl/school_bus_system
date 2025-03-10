<?php
session_start();
include '../backend/db_connect.php'; // Ensure this file contains your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: ../frontend/login.php"); // ✅ Corrected path
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_id = $user['id'];
            $full_name = $user['full_name'];
            $hashed_password = $user['password'];
            $role = $user['role']; // Fetch the role from the database

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['full_name'] = $full_name;
                $_SESSION['role'] = $role; // Store the role in the session

                // Remember Me functionality
                if (isset($_POST['remember_me'])) {
                    setcookie("user_email", $email, time() + (86400 * 30), "/"); // 30 days
                }

                // Redirect user based on their role
                if ($role == 'admin') {
                    header("Location: ../../dashboard/admin/admin_dashboard.php");
                } elseif (in_array($role, ['student', 'teacher', 'driver'])) {
                    header("Location: ../../dashboard/dashboard.php");
                } else {
                    $_SESSION['error'] = "Unauthorized role!";
                    header("Location: ../frontend/login.php"); // ✅ Corrected path
                }
                exit();
            } else {
                $_SESSION['error'] = "Incorrect password!";
                header("Location: ../frontend/login.php"); // ✅ Corrected path
                exit();
            }
        } else {
            $_SESSION['error'] = "No account found with that email!";
            header("Location: ../frontend/login.php"); // ✅ Corrected path
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database query failed: " . $e->getMessage();
        header("Location: ../frontend/login.php"); // ✅ Corrected path
        exit();
    }
}

header("Location: ../frontend/login.php"); // ✅ Corrected path
exit();