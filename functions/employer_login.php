<?php
session_start();
require_once '../conn/db_conn.php'; // Include your database connection

// Use singleton database connection
$db = Database::getInstance()->getConnection();

// Generate CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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

    // Using MySQLi
    $stmt = $db->prepare("SELECT * FROM user WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username); // "s" means string
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, check user_role
                if ($user['user_role'] === 'employer') {
                    // Set session variables for employer
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['user_role'] = $user['user_role'];
                    // Redirect to employer dashboard
                    header("Location: ../employer_dashboard.php");
                    exit;
                } else {
                    // User is not an employer
                    echo "Access denied. Only employers can log in here.";
                    exit;
                }
            } else {
                echo "Invalid password.";
                exit;
            }
        } else {
            echo "Username not found.";
            exit;
        }
        $stmt->close();
    } else {
        echo "Error in SQL statement: " . $db->error;
    }
}
