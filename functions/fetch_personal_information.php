<?php
session_start();
require_once '../conn/db_conn.php'; // Ensure this file sets up $pdo as a PDO instance

if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$email = $_SESSION['email'];

try {
    $sql = "SELECT 
                u.email,
                pi.first_name,
                pi.middle_name,
                pi.last_name,
                pi.contact_number,
                pi.city,
                pi.province,
                pi.birth_date,
                pi.gender,
                pi.campus_graduated,
                pi.course,
                pi.year_graduated
            FROM users u
            JOIN personal_information pi ON u.id = pi.user_id
            WHERE u.email = :email";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($data ?: ['error' => 'No profile data found']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
