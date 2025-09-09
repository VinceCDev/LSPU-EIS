<?php

// functions/get_hired_applicants.php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];

// Get employer's user_id from email
$stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($employer_id);
$stmt->fetch();
$stmt->close();

if (!$employer_id) {
    echo json_encode(['success' => false, 'message' => 'Employer not found']);
    exit;
}

// Fetch hired applicants (status = 'Hired')
$sql = "
SELECT 
    app.application_id,
    app.applied_at,
    app.status AS application_status, 
    a.alumni_id,
    a.first_name, a.middle_name, a.last_name, a.birthdate, a.contact, a.gender, 
    a.civil_status, a.city, a.province, a.year_graduated, a.college, a.course,
    u.email, u.secondary_email,
    j.job_id, j.title,
    e.company_name
FROM applications app
JOIN alumni a ON app.alumni_id = a.alumni_id
JOIN user u ON a.user_id = u.user_id
JOIN jobs j ON app.job_id = j.job_id
LEFT JOIN employer e ON j.employer_id = e.user_id
WHERE j.employer_id = ? AND app.status = 'Hired'
ORDER BY app.applied_at DESC
";

$stmt = $db->prepare($sql);
$stmt->bind_param('i', $employer_id);
$stmt->execute();
$result = $stmt->get_result();
$hired_applicants = [];

while ($row = $result->fetch_assoc()) {
    $row['alumni_name'] = trim($row['first_name'].' '.$row['middle_name'].' '.$row['last_name']);

    // Fetch profile picture
    $pic_stmt = $db->prepare('SELECT profile_pic FROM alumni WHERE alumni_id = ? LIMIT 1');
    $pic_stmt->bind_param('i', $row['alumni_id']);
    $pic_stmt->execute();
    $pic_stmt->bind_result($profile_pic);
    if ($pic_stmt->fetch() && $profile_pic) {
        $row['profile_image'] = 'uploads/profile_picture/'.$profile_pic;
    } else {
        $row['profile_image'] = 'images/default-avatar.png';
    }
    $pic_stmt->close();

    $hired_applicants[] = $row;
}

$stmt->close();
echo json_encode(['success' => true, 'hired_applicants' => $hired_applicants]);
