<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();
$user_id = $_SESSION['user_id'];
$experience_id = $_POST['id'] ?? null;
if (!$experience_id) {
    echo json_encode(['success' => false, 'message' => 'Missing experience id.']);
    exit();
}
// Get alumni_id
$stmt = $db->prepare('SELECT alumni_id FROM alumni WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($alumni_id);
$stmt->fetch();
$stmt->close();
if (!$alumni_id) {
    echo json_encode(['success' => false, 'message' => 'Alumni not found.']);
    exit();
}
$stmt = $db->prepare('DELETE FROM alumni_experience WHERE experience_id = ? AND alumni_id = ?');
$stmt->bind_param('ii', $experience_id, $alumni_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'deleted_rows' => $stmt->affected_rows]);
} else {
    echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $stmt->error]);
}
$stmt->close();
exit; 