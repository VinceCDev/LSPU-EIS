<?php
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../conn/db_conn.php';
header('Content-Type: application/json');

// Gemini API function using gemini-2.0-flash and X-goog-api-key header
function call_gemini_api($prompt) {
    $api_key = 'AIzaSyC5LRDr-q8eDfLMFe_QOA8GmLCzF-Qo6dU'; // Replace with your actual key
    $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-goog-api-key: ' . $api_key
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        error_log('Gemini API call failed: No response');
        return [];
    }

    $json = json_decode($response, true);
    if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
        $text = trim($json['candidates'][0]['content']['parts'][0]['text']);
        error_log('Gemini raw response: ' . $text);
        return [$text];
    }
    error_log('Gemini API unexpected response: ' . $response);
    return [];
}

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Create or Update
    $fields = [
        'employer_id', 'title', 'type', 'location', 'salary', 'status',
        'created_at', 'description', 'requirements', 'qualifications', 'employer_question'
    ];
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? '';
    }
    foreach ($fields as $field) {
        if ($data[$field] === '') {
            echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
            exit;
        }
    }
    if (isset($_POST['job_id']) && $_POST['job_id'] !== '') {
        // Update
        $job_id = $_POST['job_id'];
        $stmt = $db->prepare("UPDATE jobs SET employer_id=?, title=?, type=?, location=?, salary=?, status=?, created_at=?, description=?, requirements=?, qualifications=?, employer_question=? WHERE job_id=?");
        $stmt->bind_param(
            'issssssssssi',
            $data['employer_id'], $data['title'], $data['type'], $data['location'], $data['salary'], $data['status'],
            $data['created_at'], $data['description'], $data['requirements'], $data['qualifications'], $data['employer_question'],
            $job_id
        );
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Job updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        // Create
        $stmt = $db->prepare("INSERT INTO jobs (employer_id, title, type, location, salary, status, created_at, description, requirements, qualifications, employer_question) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            'issssssssss',
            $data['employer_id'], $data['title'], $data['type'], $data['location'], $data['salary'], $data['status'],
            $data['created_at'], $data['description'], $data['requirements'], $data['qualifications'], $data['employer_question']
        );
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Job created successfully.']);
        
            // --- Gemini job match notification logic (per alumni, Match/Not a Match) ---
            $alumni = [];
            $result = $db->query("SELECT a.user_id, a.course, a.alumni_id FROM alumni a");
            while ($row = $result->fetch_assoc()) {
                // Get skills for each alumni
                $skills = [];
                $stmt_skills = $db->prepare("SELECT name FROM alumni_skill WHERE alumni_id = ?");
                $stmt_skills->bind_param('i', $row['alumni_id']);
                $stmt_skills->execute();
                $skill_result = $stmt_skills->get_result();
                while ($skill_row = $skill_result->fetch_assoc()) {
                    $skills[] = $skill_row['name'];
                }
                $stmt_skills->close();
                
                // Debug output for skills fetching
                error_log("Fetched skills for alumni_id {$row['alumni_id']}: " . implode(', ', $skills));
                
                $alumni[] = [
                    'user_id' => $row['user_id'],
                    'course' => $row['course'],
                    'skills' => implode(', ', $skills),
                    'debug_output' => "Alumni ID: {$row['alumni_id']}, Skills: " . implode(', ', $skills) // Added debug output
                ];
            }
            
            // Output the skills data for debugging
            error_log("Alumni skills data: " . print_r($alumni, true));
            
            $job_title = $data['title'];
            $requirements = $data['requirements'];
            $qualifications = $data['qualifications'];
            
            foreach ($alumni as $alum) {
                $skills_text = !empty($alum['skills']) ? "Skills: {$alum['skills']}" : "";
                $prompt = "Based on the following information, determine if this is a 'Match' or 'Not a Match' for the job. 
                          Provide only one of these two phrases as your response, with no additional explanation or text.\n\n
                          Candidate Background:\n
                          College Program: \"{$alum['course']}\"\n
                          Skills: \"{$skills_text}\"\n
                          Job Details:\n
                          Title: \"$job_title\"\n
                          Requirements: \"$requirements\"\n
                          Qualifications: \"$qualifications\"\n\n
                          Response:";
                
                // Debug output for the prompt being sent to Gemini
                error_log("Sending to Gemini for user {$alum['user_id']}:\n$prompt");
                
                $result = call_gemini_api($prompt);
                $match = '';
                if (is_array($result) && count($result) > 0) {
                    $match = trim($result[0]);
                } elseif (is_string($result)) {
                    $match = trim($result);
                }
                
                // Debug output for Gemini response
                error_log("Gemini response for user {$alum['user_id']}: " . $match);
                
                if (stripos($match, 'match') !== false && stripos($match, 'not a match') === false) {
                    $notif_message = "New job matches your profile!";
                    $notif_details = "A new job posting for '$job_title' matches your background. Check it out!";
                    $stmt_notif = $db->prepare('INSERT INTO notifications (user_id, type, message, details) VALUES (?, ?, ?, ?)');
                    $notif_type = 'job_match';
                    $stmt_notif->bind_param('isss', $alum['user_id'], $notif_type, $notif_message, $notif_details);
                    $stmt_notif->execute();
                    $stmt_notif->close();

                    // Get alumni email
                    $stmt_email = $db->prepare("SELECT u.email, u.secondary_email, a.first_name, a.last_name 
                        FROM alumni a 
                        JOIN user u ON a.user_id = u.user_id 
                        WHERE a.user_id = ? LIMIT 1");
                    $stmt_email->bind_param('i', $alum['user_id']);
                    $stmt_email->execute();
                    $stmt_email->bind_result($email, $secondary_email, $first_name, $last_name);
                    $stmt_email->fetch();
                    $stmt_email->close();

                    $recipient = $email ?: $secondary_email;

                    if ($recipient) {
                    try {
                    $mail = new PHPMailer(true);

                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'allencristal12@gmail.com'; // Change to your email
                    $mail->Password = 'ugwb vksz wjto zbwf'; // Change to your app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('allencristal12@gmail.com', 'LSPU EIS');
                    $mail->addAddress($recipient);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = "New Job Match: $job_title";
                    $mail->Body = "
                        <!DOCTYPE html>
                        <html lang='en'>
                        <head>
                            <meta charset='UTF-8'>
                            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                            <title>New Job Match Notification</title>
                            <style>
                                body { 
                                    font-family: Arial, sans-serif; 
                                    line-height: 1.6; 
                                    color: #333;
                                    margin: 0;
                                    padding: 0;
                                    background-color: #f5f5f5;
                                }
                                .container { 
                                    max-width: 600px; 
                                    margin: 0 auto; 
                                    background: white;
                                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                                }
                                .header { 
                                    background: linear-gradient(135deg, #00A0E9 0%, #1A1A1A 100%); 
                                    color: white; 
                                    padding: 30px; 
                                    text-align: center; 
                                    border-radius: 10px 10px 0 0; 
                                }
                                .content { 
                                    padding: 30px; 
                                    border-radius: 0 0 10px 10px;
                                }
                                .job-match-card {
                                    background: #e8f5e9;
                                    border: 1px solid #c8e6c9;
                                    padding: 20px;
                                    border-radius: 8px;
                                    margin: 20px 0;
                                }
                                .button { 
                                    display: block;
                                    width: fit-content;
                                    background: #00A0E9; 
                                    color: white; 
                                    padding: 12px 30px; 
                                    text-decoration: none; 
                                    border-radius: 5px; 
                                    margin: 25px auto;
                                    font-weight: bold;
                                    text-align: center;
                                    transition: all 0.3s ease;
                                }
                                .button:hover {
                                    background: #0088cc;
                                    transform: translateY(-2px);
                                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                                }
                                .footer { 
                                    text-align: center; 
                                    margin-top: 30px; 
                                    color: #666; 
                                    font-size: 14px;
                                    padding: 20px;
                                    border-top: 1px solid #eee;
                                }
                                .highlight-box {
                                    background: #e3f2fd;
                                    border: 1px solid #bbdefb;
                                    padding: 15px;
                                    border-radius: 8px;
                                    margin: 20px 0;
                                }
                                ul {
                                    padding-left: 20px;
                                }
                                li {
                                    margin-bottom: 8px;
                                }
                                h2 {
                                    color: #00A0E9;
                                    margin-top: 0;
                                }
                                .company-name {
                                    font-weight: bold;
                                    color: #1A1A1A;
                                }
                            </style>
                        </head>
                        <body>
                            <div class='container'>
                                <div class='header'>
                                    <h1>🎓 LSPU EIS</h1>
                                    <p>Laguna State Polytechnic University</p>
                                    <p>Employment Information System</p>
                                </div>
                                
                                <div class='content'>
                                    <h2>Hello {$first_name} {$last_name},</h2>
                                    <p>We're excited to inform you about a new job opportunity that matches your profile!</p>
                                    
                                    <div class='job-match-card'>
                                        <h3 style='margin-top: 0; color: #00A0E9;'>{$job_title}</h3>
                                        
                                        <div class='highlight-box'>
                                            <h4 style='margin-top: 0;'>Job Details:</h4>
                                            <p><strong>Requirements:</strong><br>
                                            {$requirements}</p>
                                            
                                            <p><strong>Qualifications:</strong><br>
                                            {$qualifications}</p>
                                        </div>
                                        
                                        <h4>Why This Matches You:</h4>
                                        <ul>
                                            <li>Your course: <strong>{$alum['course']}</strong></li>
                                            <li>Your skills: <strong>{$alum['skills']}</strong></li>
                                        </ul>
                                    </div>
                                    
                                    <a href='http://localhost/lspu-eis/home' class='button'>View Job Details</a>
                                    
                                    <div style='margin-top: 30px;'>
                                        <p><strong>Next Steps:</strong></p>
                                        <ul>
                                            <li>Review the job details by clicking the button above</li>
                                            <li>Apply directly through our system if interested</li>
                                            <li>Prepare your updated resume and portfolio</li>
                                        </ul>
                                    </div>
                                    
                                    <p style='margin-top: 30px;'>Best regards,<br>
                                    <strong>LSPU Employment Information System Team</strong></p>
                                </div>
                                
                                <div class='footer'>
                                    <p>This is an automated message from the LSPU Employment Information System.</p>
                                    <p>© 2024 Laguna State Polytechnic University. All rights reserved.</p>
                                </div>
                            </div>
                        </body>
                        </html>
                        ";

                        $mail->AltBody = "Hello {$first_name} {$last_name},\n\nWe found a new job posting that matches your profile!\n\nJob Title: {$job_title}\nRequirements: {$requirements}\nQualifications: {$qualifications}\n\nView Job:localhost/lspu-eis/home\n\nBest regards,\nLSPU EIS Team";

                        $mail->send();
                        error_log("Email sent to {$recipient} for job match");
                        } catch (Exception $e) {
                        error_log("Mailer Error: {$mail->ErrorInfo}");
                        }
                    }
                                        
                    // Debug output for successful match
                    error_log("Match found for user {$alum['user_id']} - notification sent");
                }
            }
            // --- End Gemini job match notification logic ---
        } else {
            echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $stmt->error]);
        }
        $stmt->close();
    }
    exit;
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $job_id = $input['job_id'] ?? null;
    if (!$job_id) {
        echo json_encode(['success' => false, 'message' => 'Missing job_id']);
        exit;
    }
    $stmt = $db->prepare("DELETE FROM jobs WHERE job_id=?");
    $stmt->bind_param('i', $job_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Job deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']); 