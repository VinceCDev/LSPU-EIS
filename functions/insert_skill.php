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

$name = $_POST['name'] ?? '';
$certificate = $_FILES['certificate_file'] ?? null;
$certificate_text = $_POST['certificate'] ?? '';

if (!$name) {
    echo json_encode(['success' => false, 'message' => 'Skill name is required.']);
    exit;
}

// Handle file upload
$certificate_filename = null;
if ($certificate && $certificate['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/certificates/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_extension = pathinfo($certificate['name'], PATHINFO_EXTENSION);
    $certificate_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $certificate_filename;
    
    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($file_extension), $allowed_extensions)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDF, JPG, PNG, and GIF are allowed.']);
        exit;
    }
    
    if ($certificate['size'] > 5 * 1024 * 1024) { // 5MB limit
        echo json_encode(['success' => false, 'message' => 'File size too large. Maximum 5MB allowed.']);
        exit;
    }
    
    if (!move_uploaded_file($certificate['tmp_name'], $upload_path)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload file.']);
        exit;
    }
}

$stmt = $db->prepare('INSERT INTO alumni_skill (alumni_id, name, certificate, certificate_file) VALUES (?, ?, ?, ?)');
$stmt->bind_param('isss', $alumni_id, $name, $certificate_text, $certificate_filename);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'skill_id' => $stmt->insert_id,
        'certificate_file' => $certificate_filename
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Insert failed: '.$stmt->error]);
}

$stmt->close();
exit;
?>