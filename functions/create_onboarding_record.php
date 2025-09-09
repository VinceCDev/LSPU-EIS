<?php

session_start();
require_once '../conn/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$application_id = $data['application_id'];
$employer_id = $data['employer_id'];

$db = Database::getInstance()->getConnection();

// Create a new onboarding record
$stmt = $db->prepare('INSERT INTO onboarding (application_id, employer_id, status) VALUES (?, ?, "pending")');
$stmt->bind_param('ii', $application_id, $employer_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'onboarding_id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create onboarding record']);
}
