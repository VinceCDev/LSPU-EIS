<?php
require '../conn/db_conn.php';

header('Content-Type: application/json');

// Defensive: Check connection
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit;
}

// SQL query to fetch pending employers with user info
$query = "
    SELECT e.*, u.email, u.status, u.user_id
    FROM employer e
    INNER JOIN user u ON e.user_id = u.user_id
    WHERE u.status = 'Pending';
";

$result = $conn->query($query);
if (!$result) {
    echo json_encode(['success' => false, 'error' => 'Query failed: ' . $conn->error]);
    exit;
}

$employer_list = [];

while ($row = $result->fetch_assoc()) {
    // Optionally add full logo/document paths
    if (!empty($row['company_logo'])) {
        $row['company_logo'] = '../uploads/logos/' . $row['company_logo'];
    }
    if (!empty($row['document_file'])) {
        $row['document_file'] = '../uploads/documents/' . $row['document_file'];
    }
    $employer_list[] = $row;
}

echo json_encode($employer_list);

$conn->close();
