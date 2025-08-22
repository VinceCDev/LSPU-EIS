<?php
require_once '../conn/db_conn.php';
header('Content-Type: application/json');

$email = 'admin.lspu1@example.com';
$plainPassword = 'A7b!c9D@';
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
$user_role = 'admin';
$status = 'Active';

try {
    $db = Database::getInstance()->getConnection();
    // Check if user exists
    $stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        // Update password
        $stmt->close();
        $stmt2 = $db->prepare('UPDATE user SET password = ? WHERE email = ?');
        $stmt2->bind_param('ss', $hashedPassword, $email);
        if ($stmt2->execute()) {
            echo json_encode(['success' => true, 'message' => 'Admin password updated (A7b!c9D@).']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating admin password: ' . $stmt2->error]);
        }
        $stmt2->close();
    } else {
        // Insert new admin user
        $stmt->close();
        $stmt3 = $db->prepare('INSERT INTO user (email, password, user_role, status) VALUES (?, ?, ?, ?)');
        $stmt3->bind_param('ssss', $email, $hashedPassword, $user_role, $status);
        if ($stmt3->execute()) {
            echo json_encode(['success' => true, 'message' => 'Admin user created with password (A7b!c9D@).']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error inserting admin user: ' . $stmt3->error]);
        }
        $stmt3->close();
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} 