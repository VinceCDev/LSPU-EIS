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

if (!isset($_FILES['profile_pic']) || $_FILES['profile_pic']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
    exit();
}

$uploadDir = '../uploads/profile_picture/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
$ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '_admin.' . $ext;
$targetPath = $uploadDir . $filename;
if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
    $profile_pic = 'uploads/profile_picture/' . $filename;
    $stmt = $db->prepare('UPDATE administrator SET profile_pic=? WHERE user_id=?');
    $stmt->bind_param('si', $profile_pic, $user_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile photo updated.', 'profile_pic' => $profile_pic]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
} 