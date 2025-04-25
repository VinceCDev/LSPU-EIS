<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

require_once '../conn/db_conn.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM jobs ORDER BY id DESC");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'jobs' => $jobs
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
