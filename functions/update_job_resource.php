<?php
session_start();
require_once '../conn/db_conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$db = Database::getInstance()->getConnection();

// Get and validate input
$id = $_POST['id'] ?? '';
$department = $_POST['department'] ?? '';
$financial_budget = $_POST['financial_budget'] ?? 0;
$technology_needed = $_POST['technology_needed'] ?? '';
$training_required = $_POST['training_required'] ?? '';
$physical_objects = $_POST['physical_objects'] ?? '';
$staffing_requirements = $_POST['staffing_requirements'] ?? '';
$timeline = $_POST['timeline'] ?? '';
$status = $_POST['status'] ?? 'Planning';
$notes = $_POST['notes'] ?? '';

// Validate required fields
if (empty($id) || empty($department)) {
    echo json_encode(['success' => false, 'message' => 'ID and Department are required']);
    exit();
}

// Update database
$stmt = $db->prepare('UPDATE job_resources SET department = ?, financial_budget = ?, technology_needed = ?, training_required = ?, physical_objects = ?, staffing_requirements = ?, timeline = ?, status = ?, notes = ? WHERE id = ?');
$stmt->bind_param('sdsssssssi', $department, $financial_budget, $technology_needed, $training_required, $physical_objects, $staffing_requirements, $timeline, $status, $notes, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Job resource updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
?>