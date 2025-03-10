<?php
session_start();
include 'db_connect.php'; // Ensure database connection

if (!isset($conn)) {
    die("Database connection not established.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input
    $full_name = isset($_POST['full_name']) ? htmlspecialchars(trim($_POST['full_name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $password = isset($_POST['password']) ? htmlspecialchars(trim($_POST['password'])) : '';
    $role = isset($_POST['role']) ? htmlspecialchars(trim($_POST['role'])) : '';

    // Remove spaces and non-numeric characters
    $phone = preg_replace("/[^0-9]/", "", $phone);

    // Convert international format (63XXXXXXXXXX) to local format (0XXXXXXXXXX)
    if (substr($phone, 0, 2) === "63") {
        $phone = "0" . substr($phone, 2);
    }

    // Ensure phone number follows "0XXX XXX XXXX" format
    if (strlen($phone) === 11) {
        $phone = preg_replace("/(\d{4})(\d{3})(\d{4})/", "$1 $2 $3", $phone);
    }

    // Check for empty fields
    if (empty($full_name) || empty($email) || empty($phone) || empty($password) || empty($role)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: ../frontend/signup.php");
        exit();
    }

    try {
        // Check if email already exists
        $check_email_query = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($check_email_query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Email already registered! Please use a different email.";
            header("Location: ../frontend/signup.php");
            exit();
        }

        // Check if phone number already exists
        $check_phone_query = "SELECT * FROM users WHERE phone = :phone";
        $stmt = $conn->prepare($check_phone_query);
        $stmt->bindParam(":phone", $phone);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Phone number is already registered!";
            header("Location: ../frontend/signup.php");
            exit();
        }

        // Password validation on server side
        if (strlen($password) < 8) {
            $_SESSION['error'] = "Password must be at least 8 characters long.";
            header("Location: ../frontend/signup.php");
            exit();
        }

        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $insert_query = "INSERT INTO users (full_name, email, phone, password, role) 
                         VALUES (:full_name, :email, :phone, :password, :role)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bindParam(":full_name", $full_name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
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