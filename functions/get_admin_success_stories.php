<?php
session_start();
require_once '../conn/db_conn.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $query = "SELECT ss.*, 
                     a.first_name, a.middle_name, a.last_name, a.profile_pic,
                     u.email
              FROM success_stories ss
              JOIN alumni a ON ss.user_id = a.user_id
              JOIN user u ON ss.user_id = u.user_id
              ORDER BY ss.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $stories = [];
    while ($row = $result->fetch_assoc()) {
        $stories[] = [
            'story_id' => $row['story_id'],
            'user_id' => $row['user_id'],
            'title' => $row['title'],
            'content' => $row['content'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'profile_picture' => $row['profile_pic']
        ];
    }
    
    echo json_encode(['success' => true, 'stories' => $stories]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>