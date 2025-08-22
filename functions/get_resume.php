<?php
session_start();
include '../conn/db_conn.php'; // your DB connection

if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit;
}

$email = $_SESSION['email']; // Get the logged-in user's email

// Get the user_id from users table
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if (!$user_id) {
    echo json_encode(['error' => 'User not found.']);
    exit;
}

// Get the alumni_id from alumni_profile table
$stmt2 = $conn->prepare("SELECT alumni_id FROM alumni_profile WHERE user_id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$stmt2->bind_result($alumni_id);
$stmt2->fetch();
$stmt2->close();

if (!$alumni_id) {
    echo json_encode(['error' => 'Alumni profile not found.']);
    exit;
}

// Select the resume from alumni_resumes table based on alumni_id
$stmt3 = $conn->prepare("SELECT resume_file FROM alumni_resumes WHERE alumni_id = ?");
$stmt3->bind_param("i", $alumni_id);
$stmt3->execute();
$stmt3->bind_result($resume_file);
$stmt3->fetch();
$stmt3->close();

if ($resume_file) {
    // Return the resume filename and path in JSON format
    echo json_encode(['resume' => $resume_file]);
} else {
    echo json_encode(['resume' => null]);
}
