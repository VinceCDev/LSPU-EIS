<?php
session_start();
require_once '../conn/db_conn.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['user_id']) || !isset($data['title']) || !isset($data['content']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $query = "INSERT INTO success_stories (user_id, title, content, status, created_at, updated_at) 
              VALUES (?, ?, ?, ?, NOW(), NOW())";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param('isss', $data['user_id'], $data['title'], $data['content'], $data['status']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Success story added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add success story']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>