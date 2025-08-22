<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'alumni') {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();

$job_id = $_GET['job_id'] ?? null;
if (!$job_id) {
    echo json_encode(['success' => false, 'message' => 'Job ID required.']);
    exit();
}

$stmt = $db->prepare('SELECT job_id, employer_id, title, type, location, salary, status, created_at, description, requirements, qualifications, employer_question FROM jobs WHERE job_id = ? LIMIT 1');
$stmt->bind_param('i', $job_id);
$stmt->execute();
$stmt->bind_result($job_id, $employer_id, $title, $type, $location, $salary, $status, $created_at, $description, $requirements, $qualifications, $employer_question);

if ($stmt->fetch()) {
    $stmt->close();
    // Fetch company name from employer table
    $company_name = '';
    $emp_stmt = $db->prepare('SELECT company_name FROM employer WHERE employer_id = ? LIMIT 1');
    $emp_stmt->bind_param('i', $employer_id);
    $emp_stmt->execute();
    $emp_stmt->bind_result($company_name);
    $emp_stmt->fetch();
    $emp_stmt->close();
    echo json_encode([
        'success' => true,
        'job' => [
            'job_id' => $job_id,
            'employer_id' => $employer_id,
            'company_name' => $company_name,
            'title' => $title,
            'type' => $type,
            'location' => $location,
            'salary' => $salary,
            'status' => $status,
            'created_at' => $created_at,
            'description' => $description,
            'requirements' => $requirements,
            'qualifications' => $qualifications,
            'employer_question' => $employer_question
        ]
    ]);
} else {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Job not found.']);
}
exit;
