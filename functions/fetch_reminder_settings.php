<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

// Check if user is authenticated and is admin
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = Database::getInstance()->getConnection();

// Fetch all reminder settings from the correct table
$sql = "SELECT setting_key, setting_value FROM reminder_settings";
$result = $db->query($sql);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch settings']);
    exit();
}

// Default settings structure
$settings = [
    'business_hours_start' => '9',
    'business_hours_end' => '18',
    'timezone' => 'Asia/Manila',
    'frequency_minutes' => '1',
    'max_reminders_per_day' => '3',
    'email_enabled' => '1',
    'sms_enabled' => '1',
    'email_subject' => 'LSPU EIS - Automated Reminder',
    'email_message' => 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!',
    'sms_message' => 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!'
];

while ($row = $result->fetch_assoc()) {
    $key = $row['setting_key'];
    $value = $row['setting_value'];
    
    // Update settings with database values
    if (isset($settings[$key])) {
        $settings[$key] = $value;
    }
}

echo json_encode([
    'success' => true,
    'settings' => $settings
]);
?> 