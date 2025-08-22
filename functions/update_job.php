<?php
require_once '../conn/db_conn.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
    $query = "UPDATE jobs SET title=?, company=?, type=?, location=?, description=?, requirements=?, qualifications=?, employer_question=?, salary=?, status=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        'ssssssssssi',
        $data['title'],
        $data['company'],
        $data['type'],
        $data['location'],
        $data['description'],
        $data['requirements'],
        $data['qualifications'],
        $data['employer_question'],
        $data['salary'],
        $data['status'],
        $data['id']
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Missing job ID']);
}
$conn->close();
