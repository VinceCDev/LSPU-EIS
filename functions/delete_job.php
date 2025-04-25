<?php
require_once '../conn/db_conn.php';

// Read raw POST input
$inputData = json_decode(file_get_contents('php://input'), true);

// Check if 'id' is provided
if (isset($inputData['id'])) {
    $jobId = $inputData['id'];

    // Prepare and execute DELETE query
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = :id");
    $stmt->bindParam(':id', $jobId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Job deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->errorInfo()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Missing job ID']);
}

// Close connection
$pdo = null;
