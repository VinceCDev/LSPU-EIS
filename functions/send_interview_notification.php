    <?php

    session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// Authentication check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get and validate input data
$data = json_decode(file_get_contents('php://input'), true);
$employer_id = $_SESSION['user_id'];
$employer_email = $_SESSION['email'] ?? 'hr@company.com'; // Get email from session
$db = Database::getInstance()->getConnection();

// Validation
if (!isset($data['alumni_id']) || empty($data['alumni_id']) || !isset($data['job_id']) || empty($data['job_id'])) {
    echo json_encode(['success' => false, 'message' => 'Alumni ID and Job ID are required']);
    exit;
}

try {
    // Get alumni details
    $alumni_sql = 'SELECT a.alumni_id, a.user_id, a.first_name, a.last_name, u.email 
                    FROM alumni a 
                    JOIN user u ON a.user_id = u.user_id 
                    WHERE a.alumni_id = ?';
    $alumni_stmt = $db->prepare($alumni_sql);
    $alumni_stmt->bind_param('i', $data['alumni_id']);
    $alumni_stmt->execute();
    $alumni_result = $alumni_stmt->get_result();

    if ($alumni_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Alumni not found']);
        exit;
    }

    $alumni = $alumni_result->fetch_assoc();
    $alumni_stmt->close();

    // Get job details
    $job_sql = 'SELECT title FROM jobs WHERE job_id = ?';
    $job_stmt = $db->prepare($job_sql);
    $job_stmt->bind_param('i', $data['job_id']);
    $job_stmt->execute();
    $job_result = $job_stmt->get_result();
    $job = $job_result->fetch_assoc();
    $job_stmt->close();

    // Get employer details (company name only, email comes from session)
    $employer_sql = 'SELECT company_name FROM employer WHERE user_id = ?';
    $employer_stmt = $db->prepare($employer_sql);
    $employer_stmt->bind_param('i', $employer_id);
    $employer_stmt->execute();
    $employer_result = $employer_stmt->get_result();
    $employer = $employer_result->fetch_assoc();
    $employer_stmt->close();

    // Add email to employer array from session
    $employer['email'] = $employer_email;

    // Prepare notification message based on action
    $action = $data['action'] ?? 'scheduled';
    $interview_date = isset($data['interview_date']) ? new DateTime($data['interview_date']) : null;

    switch ($action) {
        case 'scheduled':
            $notification_type = 'interview_scheduled';
            $notification_message = "Interview scheduled for {$job['title']} at {$employer['company_name']}";
            $email_subject = "Interview Scheduled: {$job['title']} at {$employer['company_name']}";
            break;

        case 'rescheduled':
            $notification_type = 'interview_rescheduled';
            $notification_message = "Interview rescheduled for {$job['title']} at {$employer['company_name']}";
            $email_subject = "Interview Rescheduled: {$job['title']} at {$employer['company_name']}";
            break;

        case 'cancelled':
            $notification_type = 'interview_cancelled';
            $notification_message = "Interview cancelled for {$job['title']} at {$employer['company_name']}";
            $email_subject = "Interview Cancelled: {$job['title']} at {$employer['company_name']}";
            break;

        case 'completed':
            $notification_type = 'interview_completed';
            $notification_message = "Interview completed for {$job['title']} at {$employer['company_name']}";
            $email_subject = "Interview Completed: {$job['title']} at {$employer['company_name']}";
            break;

        default:
            $notification_type = 'interview_update';
            $notification_message = "Interview update for {$job['title']} at {$employer['company_name']}";
            $email_subject = "Interview Update: {$job['title']} at {$employer['company_name']}";
    }

    // Create notification details
    $notification_details = "You have scheduled an interview for {$job['title']} at {$employer['company_name']}. Please check your email and messages for complete details.";

    // Save notification to database
    $stmt_notif = $db->prepare('INSERT INTO notifications (user_id, type, message, details, job_id) VALUES (?, ?, ?, ?, ?)');
    $stmt_notif->bind_param('isssi', $alumni['user_id'], $notification_type, $notification_message, $notification_details, $data['job_id']);

    // Update the response part of your main code:
    if ($stmt_notif->execute()) {
        $notification_id = $stmt_notif->insert_id;

        // Send email notification
        $email_sent = sendInterviewEmail($alumni, $employer, $job, $data, $email_subject, $action);

        // Send internal message
        $message_sent = sendInterviewMessage($alumni['user_id'], $employer_id, $email_subject, $data, $action, $employer, $job);

        $response_message = 'Interview notification sent successfully. ';
        $response_message .= $message_sent ? 'Message sent to alumni platform. ' : 'Failed to send message. ';
        $response_message .= $email_sent ? 'Email notification sent.' : 'Email notification failed (check server logs).';

        echo json_encode([
            'success' => true,
            'message' => $response_message,
            'notification_id' => $notification_id,
            'email_sent' => $email_sent,
            'message_sent' => $message_sent,
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save notification']);
    }

    $stmt_notif->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: '.$e->getMessage()]);
}

// Email sending function
function sendInterviewEmail($alumni, $employer, $job, $interviewData, $subject, $action)
{
    $mail = new PHPMailer(true);
    $to = $alumni['email'];
    $alumni_name = $alumni['first_name'].' '.$alumni['last_name'];

    $interview_date = isset($interviewData['interview_date']) ? new DateTime($interviewData['interview_date']) : null;
    $formatted_date = $interview_date ? $interview_date->format('F j, Y \a\t g:i A') : 'To be determined';

    try {
        // Server settings - UPDATE THESE WITH YOUR ACTUAL CREDENTIALS
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lspueis@gmail.com';
        $mail->Password = 'afbp fcwf oujr yqzr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('no-reply@lspu.edu.ph', 'LSPU EIS System');
        $mail->addAddress($to, $alumni_name);
        $mail->addReplyTo($employer['email'], $employer['company_name']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = getInterviewEmailTemplate($alumni_name, $employer, $job, $formatted_date, $interviewData, $action);
        $mail->AltBody = getPlainTextVersion($alumni_name, $employer, $job, $formatted_date, $interviewData, $action);

        $mail->send();

        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");

        return false;
    }
}
function getInterviewEmailTemplate($alumni_name, $employer, $job, $formatted_date, $interviewData, $action)
{
    $company_name = $employer['company_name'] ?? 'The Company';
    $job_title = $job['title'] ?? 'the position';

    // Action-specific content
    switch ($action) {
        case 'scheduled':
            $title = 'Interview Scheduled';
            $header_color = '#00A0E9';
            $main_message = 'We are pleased to inform you that an interview has been scheduled';
            break;

        case 'rescheduled':
            $title = 'Interview Rescheduled';
            $header_color = '#FF9800';
            $main_message = 'Your interview has been rescheduled';
            break;

        case 'cancelled':
            $title = 'Interview Cancelled';
            $header_color = '#dc2626';
            $main_message = 'We regret to inform you that your interview has been cancelled';
            break;

        case 'completed':
            $title = 'Interview Completed';
            $header_color = '#10B981';
            $main_message = 'Thank you for completing your interview';
            break;

        default:
            $title = 'Interview Update';
            $header_color = '#00A0E9';
            $main_message = "There's an update regarding your interview";
    }

    return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$title} | LSPU EIS</title>
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
                    background: linear-gradient(135deg, {$header_color} 0%, #1A1A1A 100%); 
                    color: white; 
                    padding: 30px; 
                    text-align: center; 
                    border-radius: 10px 10px 0 0; 
                }
                .content { 
                    padding: 30px; 
                    border-radius: 0 0 10px 10px;
                }
                .interview-card {
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
                .button:hover {
                    background: #0088cc;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
                .status-badge {
                    background: {$header_color};
                    color: white;
                    padding: 8px 16px;
                    border-radius: 20px;
                    font-weight: bold;
                    display: inline-block;
                    margin: 10px 0;
                }
                .interview-details {
                    background: #f8f9fa;
                    border-left: 4px solid {$header_color};
                    padding: 15px;
                    margin: 15px 0;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎓 LSPU EIS</h1>
                    <p>Laguna State Polytechnic University</p>
                    <p>Employment Information System</p>
                </div>
                
                <div class='content'>
                    <h2>Hello {$alumni_name},</h2>
                    <p>{$main_message} for the position of <strong>{$job_title}</strong> at <strong>{$company_name}</strong>.</p>
                    
                    <div class='interview-card'>
                        <h3 style='margin-top: 0; color: #00A0E9;'>Interview Details</h3>
                        <div class='status-badge'>{$title}</div>
                        
                        <div class='interview-details'>
                            <p><strong>Date & Time:</strong> {$formatted_date}</p>
                            <p><strong>Interview Type:</strong> {$interviewData['interview_type']}</p>
                            ".(isset($interviewData['location']) ? "<p><strong>Location/Link:</strong> {$interviewData['location']}</p>" : '').'
                            '.(isset($interviewData['duration']) ? "<p><strong>Duration:</strong> {$interviewData['duration']} minutes</p>" : '').'
                        </div>
                        
                        '.(isset($interviewData['notes']) ? "
                        <div class='highlight-box'>
                            <h4 style='margin-top: 0;'>Additional Notes:</h4>
                            <p>{$interviewData['notes']}</p>
                        </div>
                        " : '').'
                    </div>
                    
                    '.($action !== 'cancelled' ? "
                    <div class='highlight-box'>
                        <h4 style='margin-top: 0;'>Preparation Tips:</h4>
                        <ul>
                            <li>Research the company and position</li>
                            <li>Review your resume and portfolio</li>
                            <li>Prepare questions to ask the interviewer</li>
                            <li>Test your equipment (for virtual interviews)</li>
                            <li>Plan to arrive 10-15 minutes early</li>
                        </ul>
                    </div>
                    " : '')."
                    
                    <a href='http://localhost/lspu-eis/alumni_dashboard' class='button'>View Interview Details</a>
                    
                    ".($action === 'cancelled' ? "
                    <div class='highlight-box'>
                        <p><strong>Note:</strong> We apologize for any inconvenience this may cause. 
                        We will contact you if another interview opportunity becomes available.</p>
                    </div>
                    " : '')."
                    
                    <p style='margin-top: 30px;'>Best regards,<br>
                    <strong>{$company_name} Hiring Team</strong><br>
                    <em>Through LSPU Employment Information System</em></p>
                </div>
                
                <div class='footer'>
                    <p>This is an automated message from the LSPU Employment Information System.</p>
                    <p>© ".date('Y').' Laguna State Polytechnic University. All rights reserved.</p>
                    <p><small>If you believe you received this email in error, please contact our support team.</small></p>
                </div>
            </div>
        </body>
        </html>
        ';
}

function getPlainTextVersion($alumni_name, $employer, $job, $formatted_date, $interviewData, $action)
{
    $company_name = $employer['company_name'] ?? 'The Company';
    $job_title = $job['title'] ?? 'the position';

    switch ($action) {
        case 'scheduled':
            $title = 'Interview Scheduled';
            $main_message = 'We are pleased to inform you that an interview has been scheduled';
            break;

        case 'rescheduled':
            $title = 'Interview Rescheduled';
            $main_message = 'Your interview has been rescheduled';
            break;

        case 'cancelled':
            $title = 'Interview Cancelled';
            $main_message = 'We regret to inform you that your interview has been cancelled';
            break;

        case 'completed':
            $title = 'Interview Completed';
            $main_message = 'Thank you for completing your interview';
            break;

        default:
            $title = 'Interview Update';
            $main_message = "There's an update regarding your interview";
    }

    $text = "{$title}\n";
    $text .= "====================\n\n";
    $text .= "Hello {$alumni_name},\n\n";
    $text .= "{$main_message} for the position of {$job_title} at {$company_name}.\n\n";

    $text .= "INTERVIEW DETAILS:\n";
    $text .= "-----------------\n";
    $text .= "Date & Time: {$formatted_date}\n";
    $text .= "Interview Type: {$interviewData['interview_type']}\n";

    if (!empty($interviewData['location'])) {
        $text .= "Location/Link: {$interviewData['location']}\n";
    }

    if (!empty($interviewData['duration'])) {
        $text .= "Duration: {$interviewData['duration']} minutes\n";
    }

    if (!empty($interviewData['notes'])) {
        $text .= "\nAdditional Notes:\n";
        $text .= "{$interviewData['notes']}\n";
    }

    if ($action !== 'cancelled') {
        $text .= "\nPREPARATION TIPS:\n";
        $text .= "----------------\n";
        $text .= "- Research the company and position\n";
        $text .= "- Review your resume and portfolio\n";
        $text .= "- Prepare questions to ask the interviewer\n";
        $text .= "- Test your equipment (for virtual interviews)\n";
        $text .= "- Plan to arrive 10-15 minutes early\n";
    }

    $text .= "\nView your interview details: http://localhost/lspu-eis/alumni_dashboard\n\n";

    if ($action === 'cancelled') {
        $text .= 'Note: We apologize for any inconvenience this may cause. ';
        $text .= "We will contact you if another interview opportunity becomes available.\n\n";
    }

    $text .= "Best regards,\n";
    $text .= "{$company_name} Hiring Team\n";
    $text .= "Through LSPU Employment Information System\n\n";

    $text .= "---\n";
    $text .= "This is an automated message from the LSPU Employment Information System.\n";
    $text .= '© '.date('Y')." Laguna State Polytechnic University. All rights reserved.\n";

    return $text;
}

function sendInterviewMessage($alumni_user_id, $employer_id, $subject, $interviewData, $action, $employer, $job)
{
    global $db;

    $interview_date = isset($interviewData['interview_date']) ? new DateTime($interviewData['interview_date']) : null;
    $formatted_date = $interview_date ? $interview_date->format('F j, Y \a\t g:i A') : 'To be determined';

    // Prepare message content
    $message_content = getInterviewMessageContent($action, $job['title'], $employer['company_name'], $formatted_date, $interviewData);

    // Get sender and receiver emails
    $sender_email = $employer['email'];
    $receiver_email = getEmailByUserId($alumni_user_id);

    if (!$receiver_email) {
        error_log("Failed to get receiver email: receiver=$alumni_user_id");

        return false;
    }

    // Get current datetime
    $current_datetime = date('Y-m-d H:i:s');

    // Insert both messages (for sender and receiver)
    $messages_to_insert = [
        // Sender's copy (employer, folder: sent)
        [
            'sender_email' => $sender_email,
            'receiver_email' => $receiver_email,
            'subject' => $subject,
            'message' => $message_content,
            'role' => 'employer',
            'folder' => 'sent',
            'created_at' => $current_datetime,
        ],
        // Receiver's copy (alumni, folder: inbox)
        [
            'sender_email' => $sender_email,
            'receiver_email' => $receiver_email,
            'subject' => $subject,
            'message' => $message_content,
            'role' => 'alumni',
            'folder' => 'inbox',
            'created_at' => $current_datetime,
        ],
    ];

    $all_success = true;
    $stmt = $db->prepare('INSERT INTO messages (sender_email, receiver_email, subject, message, role, folder, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');

    foreach ($messages_to_insert as $message) {
        $stmt->bind_param(
            'sssssss',
            $message['sender_email'],
            $message['receiver_email'],
            $message['subject'],
            $message['message'],
            $message['role'],
            $message['folder'],
            $message['created_at']
        );

        if (!$stmt->execute()) {
            $all_success = false;
            error_log("Failed to insert message for folder: {$message['folder']}");
        }
    }

    $stmt->close();

    return $all_success;
}

function getEmailByUserId($user_id)
{
    global $db;

    $sql = 'SELECT email FROM user WHERE user_id = ?';
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        return $row['email'];
    }

    return null;
}

function getInterviewMessageContent($action, $job_title, $company_name, $date, $interviewData)
{
    $video_link = isset($interviewData['location']) ? $interviewData['location'] : 'Link will be provided later';

    switch ($action) {
        case 'scheduled':
            return "Your interview for $job_title at $company_name has been scheduled for $date.\n\n".
                "Interview type: {$interviewData['interview_type']}\n".
                "Video call link: {$interviewData['location']}\n".
                (isset($interviewData['notes']) ? "Notes: {$interviewData['notes']}\n" : '');

        case 'rescheduled':
            return "Your interview for $job_title at $company_name has been rescheduled to $date.\n\n".
                "Interview type: {$interviewData['interview_type']}\n".
                "Video call link: {$interviewData['location']}\n".
                (isset($interviewData['notes']) ? "Notes: {$interviewData['notes']}\n" : '');

        case 'cancelled':
            return "Your interview for $job_title at $company_name has been cancelled.\n\n".
                'We apologize for any inconvenience.';

        case 'completed':
            return "Your interview for $job_title at $company_name has been marked as completed.\n\n".
                'We will review your application and contact you soon.';

        default:
            return "There's an update regarding your interview for $job_title at $company_name.\n\n".
                "Video call link: {$interviewData['location']}";
    }
}
