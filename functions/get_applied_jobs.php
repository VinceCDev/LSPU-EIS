<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'alumni') {
    echo json_encode(['appliedJobs' => []]);
    exit();
}
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];

// Get alumni_id
$stmt = $db->prepare('SELECT a.alumni_id FROM alumni a JOIN user u ON a.user_id = u.user_id WHERE u.email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($alumni_id);
$stmt->fetch();
$stmt->close();
if (!$alumni_id) {
    echo json_encode(['appliedJobs' => []]);
    exit();
}

// Get all applications for this alumni
$stmt = $db->prepare('SELECT app.job_id, app.applied_at, j.title, j.type, j.location, j.salary, j.status, j.created_at, j.description, j.requirements, j.qualifications, j.employer_question, j.employer_id FROM applications app JOIN jobs j ON app.job_id = j.job_id WHERE app.alumni_id = ? ORDER BY app.applied_at DESC');
$stmt->bind_param('i', $alumni_id);
$stmt->execute();
$result = $stmt->get_result();
$appliedJobs = [];
while ($row = $result->fetch_assoc()) {
    // Get company name
    $company_name = '';
    if ($row['employer_id']) {
        $emp_stmt = $db->prepare('SELECT company_name FROM employer WHERE employer_id = ? LIMIT 1');
        $emp_stmt->bind_param('i', $row['employer_id']);
        $emp_stmt->execute();
        $emp_stmt->bind_result($company_name);
        $emp_stmt->fetch();
        $emp_stmt->close();
    }
    $row['company_name'] = $company_name;
    $appliedJobs[] = $row;
}
$stmt->close();
echo json_encode(['appliedJobs' => $appliedJobs]); 