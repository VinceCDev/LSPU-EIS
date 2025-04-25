<?php
// save_skills.php
require_once '../conn/db_conn.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO skills (name, certificate) VALUES (?, ?)");

// Optional: clear existing skills if you're editing
// $conn->query("DELETE FROM skills");

foreach ($data as $skill) {
    $name = $skill['name'];
    $certificate = $skill['certificate'] ?? null;
    $stmt->bind_param("ss", $name, $certificate);
    $stmt->execute();
}

echo json_encode(['success' => true]);
