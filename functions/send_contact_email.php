<?php
// Include PHPMailer
require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $input = $_POST;
}

$name = isset($input['name']) ? trim($input['name']) : '';
$age = isset($input['age']) ? trim($input['age']) : '';
$email = isset($input['email']) ? trim($input['email']) : '';
$message = isset($input['message']) ? trim($input['message']) : '';

// Validate required fields
if (empty($name) || empty($age) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Validate age
if (!is_numeric($age) || $age < 1 || $age > 120) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid age (1-120)']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'lspueis@gmail.com';
        $mail->Password = 'afbp fcwf oujr yqzr';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('lspueis@gmail.com', 'LSPU-EIS Contact Form');
    $mail->addAddress('lspueis@gmail.com', 'Allen Cristal');
    $mail->addReplyTo($email, $name);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'LSPU-EIS Contact Form Submission';
    
    // Simple email body
    $htmlBody = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
            .email-content { max-width: 600px; margin: 0 auto; }
            .greeting { margin-bottom: 20px; }
            .message-content { margin-bottom: 20px; }
            .signature { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px; }
        </style>
    </head>
    <body>
        <div class='email-content'>
            <div class='greeting'>
                <p>Dear Sir/Ma'am,</p>
            </div>
            
            <div class='message-content'>
                <p>" . nl2br(htmlspecialchars($message)) . "</p>
            </div>
            
            <div class='signature'>
                <p>Best regards,<br>
                <strong>" . htmlspecialchars($name) . "</strong><br>
                Age: " . htmlspecialchars($age) . "<br>
                Email: " . htmlspecialchars($email) . "</p>
            </div>
        </div>
    </body>
    </html>";

    $mail->Body = $htmlBody;
    
    // Plain text version
    $mail->AltBody = "
    Dear Sir/Ma'am,

    " . $message . "

    Best regards,
    " . $name . "
    Age: " . $age . "
    Email: " . $email;

    // Send email
    $mail->send();
    
    echo json_encode(['success' => true, 'message' => 'Message sent successfully! We will get back to you soon.']);

} catch (Exception $e) {
    error_log("PHPMailer Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again later.']);
}
?> 