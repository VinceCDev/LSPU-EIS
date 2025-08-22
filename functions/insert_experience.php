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
$title = $_POST['title'] ?? '';
$company = $_POST['company'] ?? '';
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$current = isset($_POST['current']) && $_POST['current'] == '1' ? 1 : 0;
$description = $_POST['description'] ?? '';
$location_of_work = $_POST['location_of_work'] ?? '';
$employment_status = $_POST['employment_status'] ?? '';
$employment_sector = $_POST['employment_sector'] ?? '';
if (!$title || !$company || !$start_date) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit();
}
$stmt = $db->prepare('INSERT INTO alumni_experience (alumni_id, title, company, start_date, end_date, current, description, location_of_work, employment_status, employment_sector) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->bind_param('issssissss', $alumni_id, $title, $company, $start_date, $end_date, $current, $description, $location_of_work, $employment_status, $employment_sector);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'experience_id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $stmt->error]);
}
$stmt->close();
exit; 