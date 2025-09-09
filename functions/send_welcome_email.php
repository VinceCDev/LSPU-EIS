<?php

session_start();
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

header('Content-Type: application/json');

// Check if user is logged in as employer
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get the POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['application_id']) || !isset($input['email']) || !isset($input['name'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$application_id = $input['application_id'];
$email = $input['email'];
$name = $input['name'];
$session_email = $_SESSION['email'];

try {
    // Get employer details for the email using session email
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('
        SELECT e.company_name
        FROM employer e 
        JOIN user u ON e.user_id = u.user_id 
        WHERE u.email = ?
    ');
    $stmt->bind_param('s', $session_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $employer = $result->fetch_assoc();
    $stmt->close();

    if (!$employer) {
        echo json_encode(['success' => false, 'message' => 'Employer details not found']);
        exit;
    }

    // Get job position details and alumni first_name, last_name
    $stmt = $db->prepare('
        SELECT j.title, a.first_name, a.last_name
        FROM applications app 
        JOIN jobs j ON app.job_id = j.job_id 
        JOIN alumni a ON app.alumni_id = a.alumni_id
        WHERE app.application_id = ?
    ');
    $stmt->bind_param('i', $application_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicationData = $result->fetch_assoc();
    $stmt->close();

    if (!$applicationData) {
        echo json_encode(['success' => false, 'message' => 'Application details not found']);
        exit;
    }

    $jobTitle = $applicationData['title'];
    $alumniFirstName = $applicationData['first_name'];
    $alumniLastName = $applicationData['last_name'];

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'lspueis@gmail.com';
    $mail->Password = 'afbp fcwf oujr yqzr';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('lspueis@gmail.com', 'LSPU EIS System');
    $mail->addAddress($email, $name);
    $mail->addReplyTo($session_email, $employer['company_name']);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Welcome to '.$employer['company_name'].'!';

    // Email template
    $emailTemplate = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Welcome to Our Company</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                line-height: 1.6; 
                color: #333;
                margin: 0;
                padding: 0;
                background-color: #f5f5f5;
            }
            .container { 
                max-width: 600px; 
                margin: 0 auto; 
                background: white;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            .header { 
                background: linear-gradient(135deg, #00A0E9 0%, #1A1A1A 100%); 
                color: white; 
                padding: 30px; 
                text-align: center; 
                border-radius: 10px 10px 0 0; 
            }
            .content { 
                padding: 30px; 
                border-radius: 0 0 10px 10px;
            }
            .welcome-card {
                background: #e8f5e9;
                border: 1px solid #c8e6c9;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
            }
            .button { 
                display: block;
                width: fit-content;
                background: #00A0E9; 
                color: white; 
                padding: 12px 30px; 
                text-decoration: none; 
                border-radius: 5px; 
                margin: 25px auto;
                font-weight: bold;
                text-align: center;
                transition: all 0.3s ease;
            }
            .footer { 
                text-align: center; 
                margin-top: 30px; 
                color: #666; 
                font-size: 14px;
                padding: 20px;
                border-top: 1px solid #eee;
            }
            .highlight-box {
                background: #e3f2fd;
                border: 1px solid #bbdefb;
                padding: 15px;
                border-radius: 8px;
                margin: 20px 0;
            }
            ul {
                padding-left: 20px;
            }
            li {
                margin-bottom: 8px;
            }
            h2 {
                color: #00A0E9;
                margin-top: 0;
            }
            .company-name {
                font-weight: bold;
                color: #1A1A1A;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>".htmlspecialchars($employer['company_name'])."</h1>
                <p>Welcome to Our Team!</p>
            </div>
            
            <div class='content'>
                <h2>Hello ".htmlspecialchars($alumniFirstName).' '.htmlspecialchars($alumniLastName).",</h2>
                <p>We are thrilled to welcome you to <span class='company-name'>".htmlspecialchars($employer['company_name'])."</span>!</p>
                
                <div class='welcome-card'>
                    <h3 style='margin-top: 0; color: #00A0E9;'>Your Position: ".htmlspecialchars($jobTitle)."</h3>
                    
                    <div class='highlight-box'>
                        <h4 style='margin-top: 0;'>Next Steps:</h4>
                        <ul>
                            <li>You will receive onboarding instructions shortly</li>
                            <li>Complete your employee profile</li>
                            <li>Review company policies and benefits</li>
                            <li>Prepare for your first day orientation</li>
                        </ul>
                    </div>
                    
                    <p>We're excited to have you on board and look forward to seeing the great contributions you'll make to our team.</p>
                </div>
                
                <p><strong>Your hiring manager:</strong><br>
                ".htmlspecialchars($employer['company_name']).' Hiring Team<br>
                '.htmlspecialchars($session_email)."</p>
                
                <p style='margin-top: 30px;'>Best regards,<br>
                <strong>The ".htmlspecialchars($employer['company_name'])." Team</strong></p>
            </div>
            
            <div class='footer'>
                <p>This is an automated message from the ".htmlspecialchars($employer['company_name']).' Hiring System.</p>
                <p>Please do not reply to this email directly.</p>
            </div>
        </div>
    </body>
    </html>';

    $mail->Body = $emailTemplate;

    // Alternative plain text version
    $mail->AltBody = "Hello $alumniFirstName $alumniLastName,\n\nWelcome to ".$employer['company_name']."!\n\nWe are thrilled to welcome you as our new ".$jobTitle.".\n\nYou will receive onboarding instructions shortly. Please complete your employee profile and review company policies.\n\nBest regards,\nThe ".$employer['company_name'].' Team';

    // Send email
    $mail->send();

    // Update database to mark welcome email as sent
    $stmt = $db->prepare('
        INSERT INTO onboarding_emails (application_id, email_type, sent_at) 
        VALUES (?, "welcome", NOW())
        ON DUPLICATE KEY UPDATE sent_at = NOW()
    ');
    $stmt->bind_param('i', $application_id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Welcome email sent successfully']);
} catch (Exception $e) {
    error_log('Email sending failed: '.$e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to send email: '.$e->getMessage()]);
} catch (\Exception $e) {
    error_log('Error: '.$e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: '.$e->getMessage()]);
}
