<?php
session_start();
require_once '../conn/db_conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Validate CSRF token
function get_csrf_token() {
    if (isset($_POST['csrf_token'])) return $_POST['csrf_token'];
    if (isset($_SERVER['HTTP_X_XSRF_TOKEN'])) return $_SERVER['HTTP_X_XSRF_TOKEN'];
    if (isset($_COOKIE['XSRF-TOKEN'])) return $_COOKIE['XSRF-TOKEN'];
    return null;
}

// Function to log login attempts
function logLoginAttempt($db, $email, $status, $userId = null, $reason = '') {
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $attemptTime = date('Y-m-d H:i:s');
    
    $stmt = $db->prepare("INSERT INTO login_logs (user_id, email, ip_address, user_agent, attempt_time, status, failure_reason) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issssss', $userId, $email, $ipAddress, $userAgent, $attemptTime, $status, $reason);
    $stmt->execute();
    $stmt->close();
}


// Function to get redirect URL based on user role
function getRedirectUrl($role) {
    switch ($role) {
        case 'admin':
            return 'admin_dashboard';
        case 'alumni':
            return 'home';
        case 'employer':
            return 'employer_dashboard';
        default:
            return 'login';
    }
}

$csrf_token = get_csrf_token();
if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

// Check if 2FA is required
if (!isset($_SESSION['2fa_required']) || !$_SESSION['2fa_required']) {
    echo json_encode(['success' => false, 'message' => '2FA not required.']);
    exit;
}

$code = $_POST['verification_code'] ?? '';
$user_id = $_SESSION['temp_user_id'] ?? 0;

if (empty($code) || $user_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

// Get user's 2FA code from database
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT two_factor_code, two_factor_expires FROM user WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

// Check if code is valid and not expired
$currentTime = date('Y-m-d H:i:s');
$isValid = ($user['two_factor_code'] === $code && $currentTime < $user['two_factor_expires']);

if ($isValid) {
    // Clear the 2FA code
    $updateStmt = $db->prepare("UPDATE user SET two_factor_code = NULL, two_factor_expires = NULL WHERE user_id = ?");
    $updateStmt->bind_param('i', $user_id);
    $updateStmt->execute();
    $updateStmt->close();
    
    // Complete the login process
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_role'] = $_SESSION['temp_user_role'];
    $_SESSION['email'] = $_SESSION['temp_email'];
    
    // Clean up temporary session variables
    unset($_SESSION['2fa_required']);
    unset($_SESSION['temp_user_id']);
    unset($_SESSION['temp_email']);
    unset($_SESSION['temp_user_role']);
    unset($_SESSION['2fa_method']);
    
    // Update last login time
    $updateStmt = $db->prepare("UPDATE user SET last_login = NOW() WHERE user_id = ?");
    $updateStmt->bind_param('i', $user_id);
    $updateStmt->execute();
    $updateStmt->close();
    
    // Log successful login
    logLoginAttempt($db, $_SESSION['email'], 'success', $user_id);
    
    echo json_encode(['success' => true, 'redirect' => getRedirectUrl($_SESSION['user_role'])]);
} else {
    // Log failed 2FA attempt
    logLoginAttempt($db, $_SESSION['temp_email'], 'failed', $user_id, 'Invalid 2FA code');
    
    echo json_encode(['success' => false, 'message' => 'Invalid or expired verification code.']);
}