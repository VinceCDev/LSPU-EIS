<?php
require_once '../conn/db_conn.php';
header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();
$employer_id = isset($_GET['employer_id']) ? intval($_GET['employer_id']) : 0;
if (!$employer_id) {
    echo json_encode(['success' => false, 'message' => 'Missing employer_id']);
    exit;
}
$stmt = $db->prepare('SELECT * FROM employer WHERE user_id = ?');
$stmt->bind_param('i', $employer_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'data' => $row]);
} else {
    echo json_encode(['success' => false, 'message' => 'Employer not found']);
}
$stmt->close(); 