<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'alumni') {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];
$stmt = $db->prepare('SELECT alumni_id FROM alumni a JOIN user u ON a.user_id = u.user_id WHERE u.email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($alumni_id);
$stmt->fetch();
$stmt->close();
if (!$alumni_id) {
    echo json_encode(['success' => false, 'message' => 'Alumni not found.']);
    exit();
}
$stmt = $db->prepare('SELECT resume_id, file_name, uploaded_at FROM alumni_resume WHERE alumni_id = ? ORDER BY uploaded_at DESC LIMIT 1');
$stmt->bind_param('i', $alumni_id);
$stmt->execute();
$stmt->bind_result($resume_id, $file_name, $uploaded_at);
if ($stmt->fetch()) {
    echo json_encode(['success' => true, 'resume' => [
        'resume_id' => $resume_id,
        'file_name' => $file_name,
        'uploaded_at' => $uploaded_at
    ]]);
} else {
    echo json_encode(['success' => true, 'resume' => null]);
}
$stmt->close();
exit; 