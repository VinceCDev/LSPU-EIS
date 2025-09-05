<?php
session_start();
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'alumni') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];

// Get alumni_id from alumni table using email
$stmt = $db->prepare('SELECT a.alumni_id FROM alumni a JOIN user u ON a.user_id = u.user_id WHERE u.email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($alumni_id);
$stmt->fetch();
$stmt->close();

if (!$alumni_id) {
    echo json_encode(['success' => false, 'message' => 'Alumni not found']);
    exit();
}

$job_id = $_POST['job_id'] ?? null;
if (!$job_id) {
    echo json_encode(['success' => false, 'message' => 'Job ID required']);
    exit();
}

// Prevent duplicate applications
$stmt = $db->prepare('SELECT application_id FROM applications WHERE alumni_id = ? AND job_id = ?');
$stmt->bind_param('ii', $alumni_id, $job_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Already applied']);
    exit();
}
$stmt->close();

// Insert application
$stmt = $db->prepare('INSERT INTO applications (alumni_id, job_id) VALUES (?, ?)');
$stmt->bind_param('ii', $alumni_id, $job_id);
if ($stmt->execute()) {
    // Insert notification for the user
    // Get user_id from alumni_id
    $stmt_user = $db->prepare('SELECT user_id FROM alumni WHERE alumni_id = ? LIMIT 1');
    $stmt_user->bind_param('i', $alumni_id);
    $stmt_user->execute();
    $stmt_user->bind_result($user_id);
    $stmt_user->fetch();
    $stmt_user->close();
    if ($user_id) {
        // Get job title for details
        $stmt_job = $db->prepare('SELECT title FROM jobs WHERE job_id = ? LIMIT 1');
        $stmt_job->bind_param('i', $job_id);
        $stmt_job->execute();
        $stmt_job->bind_result($job_title);
        $stmt_job->fetch();
        $stmt_job->close();
        $notif_message = 'You successfully applied for a job.';
        $notif_details = 'You have applied for the position of ' . ($job_title ?: 'the job') . '. Please wait for further announcement.';
        $stmt_notif = $db->prepare('INSERT INTO notifications (user_id, type, message, details, job_id) VALUES (?, ?, ?, ?, ?)');
        $notif_type = 'application';
        $stmt_notif->bind_param('isssi', $user_id, $notif_type, $notif_message, $notif_details, $job_id);
        $stmt_notif->execute();
        $stmt_notif->close();
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to apply']);
}
$stmt->close(); 