<?php
require_once '../conn/db_conn.php';
header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();
$sql = "SELECT a.alumni_id, a.user_id, a.first_name, a.middle_name, a.last_name, a.birthdate, a.contact, a.gender, a.civil_status, a.city, a.province, a.year_graduated, a.college, a.course, a.verification_document, u.email, u.secondary_email, u.status
        FROM alumni a
        JOIN user u ON a.user_id = u.user_id
        WHERE u.status = 'Pending'
        ORDER BY a.alumni_id DESC";
$result = $db->query($sql);

$alumni = [];
while ($row = $result->fetch_assoc()) {
    $alumni[] = $row;
}
echo json_encode($alumni); 