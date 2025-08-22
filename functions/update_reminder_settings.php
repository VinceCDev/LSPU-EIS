<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

// Check if user is authenticated and is admin
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$db = Database::getInstance()->getConnection();

// Settings to update - match the structure from admin_reminder_settings.php
$settings_to_update = [
    'business_hours_start' => $input['business_hours_start'] ?? '9',
    'business_hours_end' => $input['business_hours_end'] ?? '18',
    'timezone' => $input['timezone'] ?? 'Asia/Manila',
    'frequency_minutes' => $input['frequency_minutes'] ?? '1',
    'max_reminders_per_day' => $input['max_reminders_per_day'] ?? '3',
    'email_enabled' => isset($input['email_enabled']) ? '1' : '0',
    'sms_enabled' => isset($input['sms_enabled']) ? '1' : '0',
    'email_subject' => $input['email_subject'] ?? '',
    'email_message' => $input['email_message'] ?? '',
    'sms_message' => $input['sms_message'] ?? ''
];

$success = true;
$errors = [];

foreach ($settings_to_update as $key => $value) {
    // Use the correct table name: reminder_settings
    $stmt = $db->prepare('INSERT INTO reminder_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?');
    $stmt->bind_param('sss', $key, $value, $value);
    
    if (!$stmt->execute()) {
        $success = false;
        $errors[] = "Failed to update $key: " . $stmt->error;
    }
    
    $stmt->close();
}

if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Reminder settings updated successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update some settings',
        'errors' => $errors
    ]);
}
?> 