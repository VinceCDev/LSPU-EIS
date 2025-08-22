<?php
session_start();
require_once '../conn/db_conn.php'; // This should initialize $conn as mysqli connection

if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$email = $_SESSION['email'];

// Use prepared statement with mysqli
$sql = "SELECT 
            u.email,
            pi.*
        FROM users u
        JOIN alumni_profile pi ON u.id = pi.user_id
        WHERE u.email = ?";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'No profile data found']);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
}
