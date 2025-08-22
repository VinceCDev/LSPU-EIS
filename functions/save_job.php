<?php
session_start();
require_once '../conn/db_conn.php';

// Get user_id from session or fetch using email
$db = Database::getInstance()->getConnection();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!$user_id && isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    // Optionally set user_id in session for future requests
    $_SESSION['user_id'] = $user_id;
}

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : 'save';

if (!$job_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid job ID']);
    exit;
}

if ($action === 'save') {
    $stmt = $db->prepare("INSERT IGNORE INTO saved_jobs (user_id, job_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $job_id);
    $success = $stmt->execute();
    echo json_encode(['success' => $success, 'message' => $success ? 'Job saved!' : 'Failed to save job']);
} else if ($action === 'unsave') {
    $stmt = $db->prepare("DELETE FROM saved_jobs WHERE user_id = ? AND job_id = ?");
    $stmt->bind_param("ii", $user_id, $job_id);
    $success = $stmt->execute();
    echo json_encode(['success' => $success, 'message' => $success ? 'Job unsaved!' : 'Failed to unsave job']);
}
