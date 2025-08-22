<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];
$stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit();
}
$stmt = $db->prepare('SELECT employer_id FROM employer WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($employer_id);
$stmt->fetch();
$stmt->close();
if (!$employer_id) {
    echo json_encode(['success' => false, 'message' => 'Employer not found.']);
    exit();
}
if (!isset($_FILES['company_logo']) || $_FILES['company_logo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
    exit();
}
$ext = pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION);
$logo_name = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['company_logo']['name']);
$targetDir = realpath(__DIR__ . '/../..') . '/uploads/logos/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}
$logo_path = $targetDir . $logo_name;
if (!move_uploaded_file($_FILES['company_logo']['tmp_name'], $logo_path)) {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
    exit();
}
$stmt = $db->prepare('UPDATE employer SET company_logo = ? WHERE employer_id = ?');
$stmt->bind_param('si', $logo_name, $employer_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'logo' => '/lspu_eis/uploads/logos/' . $logo_name]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update logo.']);
}
$stmt->close();
exit; 