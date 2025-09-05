<?php
session_start();
require_once '../conn/db_conn.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['user_id']) || !isset($data['title']) || !isset($data['content']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $query = "UPDATE success_stories 
              SET user_id = ?, title = ?, content = ?, status = ?, updated_at = NOW()
              WHERE story_id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param('isssi', $data['user_id'], $data['title'], $data['content'], $data['status'], $data['id']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Success story updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update success story']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>