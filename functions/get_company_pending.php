<?php
require_once '../conn/db_conn.php';
header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();
$sql = "SELECT e.user_id AS employer_id, e.user_id, e.company_name, e.company_logo, e.company_location, e.contact_email, e.contact_number, e.industry_type, e.nature_of_business, e.tin, e.date_established, e.company_type, e.accreditation_status, e.document_file, u.email, u.status
        FROM employer e
        JOIN user u ON e.user_id = u.user_id
        WHERE u.status = 'Pending'
        ORDER BY e.user_id DESC";
$result = $db->query($sql);

$employers = [];
while ($row = $result->fetch_assoc()) {
    if (!empty($row['company_logo'])) {
        $row['company_logo'] = '../uploads/logos/' . $row['company_logo'];
    }
    if (!empty($row['document_file'])) {
        $row['document_file'] = '../uploads/documents/' . $row['document_file'];
    }
    $employers[] = $row;
}
echo json_encode($employers); 