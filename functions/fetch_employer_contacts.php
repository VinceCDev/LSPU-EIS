<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = Database::getInstance()->getConnection();
$accounts = [];

// Admins - employers can message admins
$sql = "SELECT u.user_id, u.user_role, u.email, a.first_name, a.middle_name, a.last_name, a.profile_pic, a.position, a.department, u.status FROM user u INNER JOIN administrator a ON u.user_id = a.user_id WHERE u.user_role = 'admin' AND u.status = 'active'";
$result = $db->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $accounts[] = [
            'user_id' => $row['user_id'],
            'user_role' => 'admin',
            'email' => $row['email'],
            'name' => trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']),
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'],
            'profile_pic' => $row['profile_pic'],
            'position' => $row['position'],
            'department' => $row['department'],
            'status' => $row['status'],
        ];
    }
}

// Alumni - employers can message alumni
$sql = "SELECT u.user_id, u.user_role, u.email, a.first_name, a.middle_name, a.last_name, a.profile_pic, a.course, a.college, u.status FROM user u INNER JOIN alumni a ON u.user_id = a.user_id WHERE u.user_role = 'alumni' AND u.status = 'active'";
$result = $db->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $accounts[] = [
            'user_id' => $row['user_id'],
            'user_role' => 'alumni',
            'email' => $row['email'],
            'name' => trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']),
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'],
            'profile_pic' => $row['profile_pic'],
            'position' => $row['course'],
            'department' => $row['college'],
            'status' => $row['status'],
        ];
    }
}

echo json_encode(['success' => true, 'accounts' => $accounts]);
?> 