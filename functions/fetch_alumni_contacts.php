<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

// Check if user is authenticated
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = Database::getInstance()->getConnection();

// Fetch alumni with phone numbers
$sql = "SELECT 
            a.alumni_id as id,
            CONCAT(a.first_name, ' ', COALESCE(a.middle_name, ''), ' ', a.last_name) as name,
            a.email,
            COALESCE(a.phone_number, '') as phone,
            a.course,
            a.college
        FROM alumni a 
        WHERE a.phone_number IS NOT NULL 
        AND a.phone_number != '' 
        AND a.status = 'active'
        ORDER BY a.last_name, a.first_name";

$result = $db->query($sql);

if ($result) {
    $contacts = [];
    while ($row = $result->fetch_assoc()) {
        // Clean and format phone number
        $phone = preg_replace('/[^0-9+]/', '', $row['phone']);
        
        // Add country code if not present
        if (!str_starts_with($phone, '+')) {
            if (str_starts_with($phone, '0')) {
                $phone = '+63' . substr($phone, 1);
            } else {
                $phone = '+63' . $phone;
            }
        }
        
        $contacts[] = [
            'id' => $row['id'],
            'name' => trim($row['name']),
            'email' => $row['email'],
            'phone' => $phone,
            'course' => $row['course'],
            'college' => $row['college']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'contacts' => $contacts,
        'count' => count($contacts)
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch alumni contacts',
        'contacts' => []
    ]);
}
?> 