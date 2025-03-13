<?php
session_start();
include 'db_connect.php';

if (!isset($conn)) {
    die("Database connection not established.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : '';
    $password = isset($_POST['password']) ? trim(htmlspecialchars($_POST['password'])) : '';
    $role = isset($_POST['role']) ? trim(htmlspecialchars($_POST['role'])) : '';

    if ($email === '' || $password === '' || $role === '') {
        $_SESSION['error'] = "All fields are required!";
        header("Location: ../frontend/signup.php");
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Email already registered! Please use a different email.";
            header("Location: ../frontend/signup.php");
            exit();
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = "Password must be at least 8 characters long.";
            header("Location: ../frontend/signup.php");
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (email, password, role) 
                                VALUES (:email, :password, :role)");
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":role", $role);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Account created successfully! You can now log in.";
        } else {
            $_SESSION['error'] = "Signup failed. Please try again.";
        }

        header("Location: ../frontend/signup.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: ../frontend/signup.php");
        exit();
    }
}
?>