<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('display_errors', 0);
error_log("Starting get_alumni_active.php");
error_log("Session Data: " . print_r($_SESSION, true));

// Authentication check
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}

require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();

// Fetch alumni with their skills and experiences
$sql = "SELECT a.alumni_id, a.user_id, a.first_name, a.middle_name, a.last_name, a.birthdate, a.contact, a.gender, a.civil_status, a.city, a.province, a.year_graduated, a.college, a.course, a.verification_document, a.profile_pic, u.email, u.secondary_email, u.status
        FROM alumni a
        JOIN user u ON a.user_id = u.user_id
        WHERE u.status = 'active'";
$result = $db->query($sql);

$alumni = [];
while ($row = $result->fetch_assoc()) {
    $alumni_id = $row['alumni_id'];

    // Fetch skills for this alumni (only names)
    $skills_stmt = $db->prepare('SELECT name FROM alumni_skill WHERE alumni_id = ? ORDER BY created_at DESC');
    $skills_stmt->bind_param('i', $alumni_id);
    $skills_stmt->execute();
    $skills_result = $skills_stmt->get_result();
    $skills = [];
    while ($skill_row = $skills_result->fetch_assoc()) {
        $skills[] = $skill_row['name'];
    }
    $skills_stmt->close();

    // Fetch experiences for this alumni
    $exp_stmt = $db->prepare('SELECT experience_id, title, company, start_date, end_date, current, description, location_of_work, employment_status, employment_sector FROM alumni_experience WHERE alumni_id = ? ORDER BY start_date DESC');
    $exp_stmt->bind_param('i', $alumni_id);
    $exp_stmt->execute();
    $exp_result = $exp_stmt->get_result();
    $experiences = [];
    while ($exp_row = $exp_result->fetch_assoc()) {
        $experiences[] = [
            'experience_id' => $exp_row['experience_id'],
            'title' => $exp_row['title'],
            'company' => $exp_row['company'],
            'start_date' => $exp_row['start_date'],
            'end_date' => $exp_row['end_date'],
            'current' => (bool)$exp_row['current'],
            'description' => $exp_row['description'],
            'location_of_work' => $exp_row['location_of_work'],
            'employment_status' => $exp_row['employment_status'],
            'employment_sector' => $exp_row['employment_sector']
        ];
    }
    $exp_stmt->close();

    // Structure the alumni data
    $alumni[] = [
        'alumni_id' => $row['alumni_id'],
        'user_id' => $row['user_id'],
        'first_name' => $row['first_name'],
        'middle_name' => $row['middle_name'],
        'last_name' => $row['last_name'],
        'birthdate' => $row['birthdate'],
        'contact' => $row['contact'],
        'gender' => $row['gender'],
        'civil_status' => $row['civil_status'],
        'city' => $row['city'],
        'province' => $row['province'],
        'year_graduated' => $row['year_graduated'],
        'college' => $row['college'],
        'course' => $row['course'],
        'verification_document' => $row['verification_document'],
        'profile_picture' => $row['profile_pic'] ? "/lspu_eis/uploads/profile_picture/" . urlencode($row['profile_pic']) : null,
        'email' => $row['email'],
        'secondary_email' => $row['secondary_email'],
        'status' => $row['status'],
        'skills' => $skills,
        'experiences' => $experiences,
        'documents' => $row['verification_document'] ? [
            [
                'name' => 'Verification Document',
                'url' => "/lspu_eis/uploads/documents/" . urlencode($row['verification_document'])
            ]
        ] : []
    ];
}

// Sort the alumni array by YEAR first, then COLLEGE, then NAME
usort($alumni, function($a, $b) {
    // Compare by YEAR GRADUATED (descending - newest first)
    if ($a['year_graduated'] != $b['year_graduated']) {
        return $b['year_graduated'] - $a['year_graduated'];
    }
    
    // If years are the same, compare by COLLEGE
    $collegeCompare = strcmp($a['college'], $b['college']);
    if ($collegeCompare !== 0) {
        return $collegeCompare;
    }
    
    // If same college, compare by LAST NAME
    $lastNameCompare = strcmp($a['last_name'], $b['last_name']);
    if ($lastNameCompare !== 0) {
        return $lastNameCompare;
    }
    
    // If same last name, compare by FIRST NAME
    return strcmp($a['first_name'], $b['first_name']);
});

echo json_encode(['success' => true, 'alumni' => $alumni]);
exit();