<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_POST['user_id']) || !isset($_POST['user_role'])) {
    echo json_encode(['success' => false, 'message' => 'Missing user_id or user_role.']);
    exit();
}

$user_id = intval($_POST['user_id']);
$user_role = $_POST['user_role'];

$db = Database::getInstance()->getConnection();

// Delete from role-specific table first
if ($user_role === 'admin') {
    $stmt = $db->prepare('DELETE FROM administrator WHERE user_id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();
} elseif ($user_role === 'employer') {
    $stmt = $db->prepare('DELETE FROM employer WHERE user_id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();
} elseif ($user_role === 'alumni') {
    $stmt = $db->prepare('DELETE FROM alumni WHERE user_id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid user role.']);
    exit();
}

// Delete from user table
$stmt = $db->prepare('DELETE FROM user WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Account deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete account: ' . $stmt->error]);
}
$stmt->close(); 