<?php
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($argv[1])) {
    $emailData = json_decode(base64_decode($argv[1]), true);
    
    $email = $emailData['email'];
    $secondary_email = $emailData['secondary_email'];
    $first_name = $emailData['first_name'];
    $last_name = $emailData['last_name'];
    $random_password = $emailData['random_password'];

    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'allencristal12@gmail.com';
        $mail->Password = 'ugwb vksz wjto zbwf';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        $mail->setFrom('allencristal12@gmail.com', 'LSPU EIS System');
        $mail->addAddress($email, $first_name.' '.$last_name);
        
        if ($secondary_email) {
            $mail->addAddress($secondary_email, $first_name.' '.$last_name);
        }
        
        $mail->isHTML(true);
        $mail->Subject = 'Your LSPU EIS Alumni Account (Auto-Approved)';
        $mail->Body = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Account Approved</title>
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
                    <h2>Welcome, '.htmlspecialchars($first_name).'!</h2>
                    <p>Your alumni account has been <strong>created and approved</strong> by the LSPU EIS Administrator.</p>
                    <div class="highlight">
                        <h3>🔑 Login Credentials</h3>
                        <p><strong>Email:</strong> '.htmlspecialchars($email).'</p>
                        <p><strong>Password:</strong> '.$random_password.'</p>
                        <p><strong>Login link:</strong> <a href="http://localhost/lspu_eis/login">http://localhost/lspu_eis/login</a></p>
                    </div>
                    <p><strong>Next Steps:</strong></p>
                    <ul>
                        <li>Login and change your password immediately for security.</li>
                        <li>Complete your profile and upload your resume.</li>
                        <li>Browse and apply for job opportunities.</li>
                    </ul>
                    <p>If you have any questions or need assistance, please contact the LSPU EIS support team.</p>
                    <p>Best regards,<br>
                    <strong>LSPU EIS Team</strong></p>
                </div>
                <div class="footer" style="text-align: center;">
                    <p>This is an automated message from the LSPU Employment Information System.</p>
                    <p>© 2024 Laguna State Polytechnic University. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
        
        $mail->AltBody = "Welcome, $first_name!\n\nYour alumni account has been created and approved.\n\nLogin: http://localhost/lspu_eis/login.php\nEmail: $email\nPassword: $random_password\n\nBest regards,\nLSPU EIS Team";
        
        $mail->send();
        file_put_contents(__DIR__ . '/email_log.txt', date('Y-m-d H:i:s') . " - Email sent to: $email\n", FILE_APPEND);
        
    } catch (Exception $e) {
        file_put_contents(__DIR__ . '/email_errors.txt', date('Y-m-d H:i:s') . " - Error sending to $email: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}