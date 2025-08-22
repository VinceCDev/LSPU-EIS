<?php
require_once '../conn/db_conn.php';
header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();
$sql = "SELECT e.user_id, e.company_name, e.company_logo, e.company_location, e.contact_email, e.contact_number, e.industry_type, e.nature_of_business, e.tin, e.date_established, e.company_type, e.accreditation_status, e.document_file, u.email, u.status
        FROM employer e
        JOIN user u ON e.user_id = u.user_id
        WHERE u.status = 'Active'
        ORDER BY e.user_id DESC";
$result = $db->query($sql);

$companies = [];
while ($row = $result->fetch_assoc()) {
    $companies[] = $row;
}
echo json_encode($companies);
