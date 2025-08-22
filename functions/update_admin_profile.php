<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$email = $_SESSION['email'];
$db = Database::getInstance()->getConnection();

// Fetch user_id from user table
$stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

// Collect POST data
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$middle_name = isset($_POST['middle_name']) ? trim($_POST['middle_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
$position = isset($_POST['position']) ? trim($_POST['position']) : '';
$department = isset($_POST['department']) ? trim($_POST['department']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';

// Update admin profile (including address)
$stmt = $db->prepare('UPDATE administrator SET first_name=?, middle_name=?, last_name=?, contact=?, position=?, department=?, address=? WHERE user_id=?');
$stmt->bind_param('sssssssi', $first_name, $middle_name, $last_name, $contact, $position, $department, $address, $user_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}
$stmt->close(); 