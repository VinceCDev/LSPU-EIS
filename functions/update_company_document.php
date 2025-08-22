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
if (!isset($_FILES['document_file']) || $_FILES['document_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
    exit();
}
$ext = pathinfo($_FILES['document_file']['name'], PATHINFO_EXTENSION);
$doc_name = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['document_file']['name']);
$doc_path = '../../uploads/documents/' . $doc_name;
move_uploaded_file($_FILES['document_file']['tmp_name'], $doc_path);
$stmt = $db->prepare('UPDATE employer SET document_file = ? WHERE employer_id = ?');
$stmt->bind_param('si', $doc_name, $employer_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'document' => '/lspu_eis/uploads/documents/' . $doc_name]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update document.']);
}
$stmt->close();
exit; 