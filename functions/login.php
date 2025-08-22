<?php
session_start();
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    header('Access-Control-Allow-Origin: ' . $origin);
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

function get_csrf_token() {
    // Prefer POST, then header, then cookie
    if (isset($_POST['csrf_token'])) return $_POST['csrf_token'];
    if (isset($_SERVER['HTTP_X_XSRF_TOKEN'])) return $_SERVER['HTTP_X_XSRF_TOKEN'];
    if (isset($_COOKIE['XSRF-TOKEN'])) return $_COOKIE['XSRF-TOKEN'];
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf_token = get_csrf_token();

    // CSRF check
    if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
        file_put_contents('debug_csrf.txt', 'Session: ' . ($_SESSION['csrf_token'] ?? 'none') . ' | Posted: ' . ($csrf_token ?? 'none'));
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
        exit;
    }

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT user_id, email, secondary_email, password, user_role, status FROM user WHERE email = ? OR secondary_email = ? LIMIT 1");
    $stmt->bind_param('ss', $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit;
    }
    if ($user['status'] !== 'Active') {
        echo json_encode(['success' => false, 'message' => 'Account not active.']);
        exit;
    }

    // Set session and redirect based on user_role
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_role'] = $user['user_role'];
    $_SESSION['email'] = $user['email'];
    if ($user['user_role'] === 'alumni') {
        echo json_encode(['success' => true, 'redirect' => '/lspu_eis/home']);
        exit;
    } else {
        echo json_encode(['success' => true, 'redirect' => '/lspu_eis/admin_dashboard']);
        exit;
    }
}
echo json_encode(['success' => false, 'message' => 'Invalid request.']);
