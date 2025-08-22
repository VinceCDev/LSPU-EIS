<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'alumni') {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];
// Get alumni_id
$stmt = $db->prepare('SELECT alumni_id FROM alumni a JOIN user u ON a.user_id = u.user_id WHERE u.email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($alumni_id);
$stmt->fetch();
$stmt->close();
if (!$alumni_id) {
    echo json_encode(['success' => false, 'message' => 'Alumni not found.']);
    exit();
}
$experience = [];
$stmt = $db->prepare('SELECT experience_id, title, company, start_date, end_date, current, description, location_of_work, employment_status, employment_sector FROM alumni_experience WHERE alumni_id = ? ORDER BY start_date DESC');
$stmt->bind_param('i', $alumni_id);
$stmt->execute();
$stmt->bind_result($experience_id, $title, $company, $start_date, $end_date, $current, $description, $location_of_work, $employment_status, $employment_sector);
while ($stmt->fetch()) {
    $experience[] = [
        'experience_id' => $experience_id,
        'title' => $title,
        'company' => $company,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'current' => (bool)$current,
        'description' => $description,
        'location_of_work' => $location_of_work,
        'employment_status' => $employment_status,
        'employment_sector' => $employment_sector
    ];
}
$stmt->close();
echo json_encode(['success' => true, 'experience' => $experience]);
exit; 