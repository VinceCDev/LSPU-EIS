<?php
require_once '../conn/db_conn.php';

header('Content-Type: application/json');

// Get the input data (employer user_id) from the request body
$input_data = json_decode(file_get_contents('php://input'), true);
$user_id = $input_data['id'] ?? null; // Get employer user_id, if provided


$db = Database::getInstance()->getConnection();
if ($user_id) {
    // Update the user status to 'Approved' for this employer
    $query = "UPDATE user SET status = 'Active' WHERE user_id = ?";
    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Employer approved successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to approve employer.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing the SQL query.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No employer ID provided.']);
}
