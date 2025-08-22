<?php
require_once '../conn/db_conn.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$db = Database::getInstance()->getConnection();
$user_email = $_SESSION['email'];

// Fetch inbox (received messages, not trashed)
$inbox = [];
$stmt = $db->prepare("SELECT * FROM messages WHERE receiver_email = ? AND (folder IS NULL OR folder = 'inbox') ORDER BY created_at DESC");
$stmt->bind_param('s', $user_email);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $inbox[] = $row;
}
$stmt->close();

// Fetch sent messages (not trashed)
$sent = [];
$stmt = $db->prepare("SELECT * FROM messages WHERE sender_email = ? AND (folder IS NULL OR folder = 'sent') ORDER BY created_at DESC");
$stmt->bind_param('s', $user_email);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $sent[] = $row;
}
$stmt->close();

// Fetch important (starred) messages
$important = [];
$stmt = $db->prepare("SELECT * FROM messages WHERE (receiver_email = ? OR sender_email = ?) AND folder = 'important' ORDER BY created_at DESC");
$stmt->bind_param('ss', $user_email, $user_email);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $important[] = $row;
}
$stmt->close();

// Fetch trash
$trash = [];
$stmt = $db->prepare("SELECT * FROM messages WHERE (receiver_email = ? OR sender_email = ?) AND folder = 'trash' ORDER BY created_at DESC");
$stmt->bind_param('ss', $user_email, $user_email);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $trash[] = $row;
}
$stmt->close();

echo json_encode([
    'success' => true,
    'inbox' => $inbox,
    'sent' => $sent,
    'important' => $important,
    'trash' => $trash,
    'inbox_count' => count($inbox),
    'sent_count' => count($sent),
    'important_count' => count($important),
    'trash_count' => count($trash)
]);
exit;

// SQL to add folder column if missing:
// ALTER TABLE messages ADD COLUMN folder VARCHAR(20) DEFAULT NULL; 