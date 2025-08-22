<?php
session_start();
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$email = $_SESSION['email'];
$userQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
$userQuery->bind_param("s", $email);
$userQuery->execute();
$userResult = $userQuery->get_result();

if ($userResult->num_rows === 0) {
    echo json_encode([]);
    exit;
}

$user = $userResult->fetch_assoc();
$user_id = $user['id'];

$alumniQuery = $conn->prepare("SELECT alumni_id FROM alumni_profile WHERE user_id = ?");
$alumniQuery->bind_param("i", $user_id);
$alumniQuery->execute();
$alumniResult = $alumniQuery->get_result();

if ($alumniResult->num_rows === 0) {
    echo json_encode([]);
    exit;
}

$alumni = $alumniResult->fetch_assoc();
$alumni_id = $alumni['alumni_id'];

$expQuery = $conn->prepare("SELECT * FROM alumni_work_experience WHERE alumni_id = ? ORDER BY start_date DESC");
$expQuery->bind_param("i", $alumni_id);
$expQuery->execute();
$expResult = $expQuery->get_result();

$experiences = [];
while ($row = $expResult->fetch_assoc()) {
    $experiences[] = $row;
}

echo json_encode($experiences);
