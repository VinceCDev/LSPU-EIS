<?php
session_start();
require_once '../conn/db_conn.php';

$response = ['success' => false, 'count' => 0];

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $db = Database::getInstance()->getConnection();
    
    // First get user_id from user table using email
    $stmt = $db->prepare("SELECT user_id FROM user WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['user_id'];
        
        // Now get alumni_id from alumni table using user_id
        $stmt2 = $db->prepare("SELECT alumni_id FROM alumni WHERE user_id = ?");
        $stmt2->bind_param('i', $user_id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        
        if ($result2->num_rows > 0) {
            $alumni = $result2->fetch_assoc();
            $alumni_id = $alumni['alumni_id'];
            
            // Count saved jobs for this alumni
            $stmt3 = $db->prepare('SELECT COUNT(*) as count FROM saved_jobs WHERE alumni_id = ?');
            $stmt3->bind_param('i', $alumni_id);
            $stmt3->execute();
            $result3 = $stmt3->get_result();
            $count = $result3->fetch_assoc()['count'];
            
            $response['success'] = true;
            $response['count'] = $count;
            
            $stmt3->close();
        }
        $stmt2->close();
    }
    $stmt->close();
}

echo json_encode($response);
?>