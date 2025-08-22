<?php
session_start();
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'], $_POST['email'], $_POST['password'], $_POST['password2'])) {
    $token = $_POST['token'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    if ($password !== $password2) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        exit;
    }
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT user_id, reset_token, reset_token_expiry FROM user WHERE (email = ? OR secondary_email = ?) AND reset_token = ? LIMIT 1");
    $stmt->bind_param('sss', $email, $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    if ($user && $user['reset_token'] && strtotime($user['reset_token_expiry']) > time()) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE user SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE user_id = ?");
        $stmt->bind_param('si', $hashed, $user['user_id']);
        $stmt->execute();
        $stmt->close();
        // Set session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $email;
        // Insert notification for password change
        $stmt_notif = $db->prepare('INSERT INTO notifications (user_id, type, message, details) VALUES (?, ?, ?, ?)');
        $notif_type = 'password';
        $notif_message = 'Your password was changed.';
        $notif_details = 'If you did not perform this action, please contact support.';
        $stmt_notif->bind_param('isss', $user['user_id'], $notif_type, $notif_message, $notif_details);
        $stmt_notif->execute();
        $stmt_notif->close();
        // Send confirmation email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'allencristal12@gmail.com';
            $mail->Password = 'ugwb vksz wjto zbwf';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('allencristal12@gmail.com', 'LSPU EIS');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your LSPU EIS Password Was Reset';
            $login_link = 'http://localhost/lspu_eis/login.php';
            $mail->Body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Password Reset Confirmation</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #00A0E9 0%, #1A1A1A 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                    .button { display: inline-block; background: #00A0E9; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                    .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                    .highlight { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>🎓 LSPU EIS</h1>
                        <p>Laguna State Polytechnic University</p>
                        <p>Employment Information System</p>
                    </div>
                    <div class="content">
                        <h2>Password Successfully Changed</h2>
                        <p>Your password has been <strong>successfully changed</strong>. You can now log in to your account using your new password.</p>
                        <a href="' . $login_link . '" class="button">Login to LSPU EIS</a>
                        <div class="highlight">
                            <strong>If you did not perform this action, please contact support immediately.</strong>
                        </div>
                        <p>Best regards,<br>
                        <strong>LSPU EIS Team</strong></p>
                    </div>
                    <div class="footer">
                        <p>This is an automated message from the LSPU Employment Information System.</p>
                        <p>© 2024 Laguna State Polytechnic University. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>';
            $mail->AltBody = "Your password was reset. If you did not perform this, contact support.";
            $mail->send();
        } catch (Exception $e) {}
        echo json_encode(['success' => true, 'message' => 'Password successfully changed.']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token.']);
        exit;
    }
}
echo json_encode(['success' => false, 'message' => 'Invalid request.']); 