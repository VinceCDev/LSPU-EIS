<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'alumni') {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];
$stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit();
}
// Collect and sanitize POST data
$fields = [
    'first_name', 'middle_name', 'last_name', 'birthdate', 'contact', 'gender', 'civil_status',
    'city', 'province', 'year_graduated', 'college', 'course', 'email', 'secondary_email'
];
$data = [];
foreach ($fields as $field) {
    $data[$field] = isset($_POST[$field]) ? trim($_POST[$field]) : '';
}
// Update user table (email, secondary_email)
if (!empty($data['email'])) {
    $stmt = $db->prepare('UPDATE user SET email=?, secondary_email=? WHERE user_id=?');
    $stmt->bind_param('ssi', $data['email'], $data['secondary_email'], $user_id);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'User email update failed: ' . $stmt->error]);
        $stmt->close();
        exit();
    }
    $stmt->close();
    // Update session email if changed
    $_SESSION['email'] = $data['email'];
}
// Update alumni table
$stmt = $db->prepare('UPDATE alumni SET first_name=?, middle_name=?, last_name=?, birthdate=?, contact=?, gender=?, civil_status=?, city=?, province=?, year_graduated=?, college=?, course=? WHERE user_id=?');
$stmt->bind_param('ssssssssssssi',
    $data['first_name'],
    $data['middle_name'],
    $data['last_name'],
    $data['birthdate'],
    $data['contact'],
    $data['gender'],
    $data['civil_status'],
    $data['city'],
    $data['province'],
    $data['year_graduated'],
    $data['college'],
    $data['course'],
    $user_id
);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
}
$stmt->close();
exit; 