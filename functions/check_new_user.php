<?php
session_start();
require_once '../conn/db_conn.php';

// Set header first to ensure JSON response
header('Content-Type: application/json');

$response = ['success' => false, 'is_new_user' => false];

try {
    if (!isset($_SESSION['email'])) {
        throw new Exception('Email not set in session');
    }

    $email = $_SESSION['email'];
    $db = Database::getInstance()->getConnection();
    
    // Get user_id and created_at from user table using email
    $stmt = $db->prepare("SELECT user_id, created_at FROM user WHERE email = ?");
    if (!$stmt) {
        throw new Exception('Failed to prepare user query: ' . $db->error);
    }
    
    $stmt->bind_param('s', $email);
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute user query: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('User not found with email: ' . $email);
    }
    
    $user = $result->fetch_assoc();
    $user_id = $user['user_id'];
    $created_at = $user['created_at'];
    $stmt->close();
    
    // Consider user as "new" if account was created within the last 7 days
    $current_time = new DateTime();
    $account_creation_time = new DateTime($created_at);
    $interval = $current_time->diff($account_creation_time);
    $days_since_creation = $interval->days;
    
    // If account was created today, consider it new
    $is_today = ($current_time->format('Y-m-d') === $account_creation_time->format('Y-m-d'));
    
    $response['success'] = true;
    $response['is_new_user'] = ($days_since_creation <= 7 || $is_today);
    $response['account_age_days'] = $days_since_creation;
    $response['created_at'] = $created_at;
    $response['is_today'] = $is_today;
    
} catch (Exception $e) {
    // Log the error but don't expose details to client
    error_log('check_new_user.php error: ' . $e->getMessage());
    $response['error'] = 'An error occurred';
}

echo json_encode($response);
exit();
?>