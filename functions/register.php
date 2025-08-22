<?php
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Defensive: Required fields
$required_fields = [
    'email', 'password', 'current_password', 'first_name', 'last_name', 'birthdate', 'contact', 'gender', 'civil_status', 'city', 'province', 'year_graduated', 'college', 'course'
];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || $_POST[$field] === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required field: ' . $field]);
        exit;
    }
}

$email = $_POST['email'] ?? '';
$secondary_email = $_POST['secondary_email'] ?? null;
$password = $_POST['password'] ?? '';
$first_name = $_POST['first_name'] ?? '';
$middle_name = $_POST['middle_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$birthdate = $_POST['birthdate'] ?? '';
$contact = $_POST['contact'] ?? '';
$gender = $_POST['gender'] ?? '';
$civil_status = $_POST['civil_status'] ?? '';
$city = $_POST['city'] ?? '';
$province = $_POST['province'] ?? '';
$year_graduated = $_POST['year_graduated'] ?? '';
$college = $_POST['college'] ?? '';
$course = $_POST['course'] ?? '';

// Defensive: File upload check
if (!isset($_FILES['verification_documents']) || !is_uploaded_file($_FILES['verification_documents']['tmp_name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No document uploaded.']);
    exit;
}

// 2. Handle file upload
$upload_dir = '../uploads/documents/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
$doc_name = uniqid() . '_' . basename($_FILES['verification_documents']['name']);
$target_file = $upload_dir . $doc_name;
if (!move_uploaded_file($_FILES['verification_documents']['tmp_name'], $target_file)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Document upload failed.']);
    exit;
}

// 3. Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 4. Insert into user table
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("INSERT INTO user (email, secondary_email, password, user_role, status) VALUES (?, ?, ?, 'alumni', 'Pending')");
$stmt->bind_param('sss', $email, $secondary_email, $hashed_password);
if (!$stmt->execute()) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User registration failed: ' . $stmt->error]);
    exit;
}
$user_id = $stmt->insert_id;
$stmt->close();

// 5. Insert into alumni table
$stmt = $db->prepare("INSERT INTO alumni (user_id, first_name, middle_name, last_name, birthdate, contact, gender, civil_status, city, province, year_graduated, college, course, verification_document) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('isssssssssssss', $user_id, $first_name, $middle_name, $last_name, $birthdate, $contact, $gender, $civil_status, $city, $province, $year_graduated, $college, $course, $doc_name);
if (!$stmt->execute()) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Alumni registration failed: ' . $stmt->error]);
    exit;
}
$stmt->close();

// 6. Send email notification
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Change as needed
    $mail->SMTPAuth = true;
    $mail->Username = 'allencristal12@gmail.com'; // Change to your email
    $mail->Password = 'ugwb vksz wjto zbwf'; // Change to your email password or app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('allencristal12@gmail.com', 'LSPU EIS');
    $mail->addAddress($email, $first_name . ' ' . $last_name);
    if ($secondary_email) {
        $mail->addAddress($secondary_email, $first_name . ' ' . $last_name);
    }

    $mail->isHTML(true);
    $mail->Subject = 'Welcome to LSPU EIS Alumni Portal';
    $mail->Body    = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Welcome to LSPU EIS</title>
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
                <p>Your alumni account has been <strong>created successfully</strong>.</p>
                <div class="highlight">
                    <h3>📋 Next Steps</h3>
                    <ul>
                        <li>We will verify your documents and notify you once your account is fully activated.</li>
                        <li>You will receive another email when your account is approved.</li>
                    </ul>
                </div>
                <p><strong>Login to your account:</strong></p>
                <a href="http://localhost/lspu_eis/login" class="button">Login to LSPU EIS</a>
                <p>If you have any questions or need assistance, please contact the LSPU EIS support team.</p>
                <p>Best regards,<br>
                <strong>LSPU EIS Team</strong></p>
            </div>
            <div class="footer">
                <p>This is an automated message from the LSPU Employment Information System.</p>
                <p>© 2024 Laguna State Polytechnic University. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>';
    $mail->AltBody = "Welcome, $first_name!\n\nYour alumni account has been created successfully. We will verify your documents and notify you once your account is fully activated.\n\nLogin: http://localhost/lspu_eis/login.php\n\nBest regards,\nLSPU EIS Team";
    $mail->send();
} catch (Exception $e) {
    // Optionally log $mail->ErrorInfo
}

// 7. Return success JSON
http_response_code(200);
echo json_encode(['success' => true, 'message' => 'Registration successful! Please check your email for confirmation.']);
exit;
