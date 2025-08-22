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

// ✅ Check if the required field 'experience_id' is provided
if (empty($_POST['experience_id'])) {
    throw new Exception("Missing experience ID.");
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

// ✅ Get alumni_id from the user's profile
$alumniQuery = $conn->prepare("SELECT alumni_id FROM alumni_profile WHERE user_id = ?");
$alumniQuery->bind_param("i", $user_id);
$alumniQuery->execute();
$alumniResult = $alumniQuery->get_result();

if ($alumniResult->num_rows === 0) {
    throw new Exception("Alumni profile not found.");
}

$alumniRow = $alumniResult->fetch_assoc();
$alumni_id = $alumniRow['alumni_id'];

// ✅ Get the experience_id from POST data
$experience_id = $_POST['experience_id'];

// ✅ Check if the experience belongs to the logged-in alumni
$experienceQuery = $conn->prepare("SELECT * FROM alumni_work_experience WHERE alumni_id = ? AND id = ?");
$experienceQuery->bind_param("ii", $alumni_id, $experience_id);
$experienceQuery->execute();
$experienceResult = $experienceQuery->get_result();

if ($experienceResult->num_rows === 0) {
    throw new Exception("Experience not found or does not belong to the user.");
}

// ✅ Delete the experience from the database
$deleteQuery = $conn->prepare("DELETE FROM alumni_work_experience WHERE id = ?");
$deleteQuery->bind_param("i", $experience_id);

if ($deleteQuery->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Work experience deleted successfully.']);
} else {
    throw new Exception("Error deleting data: " . $deleteQuery->error);
}

// ✅ Close the prepared statements and connection
$experienceQuery->close();
$deleteQuery->close();
$conn->close();
