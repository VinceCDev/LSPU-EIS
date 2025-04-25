<?php
session_start();
require_once '../conn/db_conn.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        header("Location: ../login.php?error=empty_fields");
        exit;
    }

    // Get user from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Check password
        if (password_verify($password, $user['password'])) {
            // Check alumni status if role is alumni
            if ($user['role'] === 'alumni') {
                $user_id = $user['id'];
                $alumni_stmt = $conn->prepare("SELECT status FROM alumni_profile WHERE user_id = ?");
                $alumni_stmt->bind_param("i", $user_id);
                $alumni_stmt->execute();
                $alumni_result = $alumni_stmt->get_result();

                if ($alumni_result->num_rows === 1) {
                    $alumni = $alumni_result->fetch_assoc();
                    if ($alumni['status'] !== 'Approved' && $alumni['status'] !== 'Active') {
                        header("Location: ../login.php?error=not_approved");
                        exit;
                    }
                } else {
                    header("Location: ../login.php?error=alumni_not_found");
                    exit;
                }
            }

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: ../admin_dashboard.php");
            } else {
                header("Location: ../home.php");
            }
            exit;
        } else {
            header("Location: ../login.php?error=invalid_password");
            exit;
        }
    } else {
        header("Location: ../login.php?error=user_not_found");
        exit;
    }
}
