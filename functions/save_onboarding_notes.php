<?php

session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = Database::getInstance()->getConnection();
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['onboarding_id'])) {
    echo json_encode(['success' => false, 'message' => 'Onboarding ID is required']);
    exit;
}

try {
    $notes = isset($data['notes']) ? $data['notes'] : '';

    $stmt = $db->prepare('
        UPDATE applicant_onboarding 
        SET notes = ?, updated_at = NOW()
        WHERE id = ?
    ');
    $stmt->bind_param('si', $notes, $data['onboarding_id']);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Notes saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving notes']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: '.$e->getMessage()]);
}
