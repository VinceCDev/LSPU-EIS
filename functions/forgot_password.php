<?php
session_start();
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT user_id, email, secondary_email FROM user WHERE email = ? OR secondary_email = ? LIMIT 1");
    $stmt->bind_param('ss', $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $stmt = $db->prepare("UPDATE user SET reset_token = ?, reset_token_expiry = ? WHERE user_id = ?");
        $stmt->bind_param('ssi', $token, $expiry, $user['user_id']);
        $stmt->execute();
        $stmt->close();
        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'lspueis@gmail.com';
            $mail->Password = 'afbp fcwf oujr yqzr';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('lspueis@gmail.com', 'LSPU EIS');
            $mail->addAddress($user['email']);
            if (!empty($user['secondary_email'])) {
                $mail->addAddress($user['secondary_email']);
            }
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your LSPU EIS Password';
            $reset_link = 'http://localhost/lspu_eis/reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($user['email']);
            $mail->Body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Reset Password</title>
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
                        <h2>Password Reset Request</h2>
                        <p>We received a request to reset your password. Click the button below to set a new password. This link will expire in 1 hour.</p>
                        <a href="' . $reset_link . '" class="button">Reset Password</a>
                        <div class="highlight">
                            <strong>If you did not request this, you can safely ignore this email.</strong>
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
            $mail->AltBody = "Reset your password: $reset_link\nIf you did not request this, ignore this email.";
            $mail->send();
            echo json_encode(['success' => true, 'message' => 'Password reset email sent successfully.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email address not found.']);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Invalid request.']);
