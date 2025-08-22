<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

// Check if user is authenticated
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get POST data
$receiver_phone = $_POST['receiver_phone'] ?? '';
$message = $_POST['message'] ?? '';
$user_role = $_SESSION['user_role'];

// Validate input
if (empty($receiver_phone) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Phone number and message are required']);
    exit();
}

// Clean phone number (remove spaces, dashes, etc.)
$receiver_phone = preg_replace('/[^0-9+]/', '', $receiver_phone);

// Add country code if not present (assuming Philippines +63)
if (!str_starts_with($receiver_phone, '+')) {
    if (str_starts_with($receiver_phone, '0')) {
        $receiver_phone = '+63' . substr($receiver_phone, 1);
    } else {
        $receiver_phone = '+63' . $receiver_phone;
    }
}

// Free SMS API configuration
$api_url = 'https://free-sms-api.svxtract.workers.dev/';

// Clean and format phone number for API (remove +63 and get 10 digits)
$api_phone = $receiver_phone;
if (str_starts_with($api_phone, '+63')) {
    $api_phone = substr($api_phone, 3); // Remove +63
} elseif (str_starts_with($api_phone, '63')) {
    $api_phone = substr($api_phone, 2); // Remove 63
} elseif (str_starts_with($api_phone, '0')) {
    $api_phone = substr($api_phone, 1); // Remove leading 0
}

// Ensure it's exactly 10 digits
if (strlen($api_phone) !== 10) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone number format. Must be 10 digits.']);
    exit();
}

// URL encode the message
$encoded_message = urlencode($message);

// Build the API URL with parameters
$full_url = $api_url . '?number=' . $api_phone . '&message=' . $encoded_message;

// Send SMS using cURL
function sendSMS($url) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'LSPU-EIS/1.0');
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    if ($error) {
        return ['success' => false, 'message' => 'cURL Error: ' . $error];
    }
    
    if ($http_code !== 200) {
        return ['success' => false, 'message' => 'HTTP Error: ' . $http_code . ' - ' . $response];
    }
    
    // The API might return different response formats
    // Let's handle both JSON and plain text responses
    $decoded = json_decode($response, true);
    if ($decoded === null) {
        // If not JSON, treat as plain text
        return ['success' => true, 'message' => 'SMS sent successfully', 'response' => $response];
    }
    
    return $decoded;
}

// Send the SMS
$result = sendSMS($full_url);

if ($result['success']) {
    // Log SMS to database
    $db = Database::getInstance()->getConnection();
    $sender_email = $_SESSION['email'];
    $stmt = $db->prepare('INSERT INTO sms_logs (sender_email, receiver_phone, message, status, sent_at, user_role) VALUES (?, ?, ?, ?, NOW(), ?)');
    $status = 'sent';
    $stmt->bind_param('sssss', $sender_email, $receiver_phone, $message, $status, $user_role);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'SMS sent successfully',
            'data' => $result
        ]);
    } else {
        echo json_encode([
            'success' => true, 
            'message' => 'SMS sent but failed to log to database',
            'data' => $result
        ]);
    }
    $stmt->close();
} else {
    // Log failed SMS attempt
    $db = Database::getInstance()->getConnection();
    $sender_email = $_SESSION['email'];
    $stmt = $db->prepare('INSERT INTO sms_logs (sender_email, receiver_phone, message, status, sent_at, user_role, error_message) VALUES (?, ?, ?, ?, NOW(), ?, ?)');
    $status = 'failed';
    $error_msg = $result['message'] ?? 'Unknown error';
    $stmt->bind_param('ssssss', $sender_email, $receiver_phone, $message, $status, $user_role, $error_msg);
    $stmt->execute();
    $stmt->close();
    
    echo json_encode($result);
}
?> 