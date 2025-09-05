<?php
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance()->getConnection();

if ($method === 'POST') {
    // Add new alumni
    $data = json_decode(file_get_contents('php://input'), true);
    $required = ['email', 'first_name', 'last_name', 'gender', 'year_graduated', 'college', 'course', 'province', 'city', 'status'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required field: ' . $field]);
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

    // Generate random password
    $random_password = bin2hex(random_bytes(4)); // 8 hex chars
    $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);

    // Insert into user table
    $stmt = $db->prepare("INSERT INTO user (email, secondary_email, password, user_role, status) VALUES (?, ?, ?, 'alumni', ?)");
    $stmt->bind_param('ssss', $email, $secondary_email, $hashed_password, $status);
    if (!$stmt->execute()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User insert failed: ' . $stmt->error]);
        exit;
    }
    $user_id = $stmt->insert_id;
    $stmt->close();
    // Insert into alumni table
    $stmt = $db->prepare("INSERT INTO alumni (user_id, first_name, middle_name, last_name, gender, year_graduated, college, course, province, city) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isssssssss', $user_id, $first_name, $middle_name, $last_name, $gender, $year_graduated, $college, $course, $province, $city);
    if (!$stmt->execute()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Alumni insert failed: ' . $stmt->error]);
        exit;
    }
    $stmt->close();

    // Send email to both emails
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lspueis@gmail.com';
        $mail->Password = 'afbp fcwf oujr yqzr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('lspueis@gmail.com', 'LSPU EIS System');
        $mail->addAddress($email, $first_name . ' ' . $last_name);
        if ($secondary_email) {
            $mail->addAddress($secondary_email, $first_name . ' ' . $last_name);
        }
        $mail->isHTML(true);
        $mail->Subject = 'Your LSPU EIS Alumni Account (Auto-Approved)';
        $mail->Body = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Account Approved</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #00A0E9 0%, #1A1A1A 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: #00A0E9; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                .highlight { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎓 LSPU EIS</h1>
                    <p>Laguna State Polytechnic University</p>
                    <p>Employment Information System</p>
                </div>
                <div class="content">
                    <h2>Welcome, ' . htmlspecialchars($first_name) . '!</h2>
                    <p>Your alumni account has been <strong>created and approved</strong> by the LSPU EIS Administrator.</p>
                    <div class="highlight">
                        <h3>🔑 Login Credentials</h3>
                        <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                        <p><strong>Password:</strong> ' . $random_password . '</p>
                        <p><strong>Login link:</strong> <a href="http://localhost/lspu_eis/login">http://localhost/lspu_eis/login</a></p>
                    </div>
                    <p><strong>Next Steps:</strong></p>
                    <ul>
                        <li>Login and change your password immediately for security.</li>
                        <li>Complete your profile and upload your resume.</li>
                        <li>Browse and apply for job opportunities.</li>
                    </ul>
                    <p>If you have any questions or need assistance, please contact the LSPU EIS support team.</p>
                    <p>Best regards,<br>
                    <strong>LSPU EIS Team</strong></p>
                </div>
                <div class="footer" style="text-align: center;">
                    <p>This is an automated message from the LSPU Employment Information System.</p>
                    <p>© 2024 Laguna State Polytechnic University. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
        $mail->AltBody = "Welcome, $first_name!\n\nYour alumni account has been created and approved.\n\nLogin: http://localhost/lspu_eis/login.php\nEmail: $email\nPassword: $random_password\n\nBest regards,\nLSPU EIS Team";
        $mail->send();
    } catch (Exception $e) {
        // Optionally log $mail->ErrorInfo
    }
    echo json_encode(['success' => true, 'message' => 'Alumni added successfully.']);
    exit;
} elseif ($method === 'PUT') {
    // Edit alumni
    $data = json_decode(file_get_contents('php://input'), true);
    $alumni_id = $data['alumni_id'] ?? null;
    if (!$alumni_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing alumni_id']);
        exit;
    }
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
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        exit;
    }
    $params[] = $alumni_id;
    $types .= 'i';
    $sql = "UPDATE alumni a JOIN user u ON a.user_id = u.user_id SET ".implode(',', $set)." WHERE a.alumni_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
        exit;
    }
    $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Alumni updated successfully.']);
    exit;
} elseif ($method === 'DELETE') {
    // Delete alumni
    $data = json_decode(file_get_contents('php://input'), true);
    $alumni_id = $data['alumni_id'] ?? null;
    if (!$alumni_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing alumni_id']);
        exit;
    }
    // Get user_id
    $stmt = $db->prepare("SELECT user_id FROM alumni WHERE alumni_id = ?");
    $stmt->bind_param('i', $alumni_id);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Alumni not found']);
        exit;
    }
    // Delete alumni
    $stmt = $db->prepare("DELETE FROM alumni WHERE alumni_id = ?");
    $stmt->bind_param('i', $alumni_id);
    $stmt->execute();
    $stmt->close();
    // Delete user
    $stmt = $db->prepare("DELETE FROM user WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Alumni deleted successfully.']);
    exit;
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
} 