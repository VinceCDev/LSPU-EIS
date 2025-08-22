<?php
require_once '../conn/db_conn.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance()->getConnection();
    $id = $_POST['id'] ?? '';
    $folder = $_POST['folder'] ?? '';
    if (!$id || !$folder) {
        echo json_encode(['success' => false, 'message' => 'Missing id or folder.']);
        exit;
    }
    $stmt = $db->prepare('UPDATE messages SET folder = ? WHERE id = ?');
    $stmt->bind_param('si', $folder, $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Message updated.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update message.']);
    }
    $stmt->close();
    exit;
}
echo json_encode(['success' => false, 'message' => 'Invalid request.']);
exit; 