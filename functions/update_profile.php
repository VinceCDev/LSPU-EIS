<?php
session_start();
include '../conn/db_conn.php'; // This should initialize $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['email'])) {
        echo 'no_session';
        exit;
    }

    $email = $_SESSION['email'];

    // Get the user_id using the email
    $stmt_user = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt_user) {
        echo 'prepare_failed_user';
        exit;
    }

    $stmt_user->bind_param("s", $email);
    $stmt_user->execute();
    $stmt_user->bind_result($user_id);
    if (!$stmt_user->fetch()) {
        echo 'user_not_found';
        $stmt_user->close();
        $conn->close();
        exit;
    }
    $stmt_user->close();

    // Check if all expected fields are present
    $required_fields = [
        'first_name',
        'middle_name',
        'last_name',
        'contact_number',
        'city',
        'province',
        'birthdate',
        'gender',
        'civil_status',
        'year_graduated',
        'campus',
        'course',
        'status_prior_graduation',
        'status_employement_graduation'
    ];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo 'missing_' . $field;
            exit;
        }
    }

    // Sanitize input data
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $contact_number = trim($_POST['contact_number']);
    $city = trim($_POST['city']);
    $province = trim($_POST['province']);
    $birthdate = trim($_POST['birthdate']);
    $gender = trim($_POST['gender']);
    $civil_status = trim($_POST['civil_status']);
    $year_graduated = trim($_POST['year_graduated']);
    $campus = trim($_POST['campus']);
    $course = trim($_POST['course']);
    $status_prior_graduation = trim($_POST['status_prior_graduation']);
    $status_employement_graduation = trim($_POST['status_employement_graduation']);

    // Update alumni_profile table
    $stmt_update = $conn->prepare("
        UPDATE alumni_profile SET
            first_name = ?, 
            middle_name = ?, 
            last_name = ?, 
            contact_number = ?, 
            city = ?, 
            province = ?, 
            birthdate = ?, 
            gender = ?, 
            civil_status = ?, 
            year_graduated = ?, 
            campus = ?, 
            course = ?, 
            status_prior_graduation = ?, 
            status_employement_graduation = ?
        WHERE user_id = ?
    ");

    if (!$stmt_update) {
        echo 'prepare_failed_update';
        exit;
    }

    $stmt_update->bind_param(
        "ssssssssssssssi",
        $first_name,
        $middle_name,
        $last_name,
        $contact_number,
        $city,
        $province,
        $birthdate,
        $gender,
        $civil_status,
        $year_graduated,
        $campus,
        $course,
        $status_prior_graduation,
        $status_employement_graduation,
        $user_id
    );

    // Execute and check
    if ($stmt_update->execute()) {
        echo 'success';
    } else {
        echo 'fail';
    }

    $stmt_update->close();
    $conn->close();
} else {
    echo 'invalid_request';
}
