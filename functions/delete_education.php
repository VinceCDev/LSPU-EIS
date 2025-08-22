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
$education_id = $_POST['id'] ?? null;
if (!$education_id) {
    echo json_encode(['success' => false, 'message' => 'Missing education id.']);
    exit();
}
// Get alumni_id for this user
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
// Delete the record for the matched education_id and alumni_id
$stmt = $db->prepare('DELETE FROM alumni_education WHERE education_id = ? AND alumni_id = ?');
$stmt->bind_param('ii', $education_id, $alumni_id);
$stmt->execute();
$response = [
    'success' => $stmt->affected_rows > 0,
    'deleted_rows' => $stmt->affected_rows,
    'params' => [
        'education_id' => $education_id,
        'alumni_id' => $alumni_id
    ]
];
if ($stmt->error) {
    $response['message'] = 'Delete failed: ' . $stmt->error;
}
$stmt->close();
echo json_encode($response);
exit;
