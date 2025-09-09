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

if (!isset($data['onboarding_id']) || !isset($data['item_id'])) {
    echo json_encode(['success' => false, 'message' => 'Onboarding ID and Item ID are required']);
    exit;
}

try {
    $is_completed = isset($data['is_completed']) ? (int) $data['is_completed'] : 0;
    $completed_at = $is_completed ? date('Y-m-d H:i:s') : null;

    // Check if progress already exists
    $check_stmt = $db->prepare('SELECT id FROM applicant_checklist_progress WHERE onboarding_id = ? AND item_id = ?');
    $check_stmt->bind_param('ii', $data['onboarding_id'], $data['item_id']);
    $check_stmt->execute();
    $existing = $check_stmt->get_result()->fetch_assoc();

    if ($existing) {
        // Update existing progress
        $stmt = $db->prepare('UPDATE applicant_checklist_progress SET is_completed = ?, completed_at = ? WHERE id = ?');
        $stmt->bind_param('isi', $is_completed, $completed_at, $existing['id']);
    } else {
        // Create new progress
        $stmt = $db->prepare('INSERT INTO applicant_checklist_progress (onboarding_id, item_id, is_completed, completed_at) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('iiis', $data['onboarding_id'], $data['item_id'], $is_completed, $completed_at);
    }

    if ($stmt->execute()) {
        // Update overall completion percentage
        updateCompletionPercentage($db, $data['onboarding_id']);
        echo json_encode(['success' => true, 'message' => 'Checklist item updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating checklist item']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: '.$e->getMessage()]);
}

function updateCompletionPercentage($db, $onboarding_id)
{
    // Get total items in checklist
    $total_stmt = $db->prepare('
        SELECT COUNT(*) as total 
        FROM onboarding_checklist_items oci
        JOIN applicant_onboarding ao ON oci.checklist_id = ao.checklist_id
        WHERE ao.id = ?
    ');
    $total_stmt->bind_param('i', $onboarding_id);
    $total_stmt->execute();
    $total = $total_stmt->get_result()->fetch_assoc()['total'];

    // Get completed items
    $completed_stmt = $db->prepare('
        SELECT COUNT(*) as completed 
        FROM applicant_checklist_progress 
        WHERE onboarding_id = ? AND is_completed = 1
    ');
    $completed_stmt->bind_param('i', $onboarding_id);
    $completed_stmt->execute();
    $completed = $completed_stmt->get_result()->fetch_assoc()['completed'];

    // Calculate percentage
    $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

    // Update status based on percentage
    $status = 'pending';
    if ($percentage == 100) {
        $status = 'completed';
    } elseif ($percentage > 0) {
        $status = 'in_progress';
    }

    // Update onboarding record
    $update_stmt = $db->prepare('
        UPDATE applicant_onboarding 
        SET completion_percentage = ?, status = ?, updated_at = NOW()
        WHERE id = ?
    ');
    $update_stmt->bind_param('isi', $percentage, $status, $onboarding_id);
    $update_stmt->execute();
}
