<?php
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
$alumni_id = $input['alumni_id'] ?? null;

if ($alumni_id) {
    $db = Database::getInstance()->getConnection();
    
    // Get alumni and user data
    $stmt = $db->prepare("SELECT a.*, u.email, u.secondary_email, u.user_id 
                         FROM alumni a 
                         JOIN user u ON a.user_id = u.user_id 
                         WHERE a.alumni_id = ?");
    $stmt->bind_param("i", $alumni_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $alumni_data = $result->fetch_assoc();
    $stmt->close();

    if ($alumni_data) {
        // Update user status to active
        $stmt = $db->prepare("UPDATE user SET status = 'Active' WHERE user_id = ?");
        $stmt->bind_param("i", $alumni_data['user_id']);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            // Send emails to both primary and secondary email addresses
            $emails_sent = [];
            
            // Send to primary email
            if ($alumni_data['email']) {
                $email_sent = sendApprovalEmail($alumni_data['email'], $alumni_data['first_name'] . ' ' . $alumni_data['last_name']);
                $emails_sent[] = ['email' => $alumni_data['email'], 'sent' => $email_sent];
            }
            
            // Send to secondary email if exists
            if ($alumni_data['secondary_email']) {
                $email_sent = sendApprovalEmail($alumni_data['secondary_email'], $alumni_data['first_name'] . ' ' . $alumni_data['last_name']);
                $emails_sent[] = ['email' => $alumni_data['secondary_email'], 'sent' => $email_sent];
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Alumni approved successfully',
                'emails_sent' => $emails_sent
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Alumni not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No alumni_id provided']);
}

function sendApprovalEmail($email, $name) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lspueis@gmail.com'; // Change to your email
        $mail->Password = 'afbp fcwf oujr yqzr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('lspueis@gmail.com', 'LSPU EIS System');
        $mail->addAddress($email, $name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Account Approved - Welcome to LSPU EIS!';
        
        // HTML Email Template
        $htmlBody = '
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
                    <h2>Congratulations, ' . htmlspecialchars($name) . '!</h2>
                    <p>Your account has been <strong>approved</strong> by the LSPU EIS Administrator.</p>
                    
                    <div class="highlight">
                        <h3>🚀 What\'s Next?</h3>
                        <p>You can now access your account and start exploring job opportunities, updating your profile, and connecting with potential employers.</p>
                    </div>
                    
                    <p><strong>Login to your account:</strong></p>
                    <a href="http://localhost/lspu_eis/login.php" class="button">Login to LSPU EIS</a>
                    
                    <h3>📋 Important Reminders:</h3>
                    <ul>
                        <li>Complete your profile with updated information</li>
                        <li>Upload your latest resume</li>
                        <li>Add your work experience and education details</li>
                        <li>Browse and apply for available job positions</li>
                        <li>Keep your contact information updated</li>
                    </ul>
                    
                    <div class="highlight">
                        <h3>📧 Email Reminders</h3>
                        <p>You will receive periodic email reminders to:</p>
                        <ul>
                            <li>Update your profile information</li>
                            <li>Check for new job opportunities</li>
                            <li>Maintain active account status</li>
                        </ul>
                    </div>
                    
                    <p>If you have any questions or need assistance, please contact the LSPU EIS support team.</p>
                    
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
        
        $mail->Body = $htmlBody;
        
        // Plain text version
        $mail->AltBody = "Congratulations $name!\n\nYour account has been approved by the LSPU EIS Administrator.\n\nYou can now login to your account at: http://localhost/lspu_eis/login.php\n\nPlease complete your profile and start exploring job opportunities.\n\nBest regards,\nLSPU EIS Team";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}
