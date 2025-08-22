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
$degree = $_POST['degree'] ?? '';
$school = $_POST['school'] ?? '';
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$current = isset($_POST['current']) && $_POST['current'] == '1' ? 1 : 0;
if (!$degree || !$school) {
    echo json_encode(['success' => false, 'message' => 'Degree and school are required.']);
    exit();
}
$stmt = $db->prepare('INSERT INTO alumni_education (alumni_id, degree, school, start_date, end_date, current) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->bind_param('issssi', $alumni_id, $degree, $school, $start_date, $end_date, $current);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'education_id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $stmt->error]);
}
$stmt->close();
exit;
