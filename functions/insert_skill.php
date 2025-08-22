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
// Get alumni_id
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
$name = $_POST['name'] ?? '';
$certificate = $_POST['certificate'] ?? '';
if (!$name) {
    echo json_encode(['success' => false, 'message' => 'Skill name is required.']);
    exit();
}
$stmt = $db->prepare('INSERT INTO alumni_skill (alumni_id, name, certificate) VALUES (?, ?, ?)');
$stmt->bind_param('iss', $alumni_id, $name, $certificate);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'skill_id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $stmt->error]);
}
$stmt->close();
exit; 