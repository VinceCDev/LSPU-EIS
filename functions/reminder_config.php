<?php
/**
 * Reminder System Configuration
 * Loads settings from database and provides fallback defaults
 */

// Set timezone
date_default_timezone_set('Asia/Manila');

// Include database connection
require_once dirname(__DIR__) . '/conn/db_conn.php';

// Default configuration (fallback)
$default_config = [
    'time_settings' => [
        'start_hour' => 9,
        'end_hour' => 18,
        'timezone' => 'Asia/Manila',
    ],
    'frequency' => [
        'send_every_minutes' => 1,
        'max_reminders_per_day' => 3,
    ],
    'messages' => [
        'email_subject' => 'LSPU EIS - Automated Reminder',
        'email_message' => 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!',
        'sms_message' => 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!',
    ],
    'email' => [
        'enabled' => true,
        'smtp_host' => 'smtp.gmail.com',
        'smtp_username' => 'allencristal12@gmail.com',
        'smtp_password' => 'ugwb vksz wjto zbwf',
        'smtp_port' => 587,
        'from_email' => 'allencristal12@gmail.com',
        'from_name' => 'LSPU EIS System',
    ],
        'sms' => [
        'enabled' => true,
        'api_url' => 'https://www.cloud.smschef.com/api/send/sms',
        'api_key' => '25582084b5f80149f9896fea59a7a7a4d17c59ea', // SMS Chef secret
        'device' => '00e4514aa8b68f55', // your device ID
        'sender' => 'LSPU EIS', // SMS Chef doesn’t require a sender name
        'sim' => 1 // SIM slot to use
    ],

];

// Function to load settings from database
function loadSettingsFromDatabase() {
    global $default_config;
    
    try {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT setting_key, setting_value FROM reminder_settings";
        $result = $db->query($sql);
        
        if (!$result) {
            return $default_config;
        }
        
        $settings = [];
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        // Build configuration from database settings
        $config = $default_config;
        
        if (isset($settings['business_hours_start'])) {
            $config['time_settings']['start_hour'] = (int)$settings['business_hours_start'];
        }
        if (isset($settings['business_hours_end'])) {
            $config['time_settings']['end_hour'] = (int)$settings['business_hours_end'];
        }
        if (isset($settings['timezone'])) {
            $config['time_settings']['timezone'] = $settings['timezone'];
        }
        if (isset($settings['frequency_minutes'])) {
            $config['frequency']['send_every_minutes'] = (int)$settings['frequency_minutes'];
        }
        if (isset($settings['max_reminders_per_day'])) {
            $config['frequency']['max_reminders_per_day'] = (int)$settings['max_reminders_per_day'];
        }
        if (isset($settings['email_enabled'])) {
            $config['email']['enabled'] = (bool)$settings['email_enabled'];
        }
        if (isset($settings['sms_enabled'])) {
            $config['sms']['enabled'] = (bool)$settings['sms_enabled'];
        }
        if (isset($settings['sms_api_url'])) {
            $config['sms']['api_url'] = $settings['sms_api_url'];
        }
        if (isset($settings['sms_api_key'])) {
            $config['sms']['api_key'] = $settings['sms_api_key'];
        }
        if (isset($settings['sms_sender'])) {
            $config['sms']['sender'] = $settings['sms_sender'];
        }
        if (isset($settings['email_subject'])) {
            $config['messages']['email_subject'] = $settings['email_subject'];
        }
        if (isset($settings['email_message'])) {
            $config['messages']['email_message'] = $settings['email_message'];
        }
        if (isset($settings['sms_message'])) {
            $config['messages']['sms_message'] = $settings['sms_message'];
        }
        
        return $config;
        
    } catch (Exception $e) {
        // Return default config if database fails
        return $default_config;
    }
}

// Function to save settings to database
function saveSettingsToDatabase($settings) {
    try {
        $db = Database::getInstance()->getConnection();
        
        foreach ($settings as $key => $value) {
            $stmt = $db->prepare("INSERT INTO reminder_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->bind_param('sss', $key, $value, $value);
            $stmt->execute();
            $stmt->close();
        }
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Function to check if it's time to send reminders
function shouldSendRemindersConfig($config) {
    $timezone = new DateTimeZone($config['time_settings']['timezone']);
    $now = new DateTime('now', $timezone);
    $current_hour = (int)$now->format('H');
    
    // Check if current time is within allowed hours
    if ($current_hour < $config['time_settings']['start_hour'] || 
        $current_hour >= $config['time_settings']['end_hour']) {
        return false;
    }
    
    // Check if we should send based on frequency
    $current_minute = (int)$now->format('i');
    $send_every = $config['frequency']['send_every_minutes'];
    
    // Send if current minute is divisible by the frequency
    return ($current_minute % $send_every) === 0;
}

// Function to check if user has reached daily limit
function checkDailyLimitConfig($user_id, $config) {
    try {
        $db = Database::getInstance()->getConnection();
        
        $today = date('Y-m-d');
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM reminder_logs 
            WHERE recipient = ? 
            AND DATE(sent_at) = ? 
            AND status = 'sent'
        ");
        $stmt->bind_param('ss', $user_id, $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['count'] < $config['frequency']['max_reminders_per_day'];
    } catch (Exception $e) {
        return false;
    }
}



// Load configuration from database
$reminder_config = loadSettingsFromDatabase();

// Export configuration
return $reminder_config;
?> 