<?php
require_once '../conn/db_conn.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
header('Content-Type: application/json');

// Ensure employer is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in as employer.']);
    exit;
}

// Gemini API function using gemini-2.0-flash and X-goog-api-key header
function call_gemini_api($prompt) {
    $api_key = 'AIzaSyC5LRDr-q8eDfLMFe_QOA8GmLCzF-Qo6dU';
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

// Function to extract percentage from Gemini response
function extract_match_percentage($response) {
    if (preg_match('/\((\d+)%\)/', $response, $matches)) {
        return (int)$matches[1];
    }
    return 0;
}

$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];

// Get employer's user_id from email
$stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($employer_id);
$stmt->fetch();
$stmt->close();

if (!$employer_id) {
    echo json_encode(['success' => false, 'message' => 'Employer not found.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Create or Update - REMOVE employer_id from fields since it's set automatically
    $fields = [
        'title', 'type', 'location', 'salary', 'status',
        'description', 'requirements', 'qualifications'
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
    
    // Set created_at to current date/time
    $data['created_at'] = date('Y-m-d H:i:s');
    
    if (isset($_POST['job_id']) && $_POST['job_id'] !== '') {
        // Update - employer_id is set automatically from session
        $job_id = $_POST['job_id'];
        $stmt = $db->prepare("UPDATE jobs SET title=?, type=?, location=?, salary=?, status=?, description=?, requirements=?, qualifications=? WHERE job_id=? AND employer_id=?");
        $stmt->bind_param(
            'ssssssssii',
            $data['title'], $data['type'], $data['location'], $data['salary'], 
            $data['status'], $data['description'], $data['requirements'], 
            $data['qualifications'], $job_id, $employer_id
        );
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Job updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        // Create - employer_id is set automatically from session
        $stmt = $db->prepare("INSERT INTO jobs (employer_id, title, type, location, salary, status, created_at, description, requirements, qualifications) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            'isssssssss',
            $employer_id,
            $data['title'], $data['type'], $data['location'], 
            $data['salary'], $data['status'], $data['created_at'], 
            $data['description'], $data['requirements'], $data['qualifications']
        );
        
        if ($stmt->execute()) {
            // Get the newly inserted job ID
            $job_id = $stmt->insert_id;
            error_log("New job created with ID: " . $job_id);
            
            echo json_encode(['success' => true, 'message' => 'Job created successfully.']);

            // --- Gemini job match notification logic ---
            $alumni = [];
            $result = $db->query("SELECT a.user_id, a.course, a.alumni_id, a.first_name, a.last_name FROM alumni a");
            
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
                
                $alumni[] = [
                    'user_id' => $row['user_id'],
                    'alumni_id' => $row['alumni_id'],
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'course' => $row['course'],
                    'skills' => implode(', ', $skills)
                ];
            }

            $job_title = $data['title'];
            $requirements = $data['requirements'];
            $qualifications = $data['qualifications'];

            foreach ($alumni as $alum) {
                $skills_text = !empty($alum['skills']) ? "Skills: {$alum['skills']}" : "";
                $prompt = "Based on the following information, determine if this is a 'Match' or 'Not a Match' for the job.
                    Provide only one of these two phrases as your response, followed by the job match percentage in parentheses (e.g., \"Match (85%)\"), with no additional explanation or text.

                    Candidate Background:
                    College Program: \"{$alum['course']}\"
                    Skills: \"{$skills_text}\"
                    Job Details:
                    Title: \"$job_title\"
                    Requirements: \"$requirements\"
                    Qualifications: \"$qualifications\"
                    Response:";
                
                $result = call_gemini_api($prompt);
                $match = '';
                if (is_array($result) && count($result) > 0) {
                    $match = trim($result[0]);
                } elseif (is_string($result)) {
                    $match = trim($result);
                }
                
                // Extract percentage from response
                $match_percentage = extract_match_percentage($match);
                
                // Store ALL matches in leaderboard (using alumni_id)
                $stmt_leaderboard = $db->prepare('INSERT INTO job_match_leaderboard (alumni_id, job_id, match_percentage, notified) VALUES (?, ?, ?, ?) 
                                                ON DUPLICATE KEY UPDATE match_percentage = VALUES(match_percentage), notified = VALUES(notified)');
                
                if (empty($alum['alumni_id'])) {
                    error_log("ERROR: alumni_id is empty for user_id: {$alum['user_id']}");
                    continue;
                }
                
                $notified = (stripos($match, 'match') !== false && stripos($match, 'not a match') === false && $match_percentage >= 50) ? 1 : 0;
                $stmt_leaderboard->bind_param('iiii', $alum['alumni_id'], $job_id, $match_percentage, $notified);
                
                try {
                    $stmt_leaderboard->execute();
                    error_log("Successfully inserted leaderboard entry for alumni_id: {$alum['alumni_id']}, job_id: $job_id");
                } catch (Exception $e) {
                    error_log("ERROR inserting leaderboard for alumni_id {$alum['alumni_id']}: " . $e->getMessage());
                }
                
                $stmt_leaderboard->close();
                
                if (stripos($match, 'match') !== false && stripos($match, 'not a match') === false && $match_percentage >= 50) {
                    $notif_message = "New job matches your profile!";
                    $notif_details = "A new job posting for '$job_title' ({$match_percentage}% match) matches your background. Check it out!";
                    
                    $stmt_notif = $db->prepare('INSERT INTO notifications (user_id, type, message, details, job_id) VALUES (?, ?, ?, ?, ?)');
                    $notif_type = 'job_match';
                    
                    // Debug: Check if all parameters are valid
                    error_log("Creating notification for user_id: {$alum['user_id']}, type: $notif_type, message: $notif_message, details: $notif_details, job_id: $job_id");
                    
                    $stmt_notif->bind_param('isssi', $alum['user_id'], $notif_type, $notif_message, $notif_details, $job_id);
                    
                    if ($stmt_notif->execute()) {
                        error_log("Notification successfully created for user {$alum['user_id']}");
                    } else {
                        error_log("ERROR creating notification for user {$alum['user_id']}: " . $stmt_notif->error);
                    }
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
                            $mail->Username = 'lspueis@gmail.com';
                            $mail->Password = 'afbp fcwf oujr yqzr';
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port = 587;

                            // Recipients
                            $mail->setFrom('lspueis@gmail.com', 'LSPU EIS');
                            $mail->addAddress($recipient);

                            // Content
                            $mail->isHTML(true);
                            $mail->Subject = "New Job Match: $job_title ({$match_percentage}%)";
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
                                        .percentage-badge {
                                            background: #00A0E9;
                                            color: white;
                                            padding: 8px 16px;
                                            border-radius: 20px;
                                            font-weight: bold;
                                            display: inline-block;
                                            margin: 10px 0;
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
                                                <div class='percentage-badge'>{$match_percentage}% Match</div>
                                                
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

                            $mail->AltBody = "Hello {$first_name} {$last_name},\n\nWe found a new job posting that matches your profile with a {$match_percentage}% match rate!\n\nJob Title: {$job_title}\nMatch Percentage: {$match_percentage}%\nRequirements: {$requirements}\nQualifications: {$qualifications}\n\nView Job: localhost/lspu-eis/home\n\nBest regards,\nLSPU EIS Team";

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