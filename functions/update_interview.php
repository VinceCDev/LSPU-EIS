<?php

session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to users
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

    // Build the update query dynamically based on provided fields
    $update_fields = [];
    $params = [];
    $types = '';

    $allowed_fields = ['status', 'interview_date', 'duration', 'interview_type', 'location', 'notes'];

    foreach ($allowed_fields as $field) {
        if (isset($data[$field])) {
            $update_fields[] = "$field = ?";
            $params[] = $data[$field];
            $types .= 's';
        }
    }

    // If no fields to update
    if (empty($update_fields)) {
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        exit;
    }

    // Add interview_id to parameters
    $params[] = $data['interview_id'];
    $types .= 'i';

    // Build the SQL query
    $sql = 'UPDATE interviews SET '.implode(', ', $update_fields).', updated_at = NOW() WHERE interview_id = ?';

    $stmt = $db->prepare($sql);

    // Dynamic binding
    $bind_params = [$types];
    foreach ($params as &$param) {
        $bind_params[] = &$param;
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_params);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Interview updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update interview: '.$stmt->error]);
    }

    $stmt->close();
} catch (Exception $e) {
    error_log('Update interview error: '.$e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: Could not update interview']);
}
