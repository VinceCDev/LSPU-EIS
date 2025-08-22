<?php
session_start();
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Validate required fields
$required = ['email','password','current_password','company_name','company_location','contact_email','contact_number','industry_type','nature_of_business','tin','date_established','company_type','accreditation_status'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => 'Missing required field: ' . $field]);
        exit;
    }
}
$email = $_POST['email'];
$password = $_POST['password'];
$current_password = $_POST['current_password'];
$company_name = $_POST['company_name'];
$company_location = $_POST['company_location'];
$contact_email = $_POST['contact_email'];
$contact_number = $_POST['contact_number'];
$industry_type = $_POST['industry_type'];
$nature_of_business = $_POST['nature_of_business'];
$tin = $_POST['tin'];
$date_established = $_POST['date_established'];
$company_type = $_POST['company_type'];
$accreditation_status = $_POST['accreditation_status'];

// Password policy
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
    echo json_encode(['success' => false, 'message' => 'Password does not meet the policy.']);
    exit;
}
if ($password !== $current_password) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit;
}

// Check if email already exists
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already registered.']);
    exit;
}
$stmt->close();

// Handle file uploads
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
$document_file = '';
if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
    $doc_dir = '../uploads/documents/';
    if (!is_dir($doc_dir)) mkdir($doc_dir, 0777, true);
    $doc_name = uniqid() . '_' . basename($_FILES['document_file']['name']);
    $doc_path = $doc_dir . $doc_name;
    if (move_uploaded_file($_FILES['document_file']['tmp_name'], $doc_path)) {
        $document_file = $doc_name;
    } else {
        echo json_encode(['success' => false, 'message' => 'Document upload failed.']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Document file is required.']);
    exit;
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into user table
$stmt = $db->prepare('INSERT INTO user (email, password, user_role, status) VALUES (?, ?, "employer", "Pending")');
$stmt->bind_param('ss', $email, $hashed_password);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'User registration failed: ' . $stmt->error]);
    exit;
}
$user_id = $stmt->insert_id;
$stmt->close();

// Insert into employer table
$stmt = $db->prepare('INSERT INTO employer (user_id, company_name, company_logo, company_location, contact_email, contact_number, industry_type, nature_of_business, tin, date_established, company_type, accreditation_status, document_file) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->bind_param('issssssssssss', $user_id, $company_name, $company_logo, $company_location, $contact_email, $contact_number, $industry_type, $nature_of_business, $tin, $date_established, $company_type, $accreditation_status, $document_file);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Employer registration failed: ' . $stmt->error]);
    exit;
}
$stmt->close();

// Send confirmation email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'allencristal12@gmail.com';
    $mail->Password = 'ugwb vksz wjto zbwf';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->setFrom('allencristal12@gmail.com', 'LSPU EIS');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Welcome to LSPU EIS Employer Portal';
    $mail->Body = '
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
                <h2>Welcome, ' . htmlspecialchars($company_name) . '!</h2>
                <p>Your employer account has been <strong>created successfully</strong> and is pending approval by the admin.</p>
                <div class="highlight">
                    <h3>📋 Next Steps</h3>
                    <ul>
                        <li>We will review your documents and notify you once your account is approved.</li>
                        <li>You will receive another email when your account is approved.</li>
                    </ul>
                </div>
                <p><strong>Login to your account:</strong></p>
                <a href="http://localhost/lspu_eis/employer_login.php" class="button">Login to LSPU EIS</a>
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
    $mail->AltBody = "Welcome, $company_name!\n\nYour employer account has been created successfully. We will review your documents and notify you once your account is approved.\n\nLogin: http://localhost/lspu_eis/employer_login.php\n\nBest regards,\nLSPU EIS Team";
    $mail->send();
} catch (Exception $e) {}
echo json_encode(['success' => true, 'message' => 'Registration successful! Please wait for admin approval. Check your email for confirmation.']);
exit;
