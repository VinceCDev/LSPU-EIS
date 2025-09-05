<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'alumni') {
    echo json_encode(['appliedJobs' => []]);
    exit();
}
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();
$email = $_SESSION['email'];

// Get alumni_id
$stmt = $db->prepare('SELECT a.alumni_id FROM alumni a JOIN user u ON a.user_id = u.user_id WHERE u.email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($alumni_id);
$stmt->fetch();
$stmt->close();
if (!$alumni_id) {
    echo json_encode(['appliedJobs' => []]);
    exit();
}

// Get all applications for this alumni
$stmt = $db->prepare('SELECT app.job_id, app.applied_at, app.status AS application_status, j.title, j.type, j.location, j.salary, j.status, j.created_at, j.description, j.requirements, j.qualifications, j.employer_question, j.employer_id FROM applications app JOIN jobs j ON app.job_id = j.job_id WHERE app.alumni_id = ? ORDER BY app.applied_at DESC');
$stmt->bind_param('i', $alumni_id);
$stmt->execute();
$result = $stmt->get_result();
$appliedJobs = [];
while ($row = $result->fetch_assoc()) {
    // Get company details including logo
    $company_name = '';
    $company_logo = '';
    $company_location = '';
    $contact_email = '';
    $contact_number = '';
    $nature_of_business = '';
    $industry_type = '';
    $accreditation_status = '';
    
    if ($row['employer_id']) {
        // Use employer_id from jobs table to match user_id in employer table
        $emp_stmt = $db->prepare('SELECT company_name, company_logo, company_location, contact_email, contact_number, nature_of_business, industry_type, accreditation_status FROM employer WHERE user_id = ?');
        $emp_stmt->bind_param('i', $row['employer_id']);
        $emp_stmt->execute();
        $emp_stmt->bind_result($company_name, $company_logo, $company_location, $contact_email, $contact_number, $nature_of_business, $industry_type, $accreditation_status);
        $emp_stmt->fetch();
        $emp_stmt->close();
    }
    
    $row['application_status'] = $row['application_status'];
    $row['company_name'] = $company_name;
    $row['company_logo'] = $company_logo;
    $row['company_location'] = $company_location;
    $row['contact_email'] = $contact_email;
    $row['contact_number'] = $contact_number;
    $row['nature_of_business'] = $nature_of_business;
    $row['industry_type'] = $industry_type;
    $row['accreditation_status'] = $accreditation_status;
    
    $appliedJobs[] = $row;
}
$stmt->close();
echo json_encode(['appliedJobs' => $appliedJobs]);
?>