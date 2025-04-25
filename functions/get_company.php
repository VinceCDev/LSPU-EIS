<?php
require_once '../conn/db_conn.php';

// Fetch company names from the 'company' table
$query = "SELECT company_name FROM company"; // Adjust as necessary
$result = $pdo->query($query);

// Check if there are any companies
if ($result) {
    $departments = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        // Only fetch the company_name
        $departments[] = $row['company_name'];
    }
    echo json_encode(['success' => true, 'departments' => $departments]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to fetch companies']);
}
