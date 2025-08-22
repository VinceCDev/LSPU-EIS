<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'alumni') {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];
$stmt = $db->prepare('SELECT profile_pic FROM alumni a JOIN user u ON a.user_id = u.user_id WHERE u.email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($file_name);
if ($stmt->fetch()) {
    echo json_encode(['success' => true, 'file_name' => $file_name]);
} else {
    echo json_encode(['success' => false, 'file_name' => null]);
}
$stmt->close();
exit; 