<?php

session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'alumni') {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];

// Get alumni_id
$stmt = $db->prepare('SELECT alumni_id FROM alumni a JOIN user u ON a.user_id = u.user_id WHERE u.email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($alumni_id);
$stmt->fetch();
$stmt->close();

if (!$alumni_id) {
    echo json_encode(['success' => false, 'message' => 'Alumni not found.']);
    exit;
}

$stmt = $db->prepare('SELECT skill_id, name, certificate, certificate_file FROM alumni_skill WHERE alumni_id = ?');
$stmt->bind_param('i', $alumni_id);
$stmt->execute();
$result = $stmt->get_result();
$skills = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(['success' => true, 'skills' => $skills]);
$stmt->close();
exit;
