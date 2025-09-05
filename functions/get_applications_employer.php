<?php
// functions/get_applications_employer.php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['applications' => []]);
    exit();
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
    echo json_encode(['applications' => []]);
    exit();
}

$sql = "
SELECT 
    app.application_id,
    app.applied_at,
    app.status AS application_status, 
    a.alumni_id,
    a.first_name, a.middle_name, a.last_name, a.birthdate, a.contact, a.gender, a.civil_status, a.city, a.province, a.year_graduated, a.college, a.course,
    u.email, u.secondary_email,
    j.job_id, j.title, j.type, j.location, j.salary, j.status AS job_status, j.created_at, j.description, j.requirements, j.qualifications, j.employer_question, j.employer_id,
    e.company_name
FROM applications app
JOIN alumni a ON app.alumni_id = a.alumni_id
JOIN user u ON a.user_id = u.user_id
JOIN jobs j ON app.job_id = j.job_id
LEFT JOIN employer e ON j.employer_id = e.user_id
WHERE j.employer_id = ?
ORDER BY app.applied_at DESC
";

$stmt = $db->prepare($sql);
$stmt->bind_param('i', $employer_id);
$stmt->execute();
$result = $stmt->get_result();
$applications = [];
while ($row = $result->fetch_assoc()) {
    $row['alumni_name'] = trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
    // Fallback: if company_name is empty but employer_id is set, fetch company_name manually
    if ((empty($row['company_name']) || is_null($row['company_name'])) && !empty($row['employer_id'])) {
        $emp_stmt = $db->prepare('SELECT company_name FROM employer WHERE user_id = ? LIMIT 1');
        $emp_stmt->bind_param('i', $row['employer_id']);
        $emp_stmt->execute();
        $emp_stmt->bind_result($company_name);
        if ($emp_stmt->fetch()) {
            $row['company_name'] = $company_name;
        }
        $emp_stmt->close();
    }
    // Fetch profile_pic from alumni
    $pic_stmt = $db->prepare('SELECT profile_pic FROM alumni WHERE alumni_id = ? LIMIT 1');
    $pic_stmt->bind_param('i', $row['alumni_id']);
    $pic_stmt->execute();
    $pic_stmt->bind_result($profile_pic);
    if ($pic_stmt->fetch() && $profile_pic) {
        $row['profile_image'] = 'uploads/profile_picture/' . $profile_pic;
    } else {
        $row['profile_image'] = null;
    }
    $pic_stmt->close();
    // Fetch resume from alumni_resume
    $resume_stmt = $db->prepare('SELECT file_name FROM alumni_resume WHERE alumni_id = ? ORDER BY uploaded_at DESC LIMIT 1');
    $resume_stmt->bind_param('i', $row['alumni_id']);
    $resume_stmt->execute();
    $resume_stmt->bind_result($resume_file);
    if ($resume_stmt->fetch() && $resume_file) {
        $row['resume_file'] = 'uploads/resumes/' . $resume_file;
    } else {
        $row['resume_file'] = null;
    }
    $resume_stmt->close();
    // Fetch work experience
    $exp_stmt = $db->prepare('SELECT experience_id, title, company, start_date, end_date, current, description, created_at, updated_at FROM alumni_experience WHERE alumni_id = ? ORDER BY start_date DESC');
    $exp_stmt->bind_param('i', $row['alumni_id']);
    $exp_stmt->execute();
    $exp_result = $exp_stmt->get_result();
    $experiences = [];
    while ($exp = $exp_result->fetch_assoc()) {
        $experiences[] = $exp;
    }
    $row['experiences'] = $experiences;
    $exp_stmt->close();
    // Fetch education
    $edu_stmt = $db->prepare('SELECT education_id, degree, school, start_date, end_date, current, created_at FROM alumni_education WHERE alumni_id = ? ORDER BY start_date DESC');
    $edu_stmt->bind_param('i', $row['alumni_id']);
    $edu_stmt->execute();
    $edu_result = $edu_stmt->get_result();
    $educations = [];
    while ($edu = $edu_result->fetch_assoc()) {
        $educations[] = $edu;
    }
    $row['educations'] = $educations;
    $edu_stmt->close();
    // Fetch skills
    $skill_stmt = $db->prepare('SELECT skill_id, name, certificate, created_at FROM alumni_skill WHERE alumni_id = ?');
    $skill_stmt->bind_param('i', $row['alumni_id']);
    $skill_stmt->execute();
    $skill_result = $skill_stmt->get_result();
    $skills = [];
    while ($skill = $skill_result->fetch_assoc()) {
        $skills[] = $skill;
    }
    $row['skills'] = $skills;
    $skill_stmt->close();
    $applications[] = $row;
}
$stmt->close();
echo json_encode(['applications' => $applications]); 