<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'alumni') {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();
$user_id = null;
$email = $_SESSION['email'];
$stmt = $db->prepare('SELECT user_id, email, secondary_email FROM user WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($user_id, $user_email, $secondary_email);
$stmt->fetch();
$stmt->close();
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit();
}
// Fetch alumni details
$stmt = $db->prepare('SELECT first_name, middle_name, last_name, birthdate, contact, gender, civil_status, city, province, year_graduated, college, course, verification_document FROM alumni WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $middle_name, $last_name, $birthdate, $contact, $gender, $civil_status, $city, $province, $year_graduated, $college, $course, $verification_document);
if ($stmt->fetch()) {
    $alumni = [
        'name' => trim($first_name . ' ' . $middle_name . ' ' . $last_name),
        'first_name' => $first_name,
        'middle_name' => $middle_name,
        'last_name' => $last_name,
        'email' => $user_email,
        'secondary_email' => $secondary_email,
        'birthdate' => $birthdate,
        'contact' => $contact,
        'gender' => $gender,
        'civil_status' => $civil_status,
        'city' => $city,
        'province' => $province,
        'year_graduated' => $year_graduated,
        'college' => $college,
        'course' => $course,
        'verification_document' => $verification_document
    ];
} else {
    $alumni = [
        'name' => '',
        'first_name' => '',
        'middle_name' => '',
        'last_name' => '',
        'email' => $user_email,
        'secondary_email' => $secondary_email,
        'birthdate' => '',
        'contact' => '',
        'gender' => '',
        'civil_status' => '',
        'city' => '',
        'province' => '',
        'year_graduated' => '',
        'college' => '',
        'course' => '',
        'verification_document' => ''
    ];
}
$stmt->close();
// Fetch education, skills, experiences, resume as before (if you have those tables)
$alumni['education'] = [];
$alumni['skills'] = [];
$alumni['experiences'] = [];
$alumni['resume'] = null;
echo json_encode(['success' => true, 'profile' => $alumni]);
exit; 