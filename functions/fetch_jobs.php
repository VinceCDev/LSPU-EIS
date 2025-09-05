<?php
header('Content-Type: application/json');
require_once '../conn/db_conn.php'; // This should initialize the $conn variable using MySQLi

try {
    $sql = "SELECT jobs.*, company.company_name AS company, company.company_logo AS logo
            FROM jobs
            LEFT JOIN employer AS company ON jobs.company_id = company.id
            WHERE jobs.status = 'active'
            ORDER BY jobs.id DESC";

    $result = $conn->query($sql);
    $jobs = [];

    while ($row = $result->fetch_assoc()) {
        $jobs[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'department' => $row['company'],
            'location' => $row['location'],
            'type' => $row['type'],
            'salary' => $row['salary'],
            'description' => $row['description'],
            'company' => $row['company'],
            'logo' => $row['logo'],
            'requirements' => explode("\n", $row['requirements']),
            'qualifications' => explode("\n", $row['qualifications']),
            'questions' => explode("\n", $row['employer_question']),
            'saved' => false
        ];
    }

    echo json_encode($jobs);
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
