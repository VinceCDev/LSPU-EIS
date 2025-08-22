<?php
require_once '../conn/db_conn.php';
session_start();
header('Content-Type: application/json');

// Ensure employer is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in as employer.']);
    exit;
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
    echo json_encode(['success' => false, 'message' => 'Employer not found.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Create or Update
    $fields = [
        'title', 'type', 'location', 'salary', 'status',
        'created_at', 'description', 'requirements', 'qualifications', 'employer_question'
    ];
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? '';
    }
    foreach ($fields as $field) {
        if ($data[$field] === '') {
            echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
            exit;
        }
    }
    if (isset($_POST['job_id']) && $_POST['job_id'] !== '') {
        // Update
        $job_id = $_POST['job_id'];
        $stmt = $db->prepare("UPDATE jobs SET employer_id=?, title=?, type=?, location=?, salary=?, status=?, created_at=?, description=?, requirements=?, qualifications=?, employer_question=? WHERE job_id=?");
        $stmt->bind_param(
            'issssssssssi',
            $employer_id, $data['title'], $data['type'], $data['location'], $data['salary'], $data['status'],
            $data['created_at'], $data['description'], $data['requirements'], $data['qualifications'], $data['employer_question'],
            $job_id
        );
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Job updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        // Create
        $stmt = $db->prepare("INSERT INTO jobs (employer_id, title, type, location, salary, status, created_at, description, requirements, qualifications, employer_question) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            'issssssssss',
            $employer_id, $data['title'], $data['type'], $data['location'], $data['salary'], $data['status'],
            $data['created_at'], $data['description'], $data['requirements'], $data['qualifications'], $data['employer_question']
        );
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Job created successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $stmt->error]);
        }
        $stmt->close();
    }
    exit;
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $job_id = $input['job_id'] ?? null;
    if (!$job_id) {
        echo json_encode(['success' => false, 'message' => 'Missing job_id']);
        exit;
    }
    $stmt = $db->prepare("DELETE FROM jobs WHERE job_id=?");
    $stmt->bind_param('i', $job_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Job deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']); 