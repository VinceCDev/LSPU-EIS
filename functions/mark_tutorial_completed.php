<?php
session_start();
require_once '../conn/db_conn.php';

$response = ['success' => false];

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    
    $db = Database::getInstance()->getConnection();
    
    // Update user table to mark tutorial as completed
    $stmt = $db->prepare('UPDATE user SET tutorial_completed = 1, tutorial_completed_date = NOW() WHERE email = ?');
    $stmt->bind_param('s', $email);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $_SESSION['tutorial_completed'] = true;
    }
    
    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($response);
?>