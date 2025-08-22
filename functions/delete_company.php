<?php
require_once '../conn/db_conn.php';
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
$company_id = $input['company_id'] ?? null;

if ($company_id) {
    $db = Database::getInstance()->getConnection();
    // Get user_id from company_profile
    $stmt = $db->prepare("SELECT user_id FROM employer WHERE id = ?");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        // Delete user (cascade will delete company_profile if foreign key is set)
        $stmt = $db->prepare("DELETE FROM user WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $success = $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No company_id provided']);
} 