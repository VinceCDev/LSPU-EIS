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
$stmt = $db->prepare('SELECT alumni_id, verification_document FROM alumni a JOIN user u ON a.user_id = u.user_id WHERE u.email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($alumni_id, $old_doc);
$stmt->fetch();
$stmt->close();
if (!$alumni_id) {
    echo json_encode(['success' => false, 'message' => 'Alumni not found.']);
    exit();
}
if (!isset($_FILES['verification_document']) || $_FILES['verification_document']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
    exit();
}
$file = $_FILES['verification_document'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ['pdf','jpg','jpeg','png','gif'])) {
    echo json_encode(['success' => false, 'message' => 'Only PDF or image files allowed.']);
    exit();
}
$targetDir = '../uploads/documents/';
if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
$uniqueName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
$targetPath = $targetDir . $uniqueName;
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save file.']);
    exit();
}
// Delete old file if exists
if ($old_doc && file_exists($targetDir . $old_doc)) {
    unlink($targetDir . $old_doc);
}
$stmt = $db->prepare('UPDATE alumni SET verification_document = ? WHERE alumni_id = ?');
$stmt->bind_param('si', $uniqueName, $alumni_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'file_name' => $uniqueName]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
}
$stmt->close();
exit; 