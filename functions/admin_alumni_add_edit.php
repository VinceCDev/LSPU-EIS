<?php

session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$employer_id = $_SESSION['user_id'];
$db = Database::getInstance()->getConnection();

// Validate required fields
if (!isset($data['alumni_id']) || empty($data['alumni_id']) || !isset($data['job_id']) || empty($data['job_id'])) {
    echo json_encode(['success' => false, 'message' => 'Alumni ID and Job ID are required']);
    exit;
}

// Log received data for debugging
error_log('Interview notification data: '.print_r($data, true));

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
    $job_sql = 'SELECT title, company_name FROM jobs WHERE job_id = ?';
    $job_stmt = $db->prepare($job_sql);
    $job_stmt->bind_param('i', $data['job_id']);
    $job_stmt->execute();
    $job_result = $job_stmt->get_result();

    if ($job_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Job not found']);
        exit;
    }

    $job = $job_result->fetch_assoc();
    $job_stmt->close();

    // Get employer details
    $employer_sql = 'SELECT company_name, email FROM employer WHERE user_id = ?';
    $employer_stmt = $db->prepare($employer_sql);
    $employer_stmt->bind_param('i', $employer_id);
    $employer_stmt->execute();
    $employer_result = $employer_stmt->get_result();

    if ($employer_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Employer not found']);
        exit;
    }

    $employer = $employer_result->fetch_assoc();
    $employer_stmt->close();

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
    $notification_details = json_encode([
        'interview_id' => $data['interview_id'] ?? null,
        'job_id' => $data['job_id'],
        'job_title' => $job['title'],
        'company_name' => $employer['company_name'],
        'interview_date' => $interview_date ? $interview_date->format('Y-m-d H:i:s') : null,
        'interview_type' => $data['interview_type'] ?? null,
        'location' => $data['location'] ?? null,
        'action' => $action,
    ]);

    // Save notification to database
    $stmt_notif = $db->prepare('INSERT INTO notifications (user_id, type, message, details, job_id) VALUES (?, ?, ?, ?, ?)');
    $stmt_notif->bind_param('isssi', $alumni['user_id'], $notification_type, $notification_message, $notification_details, $data['job_id']);

    if ($stmt_notif->execute()) {
        $notification_id = $stmt_notif->insert_id;
        error_log("Notification saved with ID: $notification_id");

        // Send email notification
        $email_sent = sendInterviewEmail($alumni, $employer, $job, $data, $email_subject, $action);
        error_log('Email sent status: '.($email_sent ? 'Success' : 'Failed'));

        // Send internal message
        $message_sent = sendInterviewMessage($alumni['user_id'], $employer_id, $email_subject, $data, $action, $employer, $job);
        error_log('Message sent status: '.($message_sent ? 'Success' : 'Failed'));

        echo json_encode([
            'success' => true,
            'message' => 'Notification sent successfully',
            'notification_id' => $notification_id,
            'email_sent' => $email_sent,
            'message_sent' => $message_sent,
        ]);
    } else {
        error_log('Failed to save notification: '.$stmt_notif->error);
        echo json_encode(['success' => false, 'message' => 'Failed to save notification: '.$stmt_notif->error]);
    }

    $stmt_notif->close();
} catch (Exception $e) {
    error_log('Error in interview notification: '.$e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: '.$e->getMessage()]);
}

// Email sending function
function sendInterviewEmail($alumni, $employer, $job, $interviewData, $subject, $action)
{
    // Load SMTP configuration from a config file or environment variables
    $smtp_config = include '../config/smtp_config.php'; // Create this file with your SMTP settings

    $mail = new PHPMailer(true);
    $to = $alumni['email'];
    $alumni_name = $alumni['first_name'].' '.$alumni['last_name'];

    $interview_date = isset($interviewData['interview_date']) ? new DateTime($interviewData['interview_date']) : null;
    $formatted_date = $interview_date ? $interview_date->format('F j, Y \a\t g:i A') : 'To be determined';

    try {
        // Server settings - Use configuration from file
        $mail->isSMTP();
        $mail->Host = $smtp_config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_config['username'];
        $mail->Password = $smtp_config['password'];
        $mail->SMTPSecure = $smtp_config['encryption'];
        $mail->Port = $smtp_config['port'];

        // Recipients
        $mail->setFrom($smtp_config['from_email'], $smtp_config['from_name']);
        $mail->addAddress($to, $alumni_name);
        $mail->addReplyTo($employer['email'] ?? $smtp_config['reply_to'], $employer['company_name']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = getInterviewEmailTemplate($alumni_name, $employer, $job, $formatted_date, $interviewData, $action);
        $mail->AltBody = getPlainTextVersion($alumni_name, $employer, $job, $formatted_date, $interviewData, $action);

        $mail->send();
        error_log("Email sent successfully to: $to");

        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent to $to. Mailer Error: {$mail->ErrorInfo}");

        return false;
    }
}

// [Keep the getInterviewEmailTemplate and getPlainTextVersion functions as they are]

function sendInterviewMessage($alumni_user_id, $employer_id, $subject, $interviewData, $action, $employer, $job)
{
    global $db;

    $interview_date = isset($interviewData['interview_date']) ? new DateTime($interviewData['interview_date']) : null;
    $formatted_date = $interview_date ? $interview_date->format('F j, Y \a\t g:i A') : 'To be determined';

    // Prepare message content
    $message_content = getInterviewMessageContent($action, $job['title'], $employer['company_name'], $formatted_date, $interviewData);

    // Get sender and receiver emails
    $sender_email = getEmailByUserId($employer_id);
    $receiver_email = getEmailByUserId($alumni_user_id);

    if (!$sender_email || !$receiver_email) {
        error_log("Failed to get emails: sender=$employer_id, receiver=$alumni_user_id");

        return false;
    }

    try {
        // Save message to database for sender
        $stmt_msg_sender = $db->prepare('INSERT INTO messages (sender_email, receiver_email, subject, message, role, folder) VALUES (?, ?, ?, ?, ?, ?)');
        $folder_sender = 'sent';
        $role = 'employer';
        $stmt_msg_sender->bind_param('ssssss', $sender_email, $receiver_email, $subject, $message_content, $role, $folder_sender);

        // Save message to database for receiver
        $stmt_msg_receiver = $db->prepare('INSERT INTO messages (sender_email, receiver_email, subject, message, role, folder) VALUES (?, ?, ?, ?, ?, ?)');
        $folder_receiver = 'inbox';
        $role_receiver = 'alumni';
        $stmt_msg_receiver->bind_param('ssssss', $sender_email, $receiver_email, $subject, $message_content, $role_receiver, $folder_receiver);

        // Execute both inserts
        $sender_result = $stmt_msg_sender->execute();
        $receiver_result = $stmt_msg_receiver->execute();

        $stmt_msg_sender->close();
        $stmt_msg_receiver->close();

        if ($sender_result && $receiver_result) {
            error_log('Message saved successfully for both sender and receiver');

            return true;
        } else {
            error_log("Failed to save message: Sender - $sender_result, Receiver - $receiver_result");

            return false;
        }
    } catch (Exception $e) {
        error_log('Error saving message: '.$e->getMessage());

        return false;
    }
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
        $stmt->close();

        return $row['email'];
    }

    $stmt->close();

    return null;
}

function getInterviewMessageContent($action, $job_title, $company_name, $date, $interviewData)
{
    switch ($action) {
        case 'scheduled':
            return "Your interview for $job_title at $company_name has been scheduled for $date. ".
                   "Interview type: {$interviewData['interview_type']}. ".
                   (isset($interviewData['location']) ? "Location: {$interviewData['location']}. " : '').
                   (isset($interviewData['notes']) ? "Notes: {$interviewData['notes']}" : '');

        case 'rescheduled':
            return "Your interview for $job_title at $company_name has been rescheduled to $date. ".
                   "Interview type: {$interviewData['interview_type']}. ".
                   (isset($interviewData['location']) ? "Location: {$interviewData['location']}. " : '').
                   (isset($interviewData['notes']) ? "Notes: {$interviewData['notes']}" : '');

        case 'cancelled':
            return "Your interview for $job_title at $company_name has been cancelled. ".
                   (isset($interviewData['notes']) ? "Reason: {$interviewData['notes']}" : 'We apologize for any inconvenience.');

        case 'completed':
            return "Your interview for $job_title at $company_name has been marked as completed. ".
                   'We will review your application and contact you soon.';

        default:
            return "There's an update regarding your interview for $job_title at $company_name. ".
                   (isset($interviewData['notes']) ? "Details: {$interviewData['notes']}" : '');
    }
}
