<?php

session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$employer_id = $_SESSION['user_id'];
$db = Database::getInstance()->getConnection();

$sql = 'SELECT i.*, a.first_name, a.middle_name, a.last_name, a.contact, u.email,
               (SELECT profile_pic FROM alumni WHERE alumni_id = i.alumni_id) as profile_image
        FROM interviews i
        JOIN alumni a ON i.alumni_id = a.alumni_id
        JOIN user u ON a.user_id = u.user_id
        WHERE i.employer_id = ?
        ORDER BY i.interview_date DESC';

$stmt = $db->prepare($sql);
$stmt->bind_param('i', $employer_id);
$stmt->execute();
$result = $stmt->get_result();

$interviews = [];
while ($row = $result->fetch_assoc()) {
    $row['alumni_name'] = trim($row['first_name'].' '.$row['middle_name'].' '.$row['last_name']);
    if ($row['profile_image']) {
        $row['profile_image'] = 'uploads/profile_picture/'.$row['profile_image'];
    } else {
        $row['profile_image'] = null;
    }
    $interviews[] = $row;
}

echo json_encode(['success' => true, 'interviews' => $interviews]);
