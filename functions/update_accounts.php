<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = Database::getInstance()->getConnection();
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$user_role = isset($_POST['user_role']) ? $_POST['user_role'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';

if (!$user_id || !$user_role) {
    echo json_encode(['success' => false, 'message' => 'Missing user_id or user_role.']);
    exit();
}

if ($user_role === 'admin') {
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $middle_name = isset($_POST['middle_name']) ? trim($_POST['middle_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $profile_pic = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/profile_picture/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_admin.' . $ext;
        $targetPath = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
            $profile_pic = 'uploads/profile_picture/' . $filename;
        }
    }
    // Update user email
    $stmt = $db->prepare('UPDATE user SET email=? WHERE user_id=?');
    $stmt->bind_param('si', $email, $user_id);
    $stmt->execute();
    $stmt->close();
    // Update admin details
    $query = 'UPDATE administrator SET first_name=?, middle_name=?, last_name=?, status=?';
    $params = [$first_name, $middle_name, $last_name, $status];
    $types = 'ssss';
    if ($profile_pic) {
        $query .= ', profile_pic=?';
        $params[] = $profile_pic;
        $types .= 's';
    }
    $query .= ' WHERE user_id=?';
    $params[] = $user_id;
    $types .= 'i';
    $stmt = $db->prepare($query);
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Admin updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
    exit();
}
if ($user_role === 'employer') {
    $company_name = isset($_POST['company_name']) ? trim($_POST['company_name']) : '';
    $industry_type = isset($_POST['industry_type']) ? trim($_POST['industry_type']) : '';
    $company_logo = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/logos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_employer.' . $ext;
        $targetPath = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
            $company_logo = 'uploads/logos/' . $filename;
        }
    }
    // Update employer details
    $query = 'UPDATE employer SET company_name=?, industry_type=?, status=?';
    $params = [$company_name, $industry_type, $status];
    $types = 'sss';
    if ($company_logo) {
        $query .= ', company_logo=?';
        $params[] = $company_logo;
        $types .= 's';
    }
    $query .= ' WHERE user_id=?';
    $params[] = $user_id;
    $types .= 'i';
    $stmt = $db->prepare($query);
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Employer updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
    exit();
}
if ($user_role === 'alumni') {
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $middle_name = isset($_POST['middle_name']) ? trim($_POST['middle_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $profile_pic = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/profile_picture/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_alumni.' . $ext;
        $targetPath = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
            $profile_pic = 'uploads/profile_picture/' . $filename;
        }
    }
    // Update user email
    $stmt = $db->prepare('UPDATE user SET email=? WHERE user_id=?');
    $stmt->bind_param('si', $email, $user_id);
    $stmt->execute();
    $stmt->close();
    // Update alumni details
    $query = 'UPDATE alumni SET first_name=?, middle_name=?, last_name=?, status=?';
    $params = [$first_name, $middle_name, $last_name, $status];
    $types = 'ssss';
    if ($profile_pic) {
        $query .= ', profile_pic=?';
        $params[] = $profile_pic;
        $types .= 's';
    }
    $query .= ' WHERE user_id=?';
    $params[] = $user_id;
    $types .= 'i';
    $stmt = $db->prepare($query);
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Alumni updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
    exit();
}
echo json_encode(['success' => false, 'message' => 'Invalid user role.']); 