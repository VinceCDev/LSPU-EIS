<?php
session_start();
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$email = $_SESSION['email'];
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';

if (empty($title) || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Title and content are required']);
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

// For new stories, set status to 'pending' for admin review
$status = 'draft';

$stmt = $db->prepare("INSERT INTO success_stories (user_id, title, content, status) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $user_id, $title, $content, $status);

if ($stmt->execute()) {
    $story_id = $stmt->insert_id;
    $stmt = $db->prepare("SELECT * FROM success_stories WHERE story_id = ?");
    $stmt->bind_param("i", $story_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $story = $result->fetch_assoc();
    
    echo json_encode(['success' => true, 'story' => $story]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error saving story']);
}
?>