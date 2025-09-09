<?php

session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = Database::getInstance()->getConnection();
$employer_id = $_SESSION['user_id'];

try {
    // Fetch checklists
    $checklist_stmt = $db->prepare('
        SELECT id, title, description, is_custom, created_at 
        FROM onboarding_checklist 
        WHERE employer_id = ?
        ORDER BY created_at DESC
    ');
    $checklist_stmt->bind_param('i', $employer_id);
    $checklist_stmt->execute();
    $checklists = $checklist_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch onboarding data
    $onboarding_stmt = $db->prepare('
        SELECT ao.*, a.application_id, a.alumni_id
        FROM applicant_onboarding ao
        JOIN applications a ON ao.application_id = a.application_id
        JOIN jobs j ON a.job_id = j.job_id
        WHERE j.employer_id = ?
    ');
    $onboarding_stmt->bind_param('i', $employer_id);
    $onboarding_stmt->execute();
    $onboarding_data = $onboarding_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'success' => true,
        'checklists' => $checklists,
        'onboarding_data' => $onboarding_data,
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching onboarding data: '.$e->getMessage(),
    ]);
}
