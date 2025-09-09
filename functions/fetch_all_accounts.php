<?php

session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = Database::getInstance()->getConnection();
$accounts = [];

// Admins
$sql = "SELECT u.user_id, u.user_role, u.email, u.last_login, a.first_name, a.middle_name, a.last_name, a.profile_pic, a.position, a.department, u.status FROM user u INNER JOIN administrator a ON u.user_id = a.user_id WHERE u.user_role = 'admin'";
$result = $db->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Format last_login to show only date portion
        $last_login = $row['last_login'] ? date('Y-m-d', strtotime($row['last_login'])) : null;

        $accounts[] = [
            'user_id' => $row['user_id'],
            'user_role' => 'admin',
            'email' => $row['email'],
            'last_login' => $last_login,
            'name' => trim($row['first_name'].' '.$row['middle_name'].' '.$row['last_name']),
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
// Employers
$sql = "SELECT u.user_id, u.user_role, u.email, u.last_login, e.company_name, e.company_logo, e.industry_type, u.status FROM user u INNER JOIN employer e ON u.user_id = e.user_id WHERE u.user_role = 'employer'";
$result = $db->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Format last_login to show only date portion
        $last_login = $row['last_login'] ? date('Y-m-d', strtotime($row['last_login'])) : null;

        $accounts[] = [
            'user_id' => $row['user_id'],
            'user_role' => 'employer',
            'email' => $row['email'],
            'last_login' => $last_login,
            'name' => $row['company_name'],
            'company_name' => $row['company_name'],
            'industry_type' => $row['industry_type'],
            'profile_pic' => $row['company_logo'],
            'position' => $row['industry_type'],
            'department' => '',
            'status' => $row['status'],
        ];
    }
}
// Alumni
$sql = "SELECT u.user_id, u.user_role, u.email, u.last_login, a.first_name, a.middle_name, a.last_name, a.profile_pic, a.course, a.college, u.status FROM user u INNER JOIN alumni a ON u.user_id = a.user_id WHERE u.user_role = 'alumni'";
$result = $db->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Format last_login to show only date portion
        $last_login = $row['last_login'] ? date('Y-m-d', strtotime($row['last_login'])) : null;

        $accounts[] = [
            'user_id' => $row['user_id'],
            'user_role' => 'alumni',
            'email' => $row['email'],
            'last_login' => $last_login,
            'name' => trim($row['first_name'].' '.$row['middle_name'].' '.$row['last_name']),
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
