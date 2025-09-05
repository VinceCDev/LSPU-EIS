<?php
session_start();
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$story_id = $data['story_id'] ?? '';

if (empty($story_id)) {
    echo json_encode(['success' => false, 'message' => 'Story ID is required']);
    exit;
}

$email = $_SESSION['email'];
$db = Database::getInstance()->getConnection();

// First get user_id from email
$stmt = $db->prepare("SELECT user_id FROM user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$user_id = $user['user_id'];

$stmt = $db->prepare("DELETE FROM success_stories WHERE story_id = ? AND user_id = ?");
$stmt->bind_param("ii", $story_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting story']);
}
?>