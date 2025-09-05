<?php
// functions/get_applications_admin.php
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

$db = Database::getInstance()->getConnection();

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
LEFT JOIN employer e ON j.employer_id = e.employer_id
ORDER BY app.applied_at DESC
";

$result = $db->query($sql);
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
    $applications[] = $row;
}
echo json_encode(['applications' => $applications]); 