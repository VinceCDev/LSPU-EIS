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

// Validate input
if (!isset($data['application_id']) || !isset($data['checklist_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$application_id = $data['application_id'];
$checklist_id = $data['checklist_id'];

try {
    // Check if onboarding record already exists
    $check_sql = 'SELECT id FROM applicant_onboarding WHERE application_id = ?';
    $check_stmt = $db->prepare($check_sql);
    $check_stmt->bind_param('i', $application_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Update existing record
        $update_sql = "UPDATE applicant_onboarding SET checklist_id = ?, status = 'in_progress', updated_at = NOW() WHERE application_id = ?";
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->bind_param('ii', $checklist_id, $application_id);

        if ($update_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Checklist updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update checklist']);
        }
        $update_stmt->close();
    } else {
        // Create new onboarding record
        $insert_sql = "INSERT INTO applicant_onboarding (application_id, checklist_id, status, started_at) VALUES (?, ?, 'in_progress', NOW())";
        $insert_stmt = $db->prepare($insert_sql);
        $insert_stmt->bind_param('ii', $application_id, $checklist_id);

        if ($insert_stmt->execute()) {
            $onboarding_id = $insert_stmt->insert_id;

            // Get checklist items
            $items_sql = 'SELECT id FROM onboarding_checklist_items WHERE checklist_id = ?';
            $items_stmt = $db->prepare($items_sql);
            $items_stmt->bind_param('i', $checklist_id);
            $items_stmt->execute();
            $items_result = $items_stmt->get_result();

            // Create progress records for each checklist item
            while ($item = $items_result->fetch_assoc()) {
                $progress_sql = 'INSERT INTO applicant_checklist_progress (onboarding_id, item_id) VALUES (?, ?)';
                $progress_stmt = $db->prepare($progress_sql);
                $progress_stmt->bind_param('ii', $onboarding_id, $item['id']);
                $progress_stmt->execute();
                $progress_stmt->close();
            }

            echo json_encode(['success' => true, 'message' => 'Checklist assigned successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to assign checklist']);
        }
        $insert_stmt->close();
    }

    $check_stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: '.$e->getMessage()]);
}
