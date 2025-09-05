<?php
// get_job_resources.php (updated)
session_start();
require_once '../conn/db_conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];

try {
    // Get user_id
    $stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }

    // Get ALL resources for ALL jobs belonging to this employer
    $resources = [];
    $stmt = $db->prepare('
        SELECT jr.*, j.title as job_title 
        FROM job_resources jr 
        JOIN jobs j ON jr.job_id = j.job_id 
        WHERE j.employer_id = ? 
        ORDER BY jr.created_at DESC
    ');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $resources[] = $row;
    }

    $stmt->close();
    echo json_encode(['success' => true, 'resources' => $resources]);
    
} catch (Exception $e) {
    error_log("Error in get_job_resources.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}
?>