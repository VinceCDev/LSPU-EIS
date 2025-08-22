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

// Fetch employers with contact numbers
$sql = "SELECT 
            e.employer_id as id,
            e.company_name as name,
            e.contact_email as email,
            COALESCE(e.contact_number, '') as phone,
            e.company_address,
            e.industry
        FROM employer e 
        WHERE e.contact_number IS NOT NULL 
        AND e.contact_number != '' 
        AND e.status = 'active'
        ORDER BY e.company_name";

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
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $phone,
            'address' => $row['company_address'],
            'industry' => $row['industry']
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
        'message' => 'Failed to fetch employer contacts',
        'contacts' => []
    ]);
}
?> 