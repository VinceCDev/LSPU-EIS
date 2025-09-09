<?php

session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$employer_id = $_SESSION['user_id'];
$db = Database::getInstance()->getConnection();

// Validate required fields
if (!isset($data['interview_id']) || empty($data['interview_id'])) {
    echo json_encode(['success' => false, 'message' => 'Interview ID is required']);
    exit;
}

try {
    // Check if the interview belongs to the current employer
    $check_sql = 'SELECT interview_id FROM interviews WHERE interview_id = ? AND employer_id = ?';
    $check_stmt = $db->prepare($check_sql);
    $check_stmt->bind_param('ii', $data['interview_id'], $employer_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Interview not found or access denied']);
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();

    // Update interview status to Cancelled
    $update_sql = "UPDATE interviews SET status = 'Cancelled', updated_at = NOW() WHERE interview_id = ?";
    $update_stmt = $db->prepare($update_sql);
    $update_stmt->bind_param('i', $data['interview_id']);

    if ($update_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Interview cancelled successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel interview: '.$update_stmt->error]);
    }

    $update_stmt->close();
} catch (Exception $e) {
    error_log('Cancel interview error: '.$e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: Could not cancel interview']);
}
