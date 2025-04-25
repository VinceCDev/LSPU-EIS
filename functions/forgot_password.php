<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

require_once '../conn/db_conn.php'; // Make sure $pdo is your PDO connection
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a unique token and expiration time
        $token = bin2hex(random_bytes(32));
        $token_expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // OPTIONAL: Add columns to your database if they don’t exist
        // ALTER TABLE `users` ADD `reset_token` VARCHAR(64) NULL, ADD `token_expiry` DATETIME NULL;

        // Save the token and expiry in the database
        $updateStmt = $pdo->prepare("UPDATE users SET reset_token = :token, token_expiry = :expiry WHERE email = :email");
        $updateStmt->execute([
            'token' => $token,
            'expiry' => $token_expiry,
            'email' => $email
        ]);

        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'allencristal23@gmail.com'; // Your Gmail
            $mail->Password   = 'ydvrnivcnfxonxkf';          // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('allencristal23@gmail.com', 'LSPU EIS');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body    = "Click the link below to reset your password:<br>
                <a href='http://yourdomain.com/reset_password.php?email=" . urlencode($email) . "&token=$token'>
                Reset Password</a><br><small>This link will expire in 1 hour.</small>";

            $mail->send();
            $_SESSION['message'] = '<div class="alert alert-success">Password reset email sent successfully.</div>';
        } catch (Exception $e) {
            $_SESSION['message'] = '<div class="alert alert-danger">Mailer Error: ' . $mail->ErrorInfo . '</div>';
        }
    } else {
        $_SESSION['message'] = '<div class="alert alert-warning">Email address not found.</div>';
    }

    header("Location: ../forgot_password.php");
    exit;
}
