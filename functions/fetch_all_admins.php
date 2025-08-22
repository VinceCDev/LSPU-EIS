<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = Database::getInstance()->getConnection();
$sql = "SELECT u.user_id, u.email, a.first_name, a.middle_name, a.last_name, a.profile_pic, a.position, a.department, a.status FROM user u INNER JOIN administrator a ON u.user_id = a.user_id WHERE u.user_role = 'admin'";
$result = $db->query($sql);
$admins = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $admins[] = [
            'user_id' => $row['user_id'],
            'email' => $row['email'],
            'name' => trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']),
            'profile_pic' => $row['profile_pic'],
            'position' => $row['position'],
            'department' => $row['department'],
            'status' => $row['status'],
        ];
    }
    echo json_encode(['success' => true, 'admins' => $admins]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
} 