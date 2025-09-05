<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

// PHPMailer imports
require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Check if user is authenticated and is admin
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = Database::getInstance()->getConnection();

// Get reminder data
$reminder_subject = $_POST['subject'] ?? 'LSPU EIS Reminder';
$reminder_message = $_POST['message'] ?? 'This is a reminder from LSPU Employment and Information System.';
$send_email = $_POST['send_email'] ?? true;
$send_sms = $_POST['send_sms'] ?? true;

// Fetch all active users with email and phone from user table
$sql = "SELECT 
            u.user_id,
            u.email,
            a.contact,
            a.first_name,
            a.last_name,
            a.middle_name,
            u.user_role,
            CASE 
                WHEN u.user_role = 'alumni' THEN CONCAT(a.first_name, ' ', COALESCE(a.middle_name, ''), ' ', a.last_name)
                WHEN u.user_role = 'employer' THEN e.company_name
                WHEN u.user_role = 'admin' THEN CONCAT(adm.first_name, ' ', COALESCE(adm.middle_name, ''), ' ', adm.last_name)
                ELSE CONCAT(a.first_name, ' ', COALESCE(a.middle_name, ''), ' ', a.last_name)
            END as full_name,
            CASE 
                WHEN u.user_role = 'alumni' THEN a.course
                ELSE NULL
            END as course,
            CASE 
                WHEN u.user_role = 'alumni' THEN a.college
                ELSE NULL
            END as college
        FROM user u
        LEFT JOIN alumni a ON u.user_id = a.user_id
        LEFT JOIN employer e ON u.user_id = e.user_id
        LEFT JOIN administrator adm ON u.user_id = adm.user_id
        WHERE u.status = 'active' 
        AND u.email IS NOT NULL 
        AND u.email != ''";

$result = $db->query($sql);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch user data']);
    exit();
}

$user_list = [];
$success_count = 0;
$error_count = 0;
$errors = [];

while ($row = $result->fetch_assoc()) {
    $user = [
        'id' => $row['user_id'],
        'email' => $row['email'],
        'phone' => $row['phone_number'],
        'name' => $row['full_name'],
        'role' => $row['user_role'],
        'course' => $row['course'],
        'college' => $row['college']
    ];
    
    $user_list[] = $user;
    
    // Send email reminder
    if ($send_email && !empty($user['email'])) {
        $email_result = sendEmailReminder($user, $reminder_subject, $reminder_message);
        if ($email_result['success']) {
            $success_count++;
            logReminder('email', $user['email'], $reminder_subject, $reminder_message, 'sent');
        } else {
            $error_count++;
            $errors[] = "Email failed for {$user['email']}: " . $email_result['message'];
            logReminder('email', $user['email'], $reminder_subject, $reminder_message, 'failed', $email_result['message']);
        }
    }
    
    // Send SMS reminder
    if ($send_sms && !empty($user['phone'])) {
        $sms_result = sendSMSReminder($user, $reminder_message);
        if ($sms_result['success']) {
            $success_count++;
            logReminder('sms', $user['phone'], 'SMS Reminder', $reminder_message, 'sent');
        } else {
            $error_count++;
            $errors[] = "SMS failed for {$user['phone']}: " . $sms_result['message'];
            logReminder('sms', $user['phone'], 'SMS Reminder', $reminder_message, 'failed', $sms_result['message']);
        }
    }
}

echo json_encode([
    'success' => true,
    'message' => "Reminders sent successfully!",
    'data' => [
        'total_users' => count($user_list),
        'success_count' => $success_count,
        'error_count' => $error_count,
        'errors' => $errors
    ]
]);

// Function to send email reminder
function sendEmailReminder($user, $subject, $message) {
    // Use PHPMailer to send email
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Change to your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'lspueis@gmail.com';
        $mail->Password = 'afbp fcwf oujr yqzr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('lspueis@gmail.com', 'LSPU EIS System');
        $mail->addAddress($user['email'], $user['name']);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        
        // Create HTML email template
        $html_message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #1e40af; color: white; padding: 20px; text-align: center;'>
                <h1>LSPU Employment and Information System</h1>
            </div>
            <div style='padding: 20px; background-color: #f8fafc;'>
                <h2>Hello {$user['name']}!</h2>
                <p style='font-size: 16px; line-height: 1.6;'>$message</p>";
        
        // Add role-specific details
        if ($user['role'] === 'alumni' && !empty($user['course']) && !empty($user['college'])) {
            $html_message .= "
                <div style='background-color: #e0e7ff; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p><strong>Your Details:</strong></p>
                    <p>Course: {$user['course']}</p>
                    <p>College: {$user['college']}</p>
                </div>";
        }
        
        $html_message .= "
                <p style='font-size: 14px; color: #64748b;'>
                    This is an automated reminder from the LSPU EIS system.<br>
                    Please do not reply to this email.
                </p>
            </div>
            <div style='background-color: #1e293b; color: white; padding: 15px; text-align: center; font-size: 12px;'>
                &copy; 2025 Laguna State Polytechnic University - Employment and Information System
            </div>
        </div>";
        
        $mail->Body = $html_message;
        $mail->AltBody = strip_tags($message);
        
        $mail->send();
        return ['success' => true, 'message' => 'Email sent successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"];
    }
}

// Function to send SMS reminder using a free open-source API
function sendSMSReminder($user, $message) {
    // Clean and format phone number
    $phone = preg_replace('/[^0-9+]/', '', $user['phone']);
    if (str_starts_with($phone, '+')) {
        $phone = substr($phone, 1);
    }
    if (str_starts_with($phone, '0')) {
        $phone = '63' . substr($phone, 1);
    }

    // SMS Chef API payload
    $payload = [
        "secret"  => "25582084b5f80149f9896fea59a7a7a4d17c59ea", // Your API Key
        "mode"    => "devices", // Send via your phone
        "device"  => "00e4514aa8b68f55", // From SMS Chef dashboard
        "phone"   => $phone,
        "message" => $message,
        "sim"     => 1 // SIM slot to use
    ];

    // cURL request
    $ch = curl_init("https://www.cloud.smschef.com/api/send/sms");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    // Handle response
    if ($httpCode === 200 && isset($result['id'])) {
        return [
            'success' => true,
            'message' => "SMS queued successfully! Message ID: " . $result['id']
        ];
    } else {
        return [
            'success' => false,
            'message' => "HTTP Code: $httpCode - " . json_encode($result)
        ];
    }
}

// Function to log reminder activities
function logReminder($type, $recipient, $subject, $message, $status, $error_message = null) {
    global $db;
    
    $stmt = $db->prepare('INSERT INTO reminder_logs (type, recipient, subject, message, status, error_message, sent_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
    $stmt->bind_param('ssssss', $type, $recipient, $subject, $message, $status, $error_message);
    $stmt->execute();
    $stmt->close();
}
?> 