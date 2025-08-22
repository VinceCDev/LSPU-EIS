<?php
require_once '../conn/db_conn.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance()->getConnection();
    $sender_email = $_SESSION['email'] ?? '';
    $receiver_email = $_POST['receiver_email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $role = $_POST['role'] ?? '';

    if (!$sender_email || !$receiver_email || !$subject || !$message) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    $stmt = $db->prepare('INSERT INTO messages (sender_email, receiver_email, subject, message, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->bind_param('sssss', $sender_email, $receiver_email, $subject, $message, $role);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
    }
    $stmt->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
exit;