<?php

session_start();
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// Function to get CSRF token
function get_csrf_token()
{
    if (isset($_POST['csrf_token'])) {
        return $_POST['csrf_token'];
    }
    if (isset($_SERVER['HTTP_X_XSRF_TOKEN'])) {
        return $_SERVER['HTTP_X_XSRF_TOKEN'];
    }
    if (isset($_COOKIE['XSRF-TOKEN'])) {
        return $_COOKIE['XSRF-TOKEN'];
    }

    return null;
}

// Function to log login attempts
function logLoginAttempt($db, $email, $status, $userId = null, $failureReason = null)
{
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $attemptTime = date('Y-m-d H:i:s');

    $stmt = $db->prepare('INSERT INTO login_logs (user_id, email, ip_address, user_agent, attempt_time, status, failure_reason) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('issssss', $userId, $email, $ipAddress, $userAgent, $attemptTime, $status, $failureReason);
    $stmt->execute();
    $stmt->close();
}

// Enhanced anti-brute force protection with DEBUG
// Enhanced anti-brute force protection with DEBUG
// Enhanced anti-brute force protection with DEBUG
function checkBruteForce($db, $email)
{
    // Get real IP address (handling proxies)
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $ip = explode(',', $ip)[0];
    $ip = trim($ip);

    error_log("Checking brute force for IP: $ip, Email: $email");

    // Check failed attempts from this IP in last 5 minutes
    $timeLimit = date('Y-m-d H:i:s', strtotime('-5 minutes'));
    $stmt = $db->prepare("SELECT COUNT(*) as attempts 
                         FROM login_logs 
                         WHERE ip_address = ? 
                         AND attempt_time >= ?
                         AND status = 'failed'");
    $stmt->bind_param('ss', $ip, $timeLimit);
    $stmt->execute();
    $result = $stmt->get_result();
    $ipAttempts = $result->fetch_assoc()['attempts'];
    $stmt->close();

    error_log("IP attempts in last 5 minutes: $ipAttempts");

    // Check failed attempts for this email in last 5 minutes
    $stmt = $db->prepare("SELECT COUNT(*) as attempts 
                         FROM login_logs 
                         WHERE email = ? 
                         AND attempt_time >= ?
                         AND status = 'failed'");
    $stmt->bind_param('ss', $email, $timeLimit);
    $stmt->execute();
    $result = $stmt->get_result();
    $emailAttempts = $result->fetch_assoc()['attempts'];
    $stmt->close();

    error_log("Email attempts in last 5 minutes: $emailAttempts");

    // Block if too many attempts from IP OR for this specific email
    if ($ipAttempts >= 10 || $emailAttempts >= 5) {
        error_log('BRUTE FORCE DETECTED: Blocking login');

        return true;
    }

    error_log('No brute force detected, allowing login');

    return false;
}

// Function to send 2FA email with enhanced CSS design
function send2FAEmail($email, $code, $name)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lspueis@gmail.com';
        $mail->Password = 'afbp fcwf oujr yqzr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('noreply@lspu.edu.ph', 'LSPU EIS');
        $mail->addReplyTo('support@lspu.edu.ph', 'LSPU Support');
        $mail->addAddress($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your LSPU Security Verification Code';

        $mail->Body = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSPU Security Verification</title>
    <style>
        /* 1. CONSISTENCY: Maintain LSPU branding throughout */
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        /* 2. HIERARCHY: Clear visual hierarchy */
        .header {
            background: linear-gradient(135deg, #00A0E9 0%, #1A1A1A 100%);
            color: white;
            text-align: center;
            padding: 40px 30px;
        }
        
        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 10px 0;
            letter-spacing: 0.5px;
        }
        
        .header p {
            font-size: 16px;
            margin: 5px 0;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        /* 3. CLARITY: Clear and readable content */
        .title {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .greeting {
            font-size: 16px;
            color: #555;
            text-align: center;
            margin-bottom: 30px;
        }
        
        /* 4. VISUAL EMPHASIS: Highlight important elements */
        .code-container {
            background: linear-gradient(135deg, #f6f9fc 0%, #eef2f7 100%);
            border: 2px dashed #00A0E9;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin: 30px auto;
            max-width: 320px;
        }
        
        .verification-code {
            font-size: 42px;
            font-weight: 700;
            color: #00A0E9;
            letter-spacing: 8px;
            text-align: center;
            font-family: "Courier New", monospace;
            padding: 5px;
            margin: 0;
        }
        
        /* 5. FEEDBACK: Clear status indicators */
        .warning-box {
            background: #fff8e6;
            border: 1px solid #ffd54f;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }
        
        .warning-icon {
            color: #ff9800;
            font-size: 20px;
            margin-right: 10px;
            vertical-align: middle;
        }
        
        .security-notice {
            background: #e8f5e9;
            border: 1px solid #66bb6a;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }
        
        /* 6. ACCESSIBILITY: Good contrast and readability */
        .instructions {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
        }
        
        .instructions h3 {
            font-size: 18px;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .steps {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .steps li {
            padding: 12px 0;
            border-bottom: 1px solid #eaeaea;
            display: flex;
            align-items: center;
        }
        
        .steps li:last-child {
            border-bottom: none;
        }
        
        .step-number {
            background: #00A0E9;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        /* 7. AESTHETIC & MINIMALIST DESIGN: Clean and purposeful */
        .support {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f0f4f8;
            border-radius: 12px;
        }
        
        .footer {
            text-align: center;
            padding: 30px;
            background: #2c3e50;
            color: white;
            font-size: 14px;
        }
        
        .footer a {
            color: #00A0E9;
            text-decoration: none;
        }
        
        .brand-color {
            color: #00A0E9;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mb-20 {
            margin-bottom: 20px;
        }
        
        .mt-20 {
            margin-top: 20px;
        }
        
        /* Responsive design */
        @media (max-width: 480px) {
            .header {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 28px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .verification-code {
                font-size: 32px;
                letter-spacing: 6px;
            }
            
            .code-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with LSPU branding -->
        <div class="header">
            <h1>🎓 LSPU EIS</h1>
            <p>Laguna State Polytechnic University</p>
            <p>Employment Information System</p>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <h2 class="title">Security Verification Required</h2>
            
            <p class="greeting">Hello <strong>'.htmlspecialchars($name).'</strong>,</p>
            
            <p class="text-center">To complete your login, please use the following verification code:</p>
            
            <!-- Verification Code - Clear visual emphasis -->
            <div class="code-container">
                <p class="verification-code">'.$code.'</p>
            </div>
            
            <!-- Expiration Notice - Clear feedback -->
            <div class="warning-box">
                <span class="warning-icon">⏰</span>
                <strong>This code will expire in 10 minutes</strong>
                <p class="mb-0">For your security, please use it immediately</p>
            </div>
            
            <!-- Security Notice - Trust building -->
            <div class="security-notice">
                <span class="warning-icon">🔒</span>
                <strong>Security Alert</strong>
                <p class="mb-0">Never share this code with anyone, including LSPU staff</p>
            </div>
            
            <!-- Step-by-step Instructions - Clear guidance -->
            <div class="instructions">
                <h3>How to Use This Code</h3>
                <ul class="steps">
                    <li>
                        <span class="step-number">1</span>
                        <span>Return to your login screen</span>
                    </li>
                    <li>
                        <span class="step-number">2</span>
                        <span>Enter the verification code above</span>
                    </li>
                    <li>
                        <span class="step-number">3</span>
                        <span>Complete your secure login</span>
                    </li>
                </ul>
            </div>
            
            <!-- Support Information - Help availability -->
            <div class="support">
                <p><strong>Need help?</strong></p>
                <p>If you didn\'t request this code or need assistance, please contact our support team immediately:</p>
                <p>
                    <a href="mailto:support@lspu.edu.ph">support@lspu.edu.ph</a>
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>This is an automated security message from LSPU EIS</p>
            <p>© '.date('Y').' Laguna State Polytechnic University. All rights reserved.</p>
            <p>INTEGRITY • PROFESSIONALISM • INNOVATION</p>
        </div>
    </div>
</body>
</html>
        ';

        // Plain text version for accessibility
        $mail->AltBody = "LSPU EIS SECURITY VERIFICATION\n\n".
            'Hello '.htmlspecialchars($name).",\n\n".
            'Your LSPU EIS verification code is: '.$code."\n\n".
            "This code will expire in 10 minutes.\n\n".
            "SECURITY NOTICE:\n".
            "- Never share this code with anyone\n".
            "- LSPU staff will never ask for this code\n".
            "- If you didn't request this, contact support immediately\n\n".
            "Steps to complete your login:\n".
            "1. Return to your login screen\n".
            '2. Enter the verification code: '.$code."\n".
            "3. Complete your secure login\n\n".
            "Need help? Contact: support@lspu.edu.ph\n\n".
            "This is an automated security message from\n".
            "Laguna State Polytechnic University\n".
            "Employment Information System\n\n".
            '© '.date('Y').' LSPU. All rights reserved.';

        $mail->send();
        error_log("2FA email sent successfully to: $email");

        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");

        return false;
    }
}

// Function to get redirect URL based on user role
function getRedirectUrl($role)
{
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

header('Content-Type: application/json');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    header('Access-Control-Allow-Origin: '.$origin);
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf_token = get_csrf_token();

    // CSRF check first
    if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
        $db = Database::getInstance()->getConnection();
        logLoginAttempt($db, $email, 'failed', null, 'Invalid CSRF token');
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
        exit;
    }

    $db = Database::getInstance()->getConnection();

    // Check brute force BEFORE processing the login
    // In your main login code where you check for brute force:
    if (checkBruteForce($db, $email)) {
        echo json_encode(['success' => false, 'message' => 'Too many failed attempts. Please try again in 5 minutes.']);
        exit;
    }

    $stmt = $db->prepare('SELECT user_id, email, password, user_role, status, two_factor_enabled, two_factor_method, last_login FROM user WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user || !password_verify($password, $user['password'])) {
        logLoginAttempt($db, $email, 'failed', null, 'Invalid credentials');
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit;
    }

    if ($user['status'] !== 'Active') {
        logLoginAttempt($db, $email, 'failed', $user['user_id'], 'Account not active');
        echo json_encode(['success' => false, 'message' => 'Account not active.']);
        exit;
    }

    // Check if 2FA should be required based on last login time (more than 30 days)
    $require2FA = false;
    $lastLogin = $user['last_login'];
    $currentTime = new DateTime();

    if ($lastLogin) {
        $lastLoginTime = new DateTime($lastLogin);
        $interval = $currentTime->diff($lastLoginTime);
        $daysSinceLastLogin = $interval->days;

        // Debug logging
        error_log("User last login: $lastLogin");
        error_log('Current time: '.$currentTime->format('Y-m-d H:i:s'));
        error_log("Days since last login: $daysSinceLastLogin");

        // Require 2FA if last login was more than 30 days ago
        if ($daysSinceLastLogin > 30) {
            $require2FA = true;
            error_log('2FA required due to inactive login (30+ days)');
        }
    } else {
        // If user has never logged in before, require 2FA
        $require2FA = true;
        error_log('2FA required due to first-time login');
    }

    // Check if 2FA is required (either enabled by user or forced by inactivity)
    if ($user['two_factor_enabled'] || $require2FA) {
        // Generate 6-digit verification code
        $verificationCode = sprintf('%06d', mt_rand(0, 999999));
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Store code in database
        $updateStmt = $db->prepare('UPDATE user SET two_factor_code = ?, two_factor_expires = ? WHERE user_id = ?');

        if ($updateStmt === false) {
            error_log('Prepare error: '.$db->error);
            logLoginAttempt($db, $email, 'failed', $user['user_id'], 'Database prepare error');
            echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
            exit;
        }

        $bound = $updateStmt->bind_param('ssi', $verificationCode, $expires, $user['user_id']);
        if ($bound === false) {
            error_log('Bind error: '.$updateStmt->error);
            logLoginAttempt($db, $email, 'failed', $user['user_id'], 'Database bind error');
            echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
            exit;
        }

        $executed = $updateStmt->execute();
        if ($executed === false) {
            error_log('Execute error: '.$updateStmt->error);
            logLoginAttempt($db, $email, 'failed', $user['user_id'], 'Database execute error');
            echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
            exit;
        }

        $affectedRows = $updateStmt->affected_rows;
        error_log("Update completed. Affected rows: $affectedRows");

        $updateStmt->close();

        // Verify the code was saved
        $verifyStmt = $db->prepare('SELECT two_factor_code, two_factor_expires FROM user WHERE user_id = ?');
        $verifyStmt->bind_param('i', $user['user_id']);
        $verifyStmt->execute();
        $verifyResult = $verifyStmt->get_result();
        $savedData = $verifyResult->fetch_assoc();
        $verifyStmt->close();

        if ($savedData['two_factor_code'] !== $verificationCode) {
            error_log("ERROR: Code not saved correctly. Expected: $verificationCode, Got: ".($savedData['two_factor_code'] ?? 'NULL'));
            logLoginAttempt($db, $email, 'failed', $user['user_id'], '2FA code not saved to database');
            echo json_encode(['success' => false, 'message' => 'Failed to generate verification code. Please try again.']);
            exit;
        }

        error_log('SUCCESS: Code saved correctly: '.$savedData['two_factor_code']);

        // Send email with verification code
        $emailSent = send2FAEmail($user['email'], $verificationCode, $user['email']);

        if (!$emailSent) {
            logLoginAttempt($db, $email, 'failed', $user['user_id'], 'Failed to send 2FA email');
            echo json_encode(['success' => false, 'message' => 'Failed to send verification email. Please try again.']);
            exit;
        }

        // Set temporary session variables for 2FA verification
        $_SESSION['temp_user_id'] = $user['user_id'];
        $_SESSION['temp_email'] = $user['email'];
        $_SESSION['temp_user_role'] = $user['user_role'];
        $_SESSION['2fa_required'] = true;
        $_SESSION['2fa_method'] = 'email';
        $_SESSION['2fa_reason'] = $user['two_factor_enabled'] ? 'user_enabled' : 'inactive_login';

        // Log 2FA required event
        $reason = $user['two_factor_enabled'] ? 'User-enabled 2FA' : '2FA required due to inactive login';
        logLoginAttempt($db, $email, '2fa_required', $user['user_id'], $reason);

        echo json_encode([
            'success' => true,
            'requires_2fa' => true,
            'message' => 'Verification code sent to your email.',
            'reason' => $_SESSION['2fa_reason'],
        ]);
        exit;
    } else {
        // No 2FA required - complete login immediately
        // Update last login time
        $updateStmt = $db->prepare('UPDATE user SET last_login = NOW() WHERE user_id = ?');
        $updateStmt->bind_param('i', $user['user_id']);
        $updateStmt->execute();
        $updateStmt->close();

        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_role'] = $user['user_role'];
        $_SESSION['loggedin'] = true;

        // Log successful login
        logLoginAttempt($db, $email, 'success', $user['user_id']);

        if ($user['user_role'] === 'alumni') {
            echo json_encode(['success' => true, 'redirect' => '/lspu_eis/home']);
            exit;
        } elseif ($user['user_role'] === 'employer') {
            echo json_encode(['success' => true, 'redirect' => '/lspu_eis/employer_dashboard']);
            exit;
        } else {
            echo json_encode(['success' => true, 'redirect' => '/lspu_eis/admin_dashboard']);
            exit;
        }
        exit;
    }
}
echo json_encode(['success' => false, 'message' => 'Invalid request.']);
