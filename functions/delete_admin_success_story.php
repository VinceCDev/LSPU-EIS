<?php
session_start();
require_once '../conn/db_conn.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing story ID']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $query = "DELETE FROM success_stories WHERE story_id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $data['id']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Success story deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete success story']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>