<?php
require '../conn/db_conn.php';

header('Content-Type: application/json');

// SQL query to fetch alumni data along with email by joining alumni_profile with users table
$query = "
    SELECT a.*, u.email 
FROM alumni_profile a
INNER JOIN users u ON a.user_id = u.id
WHERE a.status = 'Pending';
";

$result = $conn->query($query);

$alumni_list = [];

while ($row = $result->fetch_assoc()) {
    // You can modify the photo path if needed
    $row['alumni_id_photo'] = '../uploads/' . $row['alumni_id_photo'];
    // Add the email from users table
    $alumni_list[] = $row;
}

// Return the result as a JSON response
echo json_encode($alumni_list);

$conn->close();
