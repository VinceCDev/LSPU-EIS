<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
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
// Get employer_id
$stmt = $db->prepare('SELECT employer_id FROM employer WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($employer_id);
$stmt->fetch();
$stmt->close();
if (!$employer_id) {
    echo json_encode(['success' => false, 'message' => 'Employer not found.']);
    exit();
}
// Handle file uploads
$logo_path = null;
$doc_path = null;
if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION);
    $logo_name = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['company_logo']['name']);
    $logo_path = '../../uploads/logos/' . $logo_name;
    move_uploaded_file($_FILES['company_logo']['tmp_name'], $logo_path);
    $logo_path = $logo_name;
}
if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['document_file']['name'], PATHINFO_EXTENSION);
    $doc_name = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['document_file']['name']);
    $doc_path = '../../uploads/documents/' . $doc_name;
    move_uploaded_file($_FILES['document_file']['tmp_name'], $doc_path);
    $doc_path = $doc_name;
}
// Prepare update fields
$fields = [
    'company_name', 'company_location', 'contact_email', 'contact_number',
    'industry_type', 'nature_of_business', 'tin', 'date_established',
    'company_type', 'accreditation_status'
];
$set = [];
$params = [];
$types = '';
foreach ($fields as $field) {
    if (isset($_POST[$field])) {
        $set[] = "$field = ?";
        $params[] = $_POST[$field];
        $types .= 's';
    }
}
if ($logo_path) {
    $set[] = "company_logo = ?";
    $params[] = $logo_path;
    $types .= 's';
}
if ($doc_path) {
    $set[] = "document_file = ?";
    $params[] = $doc_path;
    $types .= 's';
}
if (empty($set)) {
    echo json_encode(['success' => false, 'message' => 'No data to update.']);
    exit();
}
$sql = "UPDATE employer SET " . implode(', ', $set) . " WHERE employer_id = ?";
$params[] = $employer_id;
$types .= 'i';
$stmt = $db->prepare($sql);
$stmt->bind_param($types, ...$params);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
}
$stmt->close();
exit; 