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

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['title']) || empty($data['title'])) {
    echo json_encode(['success' => false, 'message' => 'Checklist title is required']);
    exit;
}

try {
    $db->begin_transaction();

    // Save checklist
    $checklist_stmt = $db->prepare('
        INSERT INTO onboarding_checklist (employer_id, title, description, is_custom) 
        VALUES (?, ?, ?, TRUE)
    ');
    $checklist_stmt->bind_param('iss', $employer_id, $data['title'], $data['description']);
    $checklist_stmt->execute();
    $checklist_id = $db->insert_id;

    // Save checklist items
    if (!empty($data['items'])) {
        $item_stmt = $db->prepare('
            INSERT INTO onboarding_checklist_items (checklist_id, item_text, is_required, item_order) 
            VALUES (?, ?, ?, ?)
        ');

        $order = 1;
        foreach ($data['items'] as $item) {
            if (!empty($item['text'])) {
                $is_required = isset($item['is_required']) ? (int) $item['is_required'] : 0;
                $item_stmt->bind_param('isii', $checklist_id, $item['text'], $is_required, $order);
                $item_stmt->execute();
                ++$order;
            }
        }
    }

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Checklist saved successfully']);
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['success' => false, 'message' => 'Error saving checklist: '.$e->getMessage()]);
}
