<?php
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

function getField($arr, $key) {
    return isset($arr[$key]) ? $arr[$key] : '';
}

if ($method === 'POST') {
    // Add or update employer/company (FormData)
    $fields = [
        'company_name', 'company_location', 'contact_email', 'contact_number',
        'industry_type', 'nature_of_business', 'tin', 'date_established',
        'company_type', 'accreditation_status', 'email'
    ];
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = getField($_POST, $field);
    }
    // Validate required fields
    foreach ($fields as $field) {
        if ($data[$field] === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required field: ' . $field]);
            exit;
        }
    }
    $is_update = isset($_POST['id']) && $_POST['id'] !== '';
    if ($is_update) {
        $employer_id = $_POST['id'];
        // Get user_id from employer table
        $stmt = $db->prepare('SELECT user_id FROM employer WHERE user_id = ?');
        $stmt->bind_param('i', $employer_id);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Employer not found.']);
            exit;
        }
        // Only check for duplicate email if the email is being changed
        $current_email = '';
        $stmt = $db->prepare('SELECT email FROM user WHERE user_id = ?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($current_email);
        $stmt->fetch();
        $stmt->close();
        if ($data['email'] !== $current_email) {
            // Check if new email is already taken by another user
            $stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
            $stmt->bind_param('s', $data['email']);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Email already registered.']);
                exit;
            }
            $stmt->close();
        }
        // Continue to update logic (handled below)
    } else {
        // Insert: check if email already exists
        $stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $data['email']);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already registered.']);
            exit;
        }
        $stmt->close();
    }
    // Handle logo upload
    $company_logo = '';
    if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
        $logo_dir = '../uploads/logos/';
        if (!is_dir($logo_dir)) mkdir($logo_dir, 0777, true);
        $logo_name = uniqid() . '_' . basename($_FILES['company_logo']['name']);
        $logo_path = $logo_dir . $logo_name;
        if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $logo_path)) {
            $company_logo = $logo_name;
        }
    }
    // Handle document upload
    $document_file = '';
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
        $doc_dir = '../uploads/documents/';
        if (!is_dir($doc_dir)) mkdir($doc_dir, 0777, true);
        $doc_name = uniqid() . '_' . basename($_FILES['document_file']['name']);
        $doc_path = $doc_dir . $doc_name;
        if (move_uploaded_file($_FILES['document_file']['tmp_name'], $doc_path)) {
            $document_file = $doc_name;
        }
    }
    if (!$is_update) {
        // Generate random password
        $random_password = bin2hex(random_bytes(4));
        $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);
        // Insert into user table (add type = 'email')
        $stmt = $db->prepare('INSERT INTO user (email, password, user_role, status) VALUES (?, ?, "employer", "Active")');
        $stmt->bind_param('ss', $data['email'], $hashed_password);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'User insert failed: ' . $stmt->error]);
            exit;
        }
        $user_id = $stmt->insert_id;
        $stmt->close();
        // Insert into employer table
        $stmt = $db->prepare('INSERT INTO employer (user_id, company_name, company_logo, company_location, contact_email, contact_number, industry_type, nature_of_business, tin, date_established, company_type, accreditation_status, document_file) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('issssssssssss', $user_id, $data['company_name'], $company_logo, $data['company_location'], $data['contact_email'], $data['contact_number'], $data['industry_type'], $data['nature_of_business'], $data['tin'], $data['date_established'], $data['company_type'], $data['accreditation_status'], $document_file);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Employer insert failed: ' . $stmt->error]);
            exit;
        }
        $stmt->close();
        // Send email with credentials (unchanged)
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'lspueis@gmail.com';
            $mail->Password = 'afbp fcwf oujr yqzr';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('lspueis@gmail.com', 'LSPU EIS');
            $mail->addAddress($data['email']);
            $mail->isHTML(true);
            $mail->Subject = 'Your Employer Account for LSPU EIS';
            $mail->Body = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Employer Account Created</title><style>body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; } .container { max-width: 600px; margin: 0 auto; padding: 20px; } .header { background: linear-gradient(135deg, #00A0E9 0%, #1A1A1A 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; } .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; } .button { display: inline-block; background: #00A0E9; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; } .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; } .highlight { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }</style></head><body><div class="container"><div class="header"><h1>🎓 LSPU EIS</h1><p>Laguna State Polytechnic University</p><p>Employment Information System</p></div><div class="content"><h2>Welcome, ' . htmlspecialchars($data['company_name']) . '!</h2><p>Your employer account has been <strong>created by the administrator</strong>.</p><div class="highlight"><h3>Login Credentials</h3><ul><li><strong>Email:</strong> ' . htmlspecialchars($data['email']) . '</li><li><strong>Password:</strong> ' . $random_password . '</li></ul></div><p><strong>Login to your account:</strong></p><a href="http://localhost/lspu_eis/login.php" class="button">Login to LSPU EIS</a><p>For security, please change your password after logging in for the first time.</p><p>If you have any questions or need assistance, please contact the LSPU EIS support team.</p><p>Best regards,<br><strong>LSPU EIS Team</strong></p></div><div class="footer"><p>This is an automated message from the LSPU Employment Information System.</p><p>© 2024 Laguna State Polytechnic University. All rights reserved.</p></div></div></body></html>';
            $mail->AltBody = "Welcome, {$data['company_name']}!\n\nYour employer account has been created by the administrator.\n\nLogin: http://localhost/lspu_eis/login.php\nEmail: {$data['email']}\nPassword: $random_password\n\nBest regards,\nLSPU EIS Team";
            $mail->send();
        } catch (Exception $e) {}
        echo json_encode(['success' => true, 'message' => 'Employer added and credentials sent via email.']);
        exit;
    }
    // If update, handled below (existing logic)
}

if ($method === 'POST' && isset($_POST['id'])) {
    // Update employer/company (FormData)
    $id = $_POST['id'];
    // Get user_id from employer table
    $stmt = $db->prepare('SELECT user_id FROM employer WHERE user_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Employer not found.']);
        exit;
    }
    // Update user table (email)
    if (isset($_POST['email']) && $_POST['email'] !== '') {
        $stmt = $db->prepare('UPDATE user SET email = ? WHERE user_id = ?');
        $stmt->bind_param('si', $_POST['email'], $user_id);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Failed to update user: ' . $stmt->error]);
            exit;
        }
        $stmt->close();
    }
    // Update employer table
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
    // Handle logo upload
    if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
        $logo_dir = '../uploads/logos/';
        if (!is_dir($logo_dir)) mkdir($logo_dir, 0777, true);
        $logo_name = uniqid() . '_' . basename($_FILES['company_logo']['name']);
        $logo_path = $logo_dir . $logo_name;
        if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $logo_path)) {
            $set[] = "company_logo = ?";
            $params[] = $logo_name;
            $types .= 's';
        }
    }
    // Handle document upload
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
        $doc_dir = '../uploads/documents/';
        if (!is_dir($doc_dir)) mkdir($doc_dir, 0777, true);
        $doc_name = uniqid() . '_' . basename($_FILES['document_file']['name']);
        $doc_path = $doc_dir . $doc_name;
        if (move_uploaded_file($_FILES['document_file']['tmp_name'], $doc_path)) {
            $set[] = "document_file = ?";
            $params[] = $doc_name;
            $types .= 's';
        }
    }
    if (empty($set)) {
        echo json_encode(['success' => false, 'message' => 'No fields to update.']);
        exit;
    }
    $params[] = $user_id;
    $types .= 'i';
    $sql = "UPDATE employer SET ".implode(',', $set)." WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Company updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update company: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

if ($method === 'PUT') {
    // Update employer/company (JSON)
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Missing company ID.']);
        exit;
    }
    $fields = [
        'company_name', 'company_location', 'contact_email', 'contact_number',
        'industry_type', 'nature_of_business', 'tin', 'date_established',
        'company_type', 'accreditation_status', 'status'
    ];
    $set = [];
    $params = [];
    $types = '';
    foreach ($fields as $field) {
        if (isset($input[$field])) {
            $set[] = "$field = ?";
            $params[] = $input[$field];
            $types .= 's';
        }
    }
    if (empty($set)) {
        echo json_encode(['success' => false, 'message' => 'No fields to update.']);
        exit;
    }
    $params[] = $id;
    $types .= 'i';
    $sql = "UPDATE employer SET ".implode(',', $set)." WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Company updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update company: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

if ($method === 'DELETE') {
    // Delete employer/company
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Missing company ID.']);
        exit;
    }
    $stmt = $db->prepare('DELETE FROM employer WHERE user_id = ?');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Company deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete company: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method.']); 