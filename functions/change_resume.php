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
$stmt = $db->prepare('SELECT resume_id, file_name FROM alumni_resume WHERE alumni_id = ? ORDER BY uploaded_at DESC LIMIT 1');
$stmt->bind_param('i', $alumni_id);
$stmt->execute();
$stmt->bind_result($resume_id, $old_file);
$stmt->fetch();
$stmt->close();
if ($old_file && file_exists('../uploads/resumes/' . $old_file)) {
    unlink('../uploads/resumes/' . $old_file);
}
if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
    exit();
}
$file = $_FILES['resume'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    echo json_encode(['success' => false, 'message' => 'Only PDF files allowed.']);
    exit();
}
$targetDir = '../uploads/resumes/';
if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
$uniqueName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
$targetPath = $targetDir . $uniqueName;
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save file.']);
    exit();
}
$stmt = $db->prepare('UPDATE alumni_resume SET file_name = ?, uploaded_at = NOW() WHERE resume_id = ?');
$stmt->bind_param('si', $uniqueName, $resume_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'resume_id' => $resume_id, 'file_name' => $uniqueName]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
}
$stmt->close();
exit; 