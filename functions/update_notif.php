<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $db = Database::getInstance()->getConnection();
    if ($action === 'mark_all_read') {
        $user_id = $_POST['user_id'] ?? null;
        if (!$user_id) {
            // Try to get user_id from session email
            if (!isset($_SESSION['email'])) {
                echo json_encode(['success' => false, 'message' => 'Missing user_id and session email.']);
                exit();
            }
            $email = $_SESSION['email'];
            $stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->bind_result($user_id);
            $stmt->fetch();
            $stmt->close();
            if (!$user_id) {
                echo json_encode(['success' => false, 'message' => 'User not found for session email.']);
                exit();
            }
        }
        $stmt = $db->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0');
        $stmt->bind_param('i', $user_id);
        $success = $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => $success]);
        exit();
    } elseif ($action === 'mark_one_read') {
        $notif_id = $_POST['id'] ?? null;
        if (!$notif_id) {
            echo json_encode(['success' => false, 'message' => 'Missing notification id.']);
            exit();
        }
        $stmt = $db->prepare('UPDATE notifications SET is_read = 1 WHERE id = ?');
        $stmt->bind_param('i', $notif_id);
        $success = $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => $success]);
        exit();
    }
}
echo json_encode(['success' => false, 'message' => 'Invalid request.']); 