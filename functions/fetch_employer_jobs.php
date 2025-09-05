<?php
session_start();
require_once '../conn/db_conn.php';

// Set headers first to prevent any output
header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
error_reporting(0); // Turn off error reporting to prevent HTML in JSON

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

    // Get jobs for this employer
    $jobs = [];
    $stmt = $db->prepare('SELECT job_id, title FROM jobs WHERE employer_id = ? ORDER BY created_at DESC');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $jobs[] = $row;
    }

    $stmt->close();
    echo json_encode(['success' => true, 'jobs' => $jobs]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>