<?php
session_start();
require '../conn/db_conn.php';

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Sanitize input
function sanitize($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

// Get user inputs
$email = sanitize($_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$first_name = sanitize($_POST['first_name']);
$middle_name = sanitize($_POST['middle_name']);
$last_name = sanitize($_POST['last_name']);
$birthdate = $_POST['birthdate'];
$contact_number = sanitize($_POST['contact']);
$gender = $_POST['gender'];
$civil_status = $_POST['civil_status'];
$city = sanitize($_POST['city']);
$province = sanitize($_POST['province']);
$year_graduated = $_POST['year_graduated'];
$campus = $_POST['campus'];
$course = $_POST['course'];

// Handle photo upload
$alumni_id_photo = '';
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
    $target_dir = "../uploads/";
    $filename = uniqid() . "_" . basename($_FILES["photo"]["name"]);
    $target_file = $target_dir . $filename;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($imageFileType, $allowed)) {
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $alumni_id_photo = $filename;
        } else {
            die("Error uploading photo.");
        }
    } else {
        die("Invalid photo format.");
    }
}

// Insert into database
try {
    $conn->begin_transaction();

    // Insert into users
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $user_id = $stmt->insert_id;
    $stmt->close();

    // Insert into alumni_profile
    $stmt2 = $conn->prepare("INSERT INTO alumni_profile (
        user_id, first_name, middle_name, last_name, birthdate, contact_number, gender,
        civil_status, city, province, year_graduated, campus, course, alumni_id_photo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt2->bind_param(
        "isssssssssssss",
        $user_id,
        $first_name,
        $middle_name,
        $last_name,
        $birthdate,
        $contact_number,
        $gender,
        $civil_status,
        $city,
        $province,
        $year_graduated,
        $campus,
        $course,
        $alumni_id_photo
    );
    $stmt2->execute();
    $stmt2->close();

    $conn->commit();

    header("Location: ../login.php");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
}
