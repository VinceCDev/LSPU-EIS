<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = Database::getInstance()->getConnection();

$user_role = $_POST['user_role'] ?? '';
$email = $_POST['email'] ?? '';
$status = $_POST['status'] ?? 'Active';
$profile_pic = null;

// Generate random password
function generateRandomPassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}
$plain_password = generateRandomPassword(10);
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

// Handle profile picture upload
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $upload_dir = '../uploads/';
    if ($user_role === 'alumni') {
        $upload_dir .= 'profile_picture/';
    } elseif ($user_role === 'employer') {
        $upload_dir .= 'logos/';
    } else {
        $upload_dir .= 'profile_picture/';
    }
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $target = $upload_dir . $filename;
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
        $profile_pic = $filename;
    }
}

// Insert into user table
$stmt = $db->prepare('INSERT INTO user (email, password, user_role, status) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $email, $hashed_password, $user_role, $status);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to insert user: ' . $stmt->error]);
    exit();
}
$user_id = $stmt->insert_id;
$stmt->close();

if ($user_role === 'admin') {
    $first_name = $_POST['first_name'] ?? '';
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $stmt = $db->prepare('INSERT INTO administrator (user_id, first_name, middle_name, last_name, profile_pic) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('issss', $user_id, $first_name, $middle_name, $last_name, $profile_pic);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to insert admin: ' . $stmt->error]);
        exit();
    }
    $stmt->close();
    $recipient_name = $first_name . ' ' . $last_name;
} elseif ($user_role === 'employer') {
    $company_name = $_POST['company_name'] ?? '';
    $industry_type = $_POST['industry_type'] ?? '';
    $stmt = $db->prepare('INSERT INTO employer (user_id, company_name, company_logo, industry_type) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('isss', $user_id, $company_name, $profile_pic, $industry_type);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to insert employer: ' . $stmt->error]);
        exit();
    }
    $stmt->close();
    $recipient_name = $company_name;
} elseif ($user_role === 'alumni') {
    $first_name = $_POST['first_name'] ?? '';
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $stmt = $db->prepare('INSERT INTO alumni (user_id, first_name, middle_name, last_name, profile_pic) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('issss', $user_id, $first_name, $middle_name, $last_name, $profile_pic);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to insert alumni: ' . $stmt->error]);
        exit();
    }
    $stmt->close();
    $recipient_name = $first_name . ' ' . $last_name;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid user role.']);
    exit();
}

// Send email with credentials
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'allencristal12@gmail.com'; // Change to your email
    $mail->Password = 'ugwb vksz wjto zbwf'; // Change to your email password or app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('allencristal12@gmail.com', 'LSPU EIS');
    $mail->addAddress($email, $recipient_name);
    $mail->isHTML(true);
    $mail->Subject = 'Your LSPU EIS Account Credentials';
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
                <h2>Welcome, ' . htmlspecialchars($recipient_name) . '!</h2>
                <p>Your account has been <strong>created successfully</strong>.</p>
                <div class="highlight">
                    <h3>🔑 Your Login Credentials</h3>
                    <ul>
                        <li><strong>Email:</strong> ' . htmlspecialchars($email) . '</li>
                        <li><strong>Password:</strong> ' . htmlspecialchars($plain_password) . '</li>
                    </ul>
                </div>
                <p><strong>Login to your account:</strong></p>
                <a href="http://localhost/lspu_eis/login.php" class="button">Login to LSPU EIS</a>
                <p>For security, please change your password after your first login.</p>
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
    $mail->AltBody = "Welcome, $recipient_name!\n\nYour account has been created successfully.\n\nLogin: http://localhost/lspu_eis/login.php\nEmail: $email\nPassword: $plain_password\n\nFor security, please change your password after your first login.\n\nBest regards,\nLSPU EIS Team";
    $mail->send();
} catch (Exception $e) {
    // Optionally log $mail->ErrorInfo
}

echo json_encode(['success' => true, 'message' => 'Account created successfully.']); 