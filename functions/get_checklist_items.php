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
    $stmt = $db->prepare('
        SELECT oci.*, oc.title as checklist_title
        FROM onboarding_checklist_items oci
        JOIN onboarding_checklist oc ON oci.checklist_id = oc.id
        WHERE oc.employer_id = ?
        ORDER BY oc.title, oci.item_order
    ');
    $stmt->bind_param('i', $employer_id);
    $stmt->execute();
    $checklist_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'success' => true,
        'checklist_items' => $checklist_items,
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching checklist items: '.$e->getMessage(),
    ]);
}
