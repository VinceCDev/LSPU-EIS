<?php

require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance()->getConnection();

// Function to send email asynchronously
function sendEmailAsync($emailData) {
    $command = "php " . __DIR__ . "send_email_background.php " . 
               escapeshellarg(base64_encode(json_encode($emailData))) . " > /dev/null 2>&1 &";
    pclose(popen($command, 'r'));
}

if ($method === 'POST') {
    // Add new alumni
    $data = json_decode(file_get_contents('php://input'), true);
    $required = ['email', 'first_name', 'last_name', 'gender', 'year_graduated', 'college', 'course', 'province', 'city', 'status'];
    
    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required field: '.$field]);
            exit;
        }
    }

    $email = $data['email'];
    $secondary_email = $data['secondary_email'] ?? null;
    $first_name = $data['first_name'];
    $middle_name = $data['middle_name'] ?? '';
    $last_name = $data['last_name'];
    $gender = $data['gender'];
    $year_graduated = $data['year_graduated'];
    $college = $data['college'];
    $course = $data['course'];
    $province = $data['province'];
    $city = $data['city'];
    $status = $data['status'];

    // Start transaction for database operations
    $db->begin_transaction();

    try {
        // Generate random password
        $random_password = bin2hex(random_bytes(4));
        $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);

        // Insert into user table
        $stmt = $db->prepare("INSERT INTO user (email, secondary_email, password, user_role, status) VALUES (?, ?, ?, 'alumni', ?)");
        $stmt->bind_param('ssss', $email, $secondary_email, $hashed_password, $status);
        
        if (!$stmt->execute()) {
            throw new Exception('User insert failed: '.$stmt->error);
        }
        
        $user_id = $stmt->insert_id;
        $stmt->close();

        // Insert into alumni table
        $stmt = $db->prepare('INSERT INTO alumni (user_id, first_name, middle_name, last_name, gender, year_graduated, college, course, province, city) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('isssssssss', $user_id, $first_name, $middle_name, $last_name, $gender, $year_graduated, $college, $course, $province, $city);
        
        if (!$stmt->execute()) {
            throw new Exception('Alumni insert failed: '.$stmt->error);
        }
        
        $stmt->close();

        // Commit transaction
        $db->commit();

        // Prepare email data for async sending
        $emailData = [
            'email' => $email,
            'secondary_email' => $secondary_email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'random_password' => $random_password
        ];

        // Send email asynchronously (non-blocking)
        sendEmailAsync($emailData);

        echo json_encode(['success' => true, 'message' => 'Alumni added successfully. Email will be sent shortly.']);
        
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    
    exit;

} elseif ($method === 'PUT') {
    // Edit alumni - optimized
    $data = json_decode(file_get_contents('php://input'), true);
    $alumni_id = $data['alumni_id'] ?? null;
    
    if (!$alumni_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing alumni_id']);
        exit;
    }

    // Validate alumni exists first
    $stmt = $db->prepare('SELECT a.alumni_id, u.user_id FROM alumni a JOIN user u ON a.user_id = u.user_id WHERE a.alumni_id = ?');
    $stmt->bind_param('i', $alumni_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Alumni not found']);
        exit;
    }
    $stmt->close();

    $fields = ['first_name', 'middle_name', 'last_name', 'email', 'secondary_email', 'gender', 'year_graduated', 'college', 'course', 'province', 'city', 'status'];
    $set = [];
    $params = [];
    $types = '';
    
    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $set[] = "$field = ?";
            $params[] = $data[$field];
            $types .= 's';
        }
    }
    
    if (empty($set)) {
        echo json_encode(['success' => true, 'message' => 'No fields to update']);
        exit;
    }
    
    $params[] = $alumni_id;
    $types .= 'i';
    
    $sql = 'UPDATE alumni a JOIN user u ON a.user_id = u.user_id SET '.implode(',', $set).' WHERE a.alumni_id = ?';
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if (!$stmt->execute()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Update failed: '.$stmt->error]);
        exit;
    }
    
    $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Alumni updated successfully.']);
    exit;

} elseif ($method === 'DELETE') {
    // Delete alumni - optimized with transaction
    $data = json_decode(file_get_contents('php://input'), true);
    $alumni_id = $data['alumni_id'] ?? null;
    
    if (!$alumni_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing alumni_id']);
        exit;
    }

    $db->begin_transaction();
    
    try {
        // Get user_id
        $stmt = $db->prepare('SELECT user_id FROM alumni WHERE alumni_id = ?');
        $stmt->bind_param('i', $alumni_id);
        $stmt->execute();
        $stmt->bind_result($user_id);
        
        if (!$stmt->fetch()) {
            throw new Exception('Alumni not found');
        }
        $stmt->close();

        // Delete alumni
        $stmt = $db->prepare('DELETE FROM alumni WHERE alumni_id = ?');
        $stmt->bind_param('i', $alumni_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to delete alumni record');
        }
        $stmt->close();

        // Delete user
        $stmt = $db->prepare('DELETE FROM user WHERE user_id = ?');
        $stmt->bind_param('i', $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to delete user record');
        }
        $stmt->close();

        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Alumni deleted successfully.']);
        
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    
    exit;

} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}