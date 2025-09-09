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

// Get applicants with status 'Interviewed'
$sql = "SELECT a.application_id, a.alumni_id, a.job_id, 
               al.first_name, al.middle_name, al.last_name, u.email,
               j.title as job_title
        FROM applications a
        JOIN alumni al ON a.alumni_id = al.alumni_id
        JOIN user u ON al.user_id = u.user_id
        JOIN jobs j ON a.job_id = j.job_id
        WHERE j.employer_id = ? AND a.status = 'Interview'
        ORDER BY a.applied_at DESC";

$stmt = $db->prepare($sql);
$stmt->bind_param('i', $employer_id);
$stmt->execute();
$result = $stmt->get_result();

$candidates = [];
while ($row = $result->fetch_assoc()) {
    $row['alumni_name'] = trim($row['first_name'].' '.$row['middle_name'].' '.$row['last_name']);
    $candidates[] = $row;
}

echo json_encode(['success' => true, 'candidates' => $candidates]);
