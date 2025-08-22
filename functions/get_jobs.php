<?php
require_once '../conn/db_conn.php';
header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();
$sql = "SELECT j.job_id, j.employer_id, j.title, j.location, j.type, j.salary, 
               j.description, j.requirements, j.qualifications, j.status, j.employer_question, j.created_at,
               e.company_name, e.company_logo
        FROM jobs j
        LEFT JOIN employer e ON j.employer_id = e.user_id
        ORDER BY j.created_at DESC, j.job_id DESC";
$result = $db->query($sql);

$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}
echo json_encode($jobs);