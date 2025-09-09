<?php

session_start();
require_once '../conn/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$employer_id = $_SESSION['user_id'];
$db = Database::getInstance()->getConnection();

// Get the first checklist created by this employer
$stmt = $db->prepare('SELECT id FROM onboarding_checklists WHERE employer_id = ? ORDER BY created_at ASC LIMIT 1');
$stmt->bind_param('i', $employer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'checklist_id' => $row['id']]);
} else {
    echo json_encode(['success' => false, 'message' => 'No checklists available']);
}
