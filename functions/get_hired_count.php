<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['hired_count' => 0]);
    exit();
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
    echo json_encode(['hired_count' => 0]);
    exit();
}

// Count applications with status 'Hired' (case-insensitive, trimmed) for this employer's jobs
$sql = "SELECT COUNT(*) FROM applications app JOIN jobs j ON app.job_id = j.job_id WHERE j.employer_id = ? AND LOWER(TRIM(app.status)) = 'hired'";
$stmt = $db->prepare($sql);
$stmt->bind_param('i', $employer_id);
$stmt->execute();
$stmt->bind_result($hired_count);
$stmt->fetch();
$stmt->close();

echo json_encode(['hired_count' => $hired_count]); 