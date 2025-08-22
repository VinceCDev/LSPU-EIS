<?php
session_start();
require_once '../conn/db_conn.php';

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
    $_SESSION['user_id'] = $user_id;
}

if (!$user_id) {
    echo json_encode(['savedJobIds' => []]);
    exit;
}

$stmt = $db->prepare('SELECT job_id FROM saved_jobs WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$saved = [];
while ($row = $result->fetch_assoc()) {
    $saved[] = $row['job_id'];
}
$stmt->close();

echo json_encode(['savedJobIds' => $saved]);
