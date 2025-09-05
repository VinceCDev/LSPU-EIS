<?php
session_start();
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$email = $_SESSION['email'];
$story_id = $_POST['story_id'] ?? '';
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';

if (empty($story_id) || empty($title) || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

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

// When users edit their stories, keep the original status (don't allow them to change it)
$stmt = $db->prepare("UPDATE success_stories SET title = ?, content = ? WHERE story_id = ? AND user_id = ?");
$stmt->bind_param("ssii", $title, $content, $story_id, $user_id);

if ($stmt->execute()) {
    $stmt = $db->prepare("SELECT * FROM success_stories WHERE story_id = ?");
    $stmt->bind_param("i", $story_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $story = $result->fetch_assoc();
    
    echo json_encode(['success' => true, 'story' => $story]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating story']);
}
?>