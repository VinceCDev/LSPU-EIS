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

        // Verify password
        if (password_verify($password, $user['password'])) {

            // Check if role is employer
            if ($user['role'] === 'employer') {
                $user_id = $user['id'];

                // Check company status in company_profile
                $status_stmt = $conn->prepare("SELECT status FROM company_profile WHERE user_id = ?");
                $status_stmt->bind_param("i", $user_id);
                $status_stmt->execute();
                $status_result = $status_stmt->get_result();

                if ($status_result->num_rows === 1) {
                    $company = $status_result->fetch_assoc();

                    if ($company['status'] === 'Approved') {
                        // Set session
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['role'] = $user['role'];

                        // Redirect to employer dashboard
                        header("Location: ../employer_dashboard.php");
                        exit;
                    } else {
                        header("Location: ../login.php?error=not_approved");
                        exit;
                    }
                } else {
                    header("Location: ../login.php?error=company_not_found");
                    exit;
                }
            } else {
                header("Location: ../login.php?error=invalid_role");
                exit;
            }
        } else {
            header("Location: ../login.php?error=invalid_password");
            exit;
        }
    } else {
        header("Location: ../login.php?error=user_not_found");
        exit;
    }
}
