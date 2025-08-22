<?php
// Ensure the session is started
session_start();
require_once '../conn/db_conn.php';

// ✅ Check if the user is logged in
if (!isset($_SESSION['email'])) {
    throw new Exception("User not logged in.");
}

// ✅ Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception("Invalid request method.");
}

// ✅ Check if the required fields are present in the POST data
if (empty($_POST['title']) || empty($_POST['company']) || empty($_POST['start_date'])) {
    throw new Exception("Missing required fields.");
}

// ✅ Get user info using session email
$email = $_SESSION['email'];
$userQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
$userQuery->bind_param("s", $email);
$userQuery->execute();
$userResult = $userQuery->get_result();

if ($userResult->num_rows === 0) {
    throw new Exception("User not found.");
}

$userRow = $userResult->fetch_assoc();
$user_id = $userRow['id'];

// ✅ Get alumni_id
$alumniQuery = $conn->prepare("SELECT alumni_id FROM alumni_profile WHERE user_id = ?");
$alumniQuery->bind_param("i", $user_id);
$alumniQuery->execute();
$alumniResult = $alumniQuery->get_result();

if ($alumniResult->num_rows === 0) {
    throw new Exception("Alumni profile not found.");
}

$alumniRow = $alumniResult->fetch_assoc();
$alumni_id = $alumniRow['alumni_id'];

// ✅ Handle optional fields and current employment
$current = isset($_POST['current']) && $_POST['current'] ? 1 : 0;
$end_date = $current ? null : ($_POST['end_date'] ?? null);

// ✅ Insert into database
$sql = "INSERT INTO alumni_work_experience (
            alumni_id, title, company, company_address, sector, location,
            salary, employment_type, industry, start_date, end_date, description, current
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    throw new Exception("Database prepare failed: " . $conn->error);
}

// ✅ Bind parameters (13 parameters = 13 types)
$stmt->bind_param(
    "isssssssssssi",
    $alumni_id,
    $_POST['title'],
    $_POST['company'],
    $_POST['company_address'],
    $_POST['sector'],
    $_POST['location'],
    $_POST['salary'],
    $_POST['employment_type'],
    $_POST['industry'],
    $_POST['start_date'],
    $end_date,
    $_POST['description'],
    $current
);

// ✅ Execute the query
if ($stmt->execute()) {
    echo "Work experience added successfully!";
} else {
    throw new Exception("Error inserting data: " . $stmt->error);
}

// ✅ Close the statement and connection
$stmt->close();
$conn->close();
