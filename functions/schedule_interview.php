<?php

session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$employer_id = $_SESSION['user_id'];
$db = Database::getInstance()->getConnection();

// Get application details to extract job_id and alumni_id
$app_sql = 'SELECT job_id, alumni_id FROM applications WHERE application_id = ?';
$app_stmt = $db->prepare($app_sql);
$app_stmt->bind_param('i', $data['application_id']);
$app_stmt->execute();
$app_result = $app_stmt->get_result();
$application = $app_result->fetch_assoc();
$app_stmt->close();

if (!$application) {
    echo json_encode(['success' => false, 'message' => 'Invalid application']);
    exit;
}

// Insert interview
$sql = 'INSERT INTO interviews (application_id, job_id, alumni_id, employer_id, interview_date, duration, interview_type, location, status, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

$stmt = $db->prepare($sql);
$stmt->bind_param('iiiisissss',
    $data['application_id'],
    $application['job_id'],
    $application['alumni_id'],
    $employer_id,
    $data['interview_date'],
    $data['duration'],
    $data['interview_type'],
    $data['location'],
    $data['status'],
    $data['notes']
);

// After successfully inserting the interview
if ($stmt->execute()) {
    $interview_id = $stmt->insert_id;

    // Return the IDs needed for notification
    echo json_encode([
        'success' => true,
        'message' => 'Interview scheduled successfully',
        'interview_id' => $interview_id,
        'alumni_id' => $application['alumni_id'],
        'job_id' => $application['job_id'],
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to schedule interview']);
}

$stmt->close();
