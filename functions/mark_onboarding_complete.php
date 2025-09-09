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
    // Start transaction
    $db->begin_transaction();

    // First, mark all checklist items as completed
    $mark_items_stmt = $db->prepare('
        INSERT INTO applicant_checklist_progress (onboarding_id, item_id, is_completed, completed_at)
        SELECT ?, oci.id, 1, NOW()
        FROM onboarding_checklist_items oci
        JOIN applicant_onboarding ao ON oci.checklist_id = ao.checklist_id
        WHERE ao.id = ?
        ON DUPLICATE KEY UPDATE is_completed = 1, completed_at = NOW()
    ');
    $mark_items_stmt->bind_param('ii', $data['onboarding_id'], $data['onboarding_id']);
    $mark_items_stmt->execute();

    // Then update the onboarding record to 100% complete
    $update_stmt = $db->prepare("
        UPDATE applicant_onboarding 
        SET status = 'completed', completion_percentage = 100, completed_at = NOW(), updated_at = NOW()
        WHERE id = ?
    ");
    $update_stmt->bind_param('i', $data['onboarding_id']);

    if ($update_stmt->execute()) {
        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Onboarding marked as complete']);
    } else {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => 'Error marking onboarding as complete']);
    }
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: '.$e->getMessage()]);
}
