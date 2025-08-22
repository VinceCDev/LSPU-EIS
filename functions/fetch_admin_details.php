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

// Fetch admin details
$stmt = $db->prepare('SELECT first_name, middle_name, last_name, gender, contact, position, department, profile_pic, status, address FROM administrator WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $middle_name, $last_name, $gender, $contact, $position, $department, $profile_pic, $status, $address);
if ($stmt->fetch()) {
    $profile = [
        'profile_pic' => $profile_pic,
        'name' => trim($first_name . ' ' . $middle_name . ' ' . $last_name),
        'email' => $email,
        'phone' => $contact,
        'address' => $address ?: '',
        'position' => $position,
        'department' => $department,
        'gender' => $gender,
        'status' => $status
    ];
    echo json_encode(['success' => true, 'profile' => $profile]);
} else {
    echo json_encode(['success' => false, 'message' => 'Admin profile not found']);
}
$stmt->close(); 