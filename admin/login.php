<?php
session_start();
require_once 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL Injection
    $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify hashed password
        if (password_verify($password, $row['password'])) {
            // Login successful
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $row['username'];
            $_SESSION['admin_id'] = $row['id'];
            
            header("Location: /admin/dashboard.php");
            exit;
        } else {
            // Incorrect password
            $_SESSION['login_error'] = "Invalid username or password.";
            header("Location: /admin/index.php");
            exit;
        }
    } else {
        // User not found
        $_SESSION['login_error'] = "Invalid username or password.";
        header("Location: /admin/index.php");
        exit;
    }
    
    $stmt->close();
} else {
    // Redirect to login page if accessed directly
    header("Location: /admin/index.php");
    exit;
}
?>
