<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$application_id = $data['application_id'] ?? null;
$status = $data['status'] ?? null;

if (!$application_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing application_id or status.']);
    exit();
}

$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];
// Get employer user_id
$stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($employer_id);
$stmt->fetch();
$stmt->close();
if (!$employer_id) {
    echo json_encode(['success' => false, 'message' => 'Employer not found.']);
    exit();
}
// Ensure these variables are always defined
$company_name = '';
$contact_email = '';
$contact_number = '';
// Get employer contact details
$stmt = $db->prepare('SELECT e.company_name, e.contact_email, e.contact_number FROM employer e JOIN user u ON e.user_id = u.user_id WHERE u.email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($company_name, $contact_email, $contact_number);
$stmt->fetch();
$stmt->close();
// Get application details and check if this employer owns the job
$stmt = $db->prepare('SELECT app.alumni_id, app.job_id, j.employer_id FROM applications app JOIN jobs j ON app.job_id = j.job_id WHERE app.application_id = ? LIMIT 1');
$stmt->bind_param('i', $application_id);
$stmt->execute();
$stmt->bind_result($alumni_id, $job_id, $job_employer_id);
$stmt->fetch();
$stmt->close();
if ($job_employer_id != $employer_id) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to update this application.']);
    exit();
}
// Update status
$stmt = $db->prepare('UPDATE applications SET status = ? WHERE application_id = ?');
$stmt->bind_param('si', $status, $application_id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    $stmt->close();
    exit();
}
$stmt->close();

// Insert notification for the applicant
$notif_type = 'application';
$notif_message = 'Your application status has been updated';
$notif_details = $status === 'Hired'
    ? 'Congratulations! You have been hired for the position at ' . $company_name . '.'
    : ($status === 'Rejected'
        ? 'We regret to inform you that you were not selected for the position at ' . $company_name . '.'
        : ($status === 'Interview'
            ? 'You have been invited for an interview for the position at ' . $company_name . '.'
            : 'Your application status has been updated to ' . htmlspecialchars($status) . ' at ' . $company_name . '.'));
$notif_is_read = 0;
$notif_created_at = date('Y-m-d H:i:s');

// Get the user_id of the alumni
$alumni_user_id = null;
$stmt = $db->prepare('SELECT user_id FROM alumni WHERE alumni_id = ? LIMIT 1');
$stmt->bind_param('i', $alumni_id);
$stmt->execute();
$stmt->bind_result($alumni_user_id);
$stmt->fetch();
$stmt->close();

if ($alumni_user_id) {
    $stmt = $db->prepare('INSERT INTO notifications (user_id, type, message, details, is_read, created_at) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('isssis', $alumni_user_id, $notif_type, $notif_message, $notif_details, $notif_is_read, $notif_created_at);
    $stmt->execute();
    $stmt->close();
}

// Send email if Hired, Rejected, or Interview
if (in_array($status, ['Hired', 'Rejected', 'Interview'])) {
    // Get applicant email
    $stmt = $db->prepare('SELECT u.email, u.secondary_email, a.first_name, a.last_name FROM alumni a JOIN user u ON a.user_id = u.user_id WHERE a.alumni_id = ? LIMIT 1');
    $stmt->bind_param('i', $alumni_id);
    $stmt->execute();
    $stmt->bind_result($to_email, $secondary_email, $first_name, $last_name);
    $stmt->fetch();
    $stmt->close();
    $recipient = $to_email ?: $secondary_email;
    
    if ($recipient) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'lspueis@gmail.com'; // Change to your email
            $mail->Password = 'afbp fcwf oujr yqzr'; // Change to your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('lspueis@gmail.com', 'LSPU EIS');
            $mail->addAddress($recipient);
            $mail->isHTML(true);
            
            $mail->Subject = $status === 'Hired' 
                ? 'Congratulations! You have been hired' 
                : ($status === 'Interview' 
                    ? 'Interview Invitation' 
                    : 'Application Update');
            
            $mail->Body = '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Application Status Update</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #00A0E9 0%, #1A1A1A 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                    .button { display: inline-block; background: #00A0E9; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                    .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                    .highlight { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
                    .interview { background: #e3f2fd; border: 1px solid #bbdefb; padding: 15px; border-radius: 5px; margin: 20px 0; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>🎓 LSPU EIS</h1>
                        <p>Laguna State Polytechnic University</p>
                        <p>Employment Information System</p>
                    </div>
                    <div class="content">' .
                    ($status === 'Hired'
                        ? '<h2>Congratulations, ' . htmlspecialchars($first_name . ' ' . $last_name) . '!</h2>
                            <p>You have been <strong>hired</strong> for the position you applied for at <b>' . htmlspecialchars($company_name) . '</b>.</p>
                            <div class="highlight">
                                <h3>Next Steps</h3>
                                <ul>
                                    <li>Please log in to your account for further details and onboarding instructions.</li>
                                    <li>If you have questions, contact the company below.</li>
                                </ul>
                            </div>'
                        : ($status === 'Interview'
                            ? '<h2>Interview Invitation</h2>
                                <p>Dear ' . htmlspecialchars($first_name . ' ' . $last_name) . ',</p>
                                <p>Congratulations! You have been selected for an <strong>interview</strong> for the position at <b>' . htmlspecialchars($company_name) . '</b>.</p>
                                <div class="interview">
                                    <h3>Next Steps</h3>
                                    <ul>
                                        <li>Please check your email for interview schedule details.</li>
                                        <li>Prepare the necessary documents for the interview.</li>
                                        <li>If you have questions, contact the company below.</li>
                                    </ul>
                                </div>'
                            : '<h2>Application Update</h2>
                                <p>Dear ' . htmlspecialchars($first_name . ' ' . $last_name) . ',</p>
                                <p>Thank you for your application. We regret to inform you that you have not been selected for the position at <b>' . htmlspecialchars($company_name) . '</b> at this time.</p>
                                <div class="highlight">
                                    <h3>Keep Trying!</h3>
                                    <ul>
                                        <li>We encourage you to apply for other opportunities in the future.</li>
                                        <li>If you have questions, contact the company below.</li>
                                    </ul>
                                </div>'
                        )
                    ) .
                    '<p><strong>Company Contact:</strong><br>
                    Email: <a href="mailto:' . htmlspecialchars($contact_email) . '">' . htmlspecialchars($contact_email) . '</a><br>
                    Phone: ' . htmlspecialchars($contact_number) . '</p>
                    <a href="http://localhost/lspu_eis/login" 
                        style="display: inline-block; 
                                background: #00A0E9; 
                                color: white; 
                                padding: 12px 30px; 
                                text-decoration: none; 
                                border-radius: 5px; 
                                margin: 20px 0;
                                font-weight: bold;
                                text-align:center;">
                        Login to LSPU EIS
                    </a>
                    <p>Best regards,<br>
                    <strong>' . htmlspecialchars($company_name) . ' - LSPU EIS Team</strong></p>
                    </div>
                    <div class="footer">
                        <p>This is an automated message from the LSPU Employment Information System.</p>
                        <p>© 2024 Laguna State Polytechnic University. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>';

            $mail->AltBody = $status === 'Hired'
                ? "Congratulations, $first_name $last_name!\nYou have been hired for the position you applied for at $company_name.\nContact: $contact_email, $contact_number\nLogin: http://localhost/lspu_eis/login\nBest regards, $company_name - LSPU EIS Team"
                : ($status === 'Interview'
                    ? "Dear $first_name $last_name,\nCongratulations! You have been selected for an interview for the position at $company_name.\nContact: $contact_email, $contact_number\nLogin: http://localhost/lspu_eis/login\nBest regards, $company_name - LSPU EIS Team"
                    : "Dear $first_name $last_name,\nThank you for your application. You have not been selected for the position at $company_name.\nContact: $contact_email, $contact_number\nLogin: http://localhost/lspu_eis/login\nBest regards, $company_name - LSPU EIS Team");
            
            $mail->send();
        } catch (Exception $e) {
            // Optionally log $mail->ErrorInfo
        }
    }
}
echo json_encode(['success' => true]);