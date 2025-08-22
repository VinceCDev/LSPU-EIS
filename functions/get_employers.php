<?php
require_once '../conn/db_conn.php';
header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();
$sql = "SELECT * FROM employer ORDER BY company_name ASC";
$result = $db->query($sql);

$companies = [];
while ($row = $result->fetch_assoc()) {
    $companies[] = $row;
}
echo json_encode($companies); 