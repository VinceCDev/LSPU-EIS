<?php
session_start();
require_once '../conn/db_conn.php';

$db = Database::getInstance()->getConnection();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!$user_id && isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    $_SESSION['user_id'] = $user_id;
}

if (!$user_id) {
    echo json_encode(['savedJobs' => []]);
    exit;
}

// Get saved job IDs
$stmt = $db->prepare('SELECT job_id, saved_at FROM saved_jobs WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$saved = [];
while ($row = $result->fetch_assoc()) {
    $saved[] = [
        'job_id' => $row['job_id'],
        'savedDate' => $row['saved_at']
    ];
}
$stmt->close();

if (empty($saved)) {
    echo json_encode(['savedJobs' => []]);
    exit;
}

$savedJobs = [];
foreach ($saved as $savedJob) {
    $job_id = $savedJob['job_id'];
    $savedDate = $savedJob['savedDate'];
    // Fetch job details
    $stmt = $db->prepare('SELECT * FROM jobs WHERE job_id = ? LIMIT 1');
    $stmt->bind_param('i', $job_id);
    $stmt->execute();
    $jobResult = $stmt->get_result();
    $job = $jobResult->fetch_assoc();
    $stmt->close();
    if (!$job) continue;
    // Fetch company details
    $employer_id = $job['employer_id'];
    $company = [];
    if ($employer_id) {
        $stmt = $db->prepare('SELECT * FROM employer WHERE user_id = ? LIMIT 1');
        $stmt->bind_param('i', $employer_id);
        $stmt->execute();
        $companyResult = $stmt->get_result();
        $company = $companyResult->fetch_assoc();
        $stmt->close();
    }
    $job['savedDate'] = $savedDate;
    $job['companyDetails'] = $company;
    $savedJobs[] = $job;
}

echo json_encode(['savedJobs' => $savedJobs]);
