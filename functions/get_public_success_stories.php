<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../conn/db_conn.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $query = "SELECT ss.*, 
                     a.first_name, a.middle_name, a.last_name, a.profile_pic,
                     u.email
              FROM success_stories ss
              JOIN alumni a ON ss.user_id = a.user_id
              JOIN user u ON ss.user_id = u.user_id
              WHERE ss.status = 'Published'
              ORDER BY ss.created_at DESC
              LIMIT 6";
    
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
            'author_full_name' => $row['first_name'] . ' ' . ($row['middle_name'] ? $row['middle_name'] . ' ' : '') . $row['last_name'],
            'author_email' => $row['email'],
            'profile_picture' => $row['profile_pic']
        ];
    }
    
    echo json_encode(['success' => true, 'stories' => $stories]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>