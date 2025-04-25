<?php
session_start();
require_once '../conn/db_conn.php'; // Include your database connection

// Generate CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Create a random token
}

// Check CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect form data
    $username = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        echo "Please enter both username and password.";
        exit;
    }

    try {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role']; // Store role

                // Redirect based on role
                if ($user['role'] === 'employer') {
                    header("Location: ../employer_dashboard.php");
                }
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "Username not found.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
