<?php

session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['onboarding_id'])) {
    echo json_encode(['success' => false, 'message' => 'Onboarding ID is required']);
    exit;
}

$db = Database::getInstance()->getConnection();
$onboarding_id = $_GET['onboarding_id'];

try {
    // Get onboarding details including checklist name
    $onboarding_stmt = $db->prepare('
        SELECT ao.*, oc.title as checklist_name 
        FROM applicant_onboarding ao 
        LEFT JOIN onboarding_checklist oc ON ao.checklist_id = oc.id 
        WHERE ao.id = ?
    ');
    $onboarding_stmt->bind_param('i', $onboarding_id);
    $onboarding_stmt->execute();
    $onboarding_result = $onboarding_stmt->get_result();

    if ($onboarding_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Onboarding record not found']);
        exit;
    }

    $onboarding_details = $onboarding_result->fetch_assoc();

    // Get checklist items with progress
    $items_stmt = $db->prepare('
        SELECT 
            oci.id,
            oci.item_text,
            oci.is_required,
            oci.item_order,
            acp.is_completed,
            DATE(acp.completed_at) as completed_date,
            acp.notes
        FROM onboarding_checklist_items oci
        LEFT JOIN applicant_checklist_progress acp ON oci.id = acp.item_id AND acp.onboarding_id = ?
        WHERE oci.checklist_id = ?
        ORDER BY oci.item_order
    ');
    $items_stmt->bind_param('ii', $onboarding_id, $onboarding_details['checklist_id']);
    $items_stmt->execute();
    $checklist_items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'success' => true,
        'onboarding_details' => array_merge($onboarding_details, [
            'checklist_items' => $checklist_items,
        ]),
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching onboarding details: '.$e->getMessage(),
    ]);
}
