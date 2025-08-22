<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['employer', 'admin'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();
$user_id = null;
$email = $_SESSION['email'];
$stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit();
}
// Fetch employer details
$stmt = $db->prepare('SELECT employer_id, user_id, company_name, company_logo, company_location, contact_email, contact_number, industry_type, nature_of_business, tin, date_established, company_type, accreditation_status, document_file FROM employer WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($employer_id, $user_id, $company_name, $company_logo, $company_location, $contact_email, $contact_number, $industry_type, $nature_of_business, $tin, $date_established, $company_type, $accreditation_status, $document_file);
if ($stmt->fetch()) {
    $profile = [
        'employer_id' => $employer_id,
        'user_id' => $user_id,
        'name' => $company_name, // Add name field for Vue.js compatibility
        'company_name' => $company_name,
        'company_logo' => $company_logo ? '/lspu_eis/uploads/logos/' . $company_logo : '',
        'profile_pic' => $company_logo ? '/lspu_eis/uploads/logos/' . $company_logo : '', // Add profile_pic for Vue.js compatibility
        'company_location' => $company_location,
        'contact_email' => $contact_email,
        'contact_number' => $contact_number,
        'industry_type' => $industry_type,
        'nature_of_business' => $nature_of_business,
        'tin' => $tin,
        'date_established' => $date_established,
        'company_type' => $company_type,
        'accreditation_status' => $accreditation_status,
        'document_file' => $document_file ? '/lspu_eis/uploads/documents/' . $document_file : ''
    ];
} else {
    $profile = [
        'employer_id' => '',
        'user_id' => '',
        'name' => '', // Add name field for Vue.js compatibility
        'company_name' => '',
        'company_logo' => '',
        'profile_pic' => '', // Add profile_pic for Vue.js compatibility
        'company_location' => '',
        'contact_email' => '',
        'contact_number' => '',
        'industry_type' => '',
        'nature_of_business' => '',
        'tin' => '',
        'date_established' => '',
        'company_type' => '',
        'accreditation_status' => '',
        'document_file' => ''
    ];
}
$stmt->close();
echo json_encode(['success' => true, 'profile' => $profile]);
exit; 