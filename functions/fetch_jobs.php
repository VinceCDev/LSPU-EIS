<?php
header('Content-Type: application/json');
require_once '../conn/db_conn.php'; // This should initialize the $pdo variable using PDO

try {
    $sql = "SELECT jobs.*, company.company_name AS company, company.logo AS logo
            FROM jobs
            LEFT JOIN company ON jobs.company_id = company.id
            WHERE jobs.status = 'active'
            ORDER BY jobs.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $jobs = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $jobs[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'department' => $row['department'],
            'location' => $row['location'],
            'type' => $row['type'],
            'salary' => $row['salary'],
            'description' => $row['description'],
            'company' => $row['company'],
            'logo' => $row['logo'], // adjust if needed
            'qualifications' => explode("\n", $row['qualifications']),
            'questions' => json_decode($row['employer_question'], true),
            'saved' => false
        ];
    }

    echo json_encode($jobs);
} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
