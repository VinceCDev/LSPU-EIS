<?php
require_once '../conn/db_conn.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode([]);
    exit;
}

$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];
$stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($employer_id);
$stmt->fetch();
$stmt->close();
if (!$employer_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT * FROM jobs WHERE employer_id = ? ORDER BY created_at DESC, job_id DESC";
$stmt = $db->prepare($sql);
$stmt->bind_param('i', $employer_id);
$stmt->execute();
$result = $stmt->get_result();
$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}
$stmt->close();
echo json_encode($jobs); 