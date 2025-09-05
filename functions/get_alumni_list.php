<?php
session_start();
require_once '../conn/db_conn.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $query = "SELECT u.user_id, u.email, a.first_name, a.middle_name, a.last_name, a.profile_pic
              FROM user u 
              JOIN alumni a ON u.user_id = a.user_id 
              WHERE u.status = 'Active' 
              ORDER BY a.first_name, a.last_name";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $alumni = [];
    while ($row = $result->fetch_assoc()) {
        $alumni[] = [
            'user_id' => $row['user_id'],
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'profile_picture' => $row['profile_pic']
        ];
    }
    
    echo json_encode(['success' => true, 'alumni' => $alumni]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>