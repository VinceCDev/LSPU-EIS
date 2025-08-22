<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();
$user_id = $_SESSION['user_id'];
$experience_id = $_POST['id'] ?? null;
$title = $_POST['title'] ?? '';
$company = $_POST['company'] ?? '';
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$current = isset($_POST['current']) && $_POST['current'] == '1' ? 1 : 0;
$description = $_POST['description'] ?? '';
$location_of_work = $_POST['location_of_work'] ?? '';
$employment_status = $_POST['employment_status'] ?? '';
$employment_sector = $_POST['employment_sector'] ?? '';
if (!$experience_id || !$title || !$company || !$start_date) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit();
}
// Get alumni_id
$stmt = $db->prepare('SELECT alumni_id FROM alumni WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($alumni_id);
$stmt->fetch();
$stmt->close();
if (!$alumni_id) {
    echo json_encode(['success' => false, 'message' => 'Alumni not found.']);
    exit();
}
$stmt = $db->prepare('UPDATE alumni_experience SET title=?, company=?, start_date=?, end_date=?, current=?, description=?, location_of_work=?, employment_status=?, employment_sector=? WHERE experience_id=? AND alumni_id=?');
$stmt->bind_param('ssssisssii', $title, $company, $start_date, $end_date, $current, $description, $location_of_work, $employment_status, $employment_sector, $experience_id, $alumni_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'updated_rows' => $stmt->affected_rows]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
}
$stmt->close();
exit;
