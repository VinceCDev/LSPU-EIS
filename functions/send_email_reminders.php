<?php
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Function to send reminder emails
function sendReminderEmail($email, $name) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'allencristal12@gmail.com'; // Change to your email
        $mail->Password = 'ugwb vksz wjto zbwf'; // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('allencristal12@gmail.com', 'LSPU EIS System');
        $mail->addAddress($email, $name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Reminder: Update Your LSPU EIS Profile';
        
        // HTML Email Template
        $htmlBody = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Profile Update Reminder</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #00A0E9 0%, #1A1A1A 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: #00A0E9; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                .highlight { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .urgent { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0; }
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
                    <h2>Hello, ' . htmlspecialchars($name) . '!</h2>
                    <p>This is a friendly reminder to keep your LSPU EIS profile updated and active.</p>
                    
                    <div class="highlight">
                        <h3>📋 Profile Update Checklist</h3>
                        <p>Please review and update the following information:</p>
                        <ul>
                            <li>✅ Current contact information</li>
                            <li>✅ Latest work experience</li>
                            <li>✅ Educational background</li>
                            <li>✅ Skills and certifications</li>
                            <li>✅ Recent resume upload</li>
                            <li>✅ Profile picture</li>
                        </ul>
                    </div>
                    
                    <div class="urgent">
                        <h3>🚨 Important Notice</h3>
                        <p>Keeping your profile updated increases your chances of being matched with relevant job opportunities and helps employers find you more easily.</p>
                    </div>
                    
                    <p><strong>Access your profile:</strong></p>
                    <a href="http://localhost/lspu_eis/login.php" class="button">Login & Update Profile</a>
                    
                    <h3>💼 Job Opportunities</h3>
                    <p>New job positions are regularly posted on the LSPU EIS platform. Make sure to:</p>
                    <ul>
                        <li>Check for new job postings regularly</li>
                        <li>Apply to positions that match your skills</li>
                        <li>Keep your application status updated</li>
                        <li>Respond to employer inquiries promptly</li>
                    </ul>
                    
                    <p>If you have any questions or need assistance updating your profile, please contact the LSPU EIS support team.</p>
                    
                    <p>Best regards,<br>
                    <strong>LSPU EIS Team</strong></p>
                </div>
                <div class="footer">
                    <p>This is an automated reminder from the LSPU Employment Information System.</p>
                    <p>© 2024 Laguna State Polytechnic University. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
        
        $mail->Body = $htmlBody;
        
        // Plain text version
        $mail->AltBody = "Hello $name!\n\nThis is a friendly reminder to keep your LSPU EIS profile updated and active.\n\nPlease review and update your contact information, work experience, educational background, skills, and resume.\n\nLogin to update your profile: http://localhost/lspu_eis/login.php\n\nBest regards,\nLSPU EIS Team";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Reminder email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

// Get all active alumni for reminders
$db = Database::getInstance()->getConnection();
$sql = "SELECT a.alumni_id, a.first_name, a.last_name, u.email, u.secondary_email
        FROM alumni a
        JOIN user u ON a.user_id = u.user_id
        WHERE u.status = 'active'";
$result = $db->query($sql);

$emails_sent = [];
$total_sent = 0;

while ($row = $result->fetch_assoc()) {
    $name = $row['first_name'] . ' ' . $row['last_name'];
    
    // Send to primary email
    if ($row['email']) {
        $sent = sendReminderEmail($row['email'], $name);
        $emails_sent[] = [
            'email' => $row['email'],
            'name' => $name,
            'sent' => $sent
        ];
        if ($sent) $total_sent++;
    }
    
    // Send to secondary email if exists
    if ($row['secondary_email']) {
        $sent = sendReminderEmail($row['secondary_email'], $name);
        $emails_sent[] = [
            'email' => $row['secondary_email'],
            'name' => $name,
            'sent' => $sent
        ];
        if ($sent) $total_sent++;
    }
}

echo json_encode([
    'success' => true,
    'message' => "Reminder emails sent successfully",
    'total_sent' => $total_sent,
    'emails_sent' => $emails_sent
]); 