<?php
require '../conn/db_conn.php'; // Include database connection

header('Content-Type: application/json');

// Get the input data (alumni ID) from the request body
$input_data = json_decode(file_get_contents('php://input'), true);
$alumni_id = $input_data['id'] ?? null; // Get alumni ID, if provided

if ($alumni_id) {
    // SQL query to update the alumni status to 'Active'
    $query = "UPDATE alumni_profile SET status = 'Approved' WHERE alumni_id = ?";

    // Prepare statement
    if ($stmt = $conn->prepare($query)) {
        // Bind the alumni ID parameter to the query
        $stmt->bind_param("i", $alumni_id);

        // Execute the query
        if ($stmt->execute()) {
            // If the update is successful, send success response
            echo json_encode(['success' => true, 'message' => 'Alumni approved successfully.']);
        } else {
            // If there's an error with execution, send error response
            echo json_encode(['success' => false, 'message' => 'Failed to approve alumni.']);
        }

        // Close the statement
        $stmt->close();
    } else {
        // If there's an error with the query preparation, send error response
        echo json_encode(['success' => false, 'message' => 'Error preparing the SQL query.']);
    }
} else {
    // If no alumni ID is provided, send error response
    echo json_encode(['success' => false, 'message' => 'No alumni ID provided.']);
}

// Close the database connection
$conn->close();
