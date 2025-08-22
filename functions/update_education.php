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
$education_id = $_POST['id'] ?? null;
$degree = $_POST['degree'] ?? '';
$school = $_POST['school'] ?? '';
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$current = isset($_POST['current']) && $_POST['current'] == '1' ? 1 : 0;
if (!$education_id || !$degree || !$school) {
    echo json_encode(['success' => false, 'message' => 'Education ID, degree, and school are required.']);
    exit();
}
// Update the record for the matched education_id and alumni_id
$stmt = $db->prepare('UPDATE alumni_education SET degree=?, school=?, start_date=?, end_date=?, current=? WHERE education_id=? AND alumni_id=?');
$stmt->bind_param('ssssiii', $degree, $school, $start_date, $end_date, $current, $education_id, $alumni_id);
$stmt->execute();
$response = [
    'success' => $stmt->affected_rows > 0,
    'updated_rows' => $stmt->affected_rows,
    'params' => [
        'degree' => $degree,
        'school' => $school,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'current' => $current,
        'education_id' => $education_id,
        'alumni_id' => $alumni_id
    ]
];
if ($stmt->error) {
    $response['message'] = 'Update failed: ' . $stmt->error;
}
$stmt->close();
echo json_encode($response);
exit;