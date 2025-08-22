<?php
/**
 * LSPU EIS Reminder API
 * This API endpoint can be called by external cron services
 * URL: https://yoursite.com/lspu_eis/api_reminder.php
 */

// Set headers for API response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400'); // 24 hours

// Handle preflight OPTIONS request
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Set Philippine timezone
date_default_timezone_set('Asia/Manila');

// Include database connection
require_once 'conn/db_conn.php';

// Load configuration
$config = require_once 'functions/reminder_config.php';

// PHPMailer imports
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// API Response function
function apiResponse($success, $message, $data = null, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s'),
        'timezone' => 'Asia/Manila'
    ]);
    exit;
}



// Send email reminder
function sendEmailReminder($user, $subject, $message, $config) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = $config['email']['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['email']['smtp_username'];
        $mail->Password = $config['email']['smtp_password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['email']['smtp_port'];
        
        $mail->setFrom($config['email']['from_email'], $config['email']['from_name']);
        $mail->addAddress($user['email'], $user['name']);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        
        $html_message = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>'.$subject.'</title>
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
                }
                .content {
                    padding: 30px;
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
                .user-details {
                    background: #e0e7ff;
                    padding: 15px;
                    border-radius: 5px;
                    margin: 20px 0;
                }
                h1, h2, h3 {
                    color: #00A0E9;
                }
                p {
                    font-size: 16px;
                    line-height: 1.6;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1 style="text-color: white;">🎓 LSPU EIS</h1>
                    <p>Laguna State Polytechnic University</p>
                    <p>Employment Information System</p>
                </div>
                
                <div class="content">
                    <h2>Hello '.$user['name'].'!</h2>
                    <p>'.$message.'</p>';
        
        if ($user['role'] === 'alumni' && !empty($user['course']) && !empty($user['college'])) {
            $html_message .= '
                    <div class="user-details">
                        <h3>Your Academic Details</h3>
                        <p><strong>Course:</strong> '.$user['course'].'</p>
                        <p><strong>College:</strong> '.$user['college'].'</p>
                    </div>';
        }
        
        $html_message .= '
                    <a style="text-color: white;" href="http://localhost/lspu-eis" class="button">Access Your Account</a>
                    
                    <div class="footer">
                        <p>This is an automated message from the LSPU Employment Information System.</p>
                        <p>© '.date('Y').' Laguna State Polytechnic University. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>';
        
        $mail->Body = $html_message;
        $mail->AltBody = "Hello ".$user['name'].",\n\n".strip_tags($message)."\n\nAccess your account: http://localhost/lspu-eis\n\nBest regards,\nLSPU EIS Team";
        
        $mail->send();
        return ['success' => true, 'message' => 'Email sent successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"];
    }
}

// Send SMS reminder
// Send SMS reminder
function sendSMSReminder($user, $message, $config) {
    // Clean and format phone number
    $phone = preg_replace('/[^0-9+]/', '', $user['phone']);
    
    // Convert to international format for Philippines (+63)
    if (str_starts_with($phone, '+63')) {
        $phone = substr($phone, 3);
    } elseif (str_starts_with($phone, '63')) {
        $phone = substr($phone, 2);
    } elseif (str_starts_with($phone, '0')) {
        $phone = substr($phone, 1);
    }
    
    // Validate phone number length (should be 10 digits for Philippines)
    if (strlen($phone) !== 10) {
        return [
            'success' => false,
            'message' => 'Invalid phone number format. Expected 10 digits, got ' . strlen($phone) . ' digits: ' . $phone
        ];
    }
    
    // Add country code back
    $phone = '63' . $phone;
    
    // Prepare SMS Chef payload
    $payload = [
        "secret"  => $config['sms']['api_key'],  // API key from config
        "mode"    => "devices",
        "device"  => $config['sms']['device'],   // Device ID from config
        "phone"   => $phone,
        "message" => $message,
        "sim"     => $config['sms']['sim'] ?? 1  // Default SIM slot 1
    ];

    $ch = curl_init($config['sms']['api_url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['success' => false, 'message' => 'cURL Error: ' . $error];
    }
    
    $result = json_decode($response, true);
    
    if ($http_code === 200 && isset($result['status']) && $result['status'] == 200) {
        return [
            'success' => true,
            'message' => 'SMS queued successfully via SMS Chef',
            'message_id' => $result['data']['messageId'] ?? null,
            'response' => $result
        ];
    } else {
        return [
            'success' => false,
            'message' => 'HTTP Error: ' . $http_code . ' - ' . ($result['message'] ?? $response),
            'response' => $result
        ];
    }
}

// Log reminder activities
function logReminder($type, $recipient, $subject, $message, $status, $error_message = null) {
    try {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare('INSERT INTO reminder_logs (type, recipient, subject, message, status, error_message, sent_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        $stmt->bind_param('ssssss', $type, $recipient, $subject, $message, $status, $error_message);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        // Log error silently to avoid breaking the reminder system
    }
}

// Save reminder statistics
function saveReminderStatistics($date, $stats) {
    try {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            INSERT INTO reminder_statistics 
            (date, total_users, emails_sent, emails_failed, sms_sent, sms_failed, total_sent, total_failed) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            total_users = VALUES(total_users),
            emails_sent = VALUES(emails_sent),
            emails_failed = VALUES(emails_failed),
            sms_sent = VALUES(sms_sent),
            sms_failed = VALUES(sms_failed),
            total_sent = VALUES(total_sent),
            total_failed = VALUES(total_failed),
            updated_at = CURRENT_TIMESTAMP
        ");
        
        $stmt->bind_param('siiiiiii', 
            $date, 
            $stats['total_users'], 
            $stats['emails_sent'], 
            $stats['emails_failed'], 
            $stats['sms_sent'], 
            $stats['sms_failed'], 
            $stats['total_sent'], 
            $stats['total_failed']
        );
        
        $stmt->execute();
        $stmt->close();
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Main API Logic
try {
    // Check if it's time to send reminders
    if (!shouldSendRemindersConfig($config)) {
        apiResponse(true, 'Not time to send reminders', [
            'current_time' => date('Y-m-d H:i:s'),
            'business_hours' => $config['time_settings']['start_hour'] . ':00 - ' . $config['time_settings']['end_hour'] . ':00',
            'frequency' => 'Every ' . $config['frequency']['send_every_minutes'] . ' minutes'
        ]);
    }
    
    $db = Database::getInstance()->getConnection();
    
    // Use configuration settings
    $reminder_subject = $config['messages']['email_subject'];
    $reminder_message = $config['messages']['email_message'];
    
    // Fetch only active alumni users
    $sql = "SELECT 
                u.user_id,
                u.email,
                a.contact as phone_number,
                a.first_name,
                a.last_name,
                a.middle_name,
                u.user_role,
                CONCAT(a.first_name, ' ', COALESCE(a.middle_name, ''), ' ', a.last_name) as full_name,
                a.course,
                a.college
            FROM user u
            INNER JOIN alumni a ON u.user_id = a.user_id
            WHERE u.status = 'Active' 
            AND u.user_role = 'alumni'
            AND u.email IS NOT NULL 
            AND u.email != ''
            AND a.contact IS NOT NULL 
            AND a.contact != ''
            ORDER BY u.user_id";
    
    $result = $db->query($sql);
    
    if (!$result) {
        apiResponse(false, 'Failed to fetch user data: ' . $db->error, null, 500);
    }
    
    $total_users = $result->num_rows;
    $email_success = 0;
    $email_failed = 0;
    $sms_success = 0;
    $sms_failed = 0;
    $processed_users = [];
    
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
        
        // Check daily limit
        if (!checkDailyLimitConfig($user['id'], $config)) {
            $processed_users[] = [
                'user_id' => $user['id'],
                'name' => $user['name'],
                'status' => 'skipped',
                'reason' => 'Daily limit reached'
            ];
            continue;
        }
        
        $user_result = ['user_id' => $user['id'], 'name' => $user['name'], 'email' => [], 'sms' => []];
        
        // Send email reminder
        if (!empty($user['email']) && $config['email']['enabled']) {
            $email_result = sendEmailReminder($user, $reminder_subject, $reminder_message, $config);
            if ($email_result['success']) {
                $email_success++;
                logReminder('email', $user['email'], $reminder_subject, $reminder_message, 'sent');
                $user_result['email'] = ['status' => 'sent', 'message' => 'Email sent successfully'];
            } else {
                $email_failed++;
                logReminder('email', $user['email'], $reminder_subject, $reminder_message, 'failed', $email_result['message']);
                $user_result['email'] = ['status' => 'failed', 'message' => $email_result['message']];
            }
        }
        
        // Send SMS reminder
        if (!empty($user['phone']) && $config['sms']['enabled']) {
            $sms_result = sendSMSReminder($user, $config['messages']['sms_message'], $config);
            if ($sms_result['success']) {
                $sms_success++;
                logReminder('sms', $user['phone'], 'SMS Reminder', $config['messages']['sms_message'], 'sent');
                $user_result['sms'] = ['status' => 'sent', 'message' => 'SMS sent successfully'];
            } else {
                $sms_failed++;
                logReminder('sms', $user['phone'], 'SMS Reminder', $config['messages']['sms_message'], 'failed', $sms_result['message']);
                $user_result['sms'] = ['status' => 'failed', 'message' => $sms_result['message']];
            }
        }
        
        $processed_users[] = $user_result;
        
        // Small delay to avoid overwhelming servers
        usleep(100000); // 0.1 second delay
    }
    
    $total_sent = $email_success + $sms_success;
    $total_failed = $email_failed + $sms_failed;
    
    // Save statistics to database
    $stats = [
        'total_users' => $total_users,
        'emails_sent' => $email_success,
        'emails_failed' => $email_failed,
        'sms_sent' => $sms_success,
        'sms_failed' => $sms_failed,
        'total_sent' => $total_sent,
        'total_failed' => $total_failed
    ];
    
    $today = date('Y-m-d');
    saveReminderStatistics($today, $stats);
    
    apiResponse(true, 'Reminder system executed successfully', [
        'summary' => $stats,
        'processed_users' => $processed_users,
        'execution_time' => date('Y-m-d H:i:s'),
        'timezone' => 'Asia/Manila'
    ]);
    
} catch (Exception $e) {
    apiResponse(false, 'Critical error: ' . $e->getMessage(), null, 500);
}
?> 