<?php
session_start();
require_once '../conn/db_conn.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
if (!$job_id) {
    echo json_encode(['success' => false, 'message' => 'No job_id provided.']);
    exit;
}

$db = Database::getInstance()->getConnection();
$stmt = $db->prepare('DELETE FROM saved_jobs WHERE user_id = ? AND job_id = ?');
$stmt->bind_param('ii', $user_id, $job_id);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Job unsaved successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to unsave job.']);
} 