<?php
require_once '../conn/db_conn.php';
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
$alumni_id = $input['alumni_id'] ?? null;

if ($alumni_id) {
    $db = Database::getInstance()->getConnection();
    // Get user_id from alumni
    $stmt = $db->prepare("SELECT user_id FROM alumni WHERE alumni_id = ?");
    $stmt->bind_param("i", $alumni_id);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        // Delete user (cascade will delete alumni)
        $stmt = $db->prepare("DELETE FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $success = $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No alumni_id provided']);
} 