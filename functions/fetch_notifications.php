<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}

$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];

// Get user_id from user table
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

// Fetch notifications for this user - include job_id
$stmt = $db->prepare('SELECT id, type, message, details, is_read, created_at, job_id FROM notifications WHERE user_id = ? ORDER BY created_at DESC');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = [
        'id' => $row['id'],
        'type' => $row['type'],
        'message' => $row['message'],
        'details' => $row['details'],
        'read' => (bool)$row['is_read'],
        'time' => $row['created_at'],
        'job_id' => $row['job_id']
    ];
}
$stmt->close();

echo json_encode(['success' => true, 'notifications' => $notifications]);
?>