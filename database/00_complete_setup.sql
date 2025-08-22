-- LSPU Employment and Information System (EIS) - Complete Database Setup
-- This file contains the complete database setup for the LSPU EIS system
-- Run this file to set up the entire database from scratch

-- =====================================================
-- STEP 1: Create Database
-- =====================================================
CREATE DATABASE IF NOT EXISTS lspu_eis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lspu_eis;

-- =====================================================
-- STEP 2: Create Main Tables
-- =====================================================

-- User table (main authentication table)
CREATE TABLE IF NOT EXISTS `user` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `secondary_email` VARCHAR(255) NULL,
    `password` VARCHAR(255) NOT NULL,
    `user_role` ENUM('admin', 'employer', 'alumni') NOT NULL,
    `status` ENUM('Active', 'Pending', 'Inactive') DEFAULT 'Pending',
    `reset_token` VARCHAR(255) NULL,
    `reset_token_expiry` TIMESTAMP NULL,
    `phone_number` VARCHAR(20) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Administrator table
CREATE TABLE IF NOT EXISTS `administrator` (
    `admin_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `middle_name` VARCHAR(100) NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `gender` ENUM('Male', 'Female', 'Other') NULL,
    `contact` VARCHAR(20) NULL,
    `position` VARCHAR(100) NULL,
    `department` VARCHAR(100) NULL,
    `profile_pic` VARCHAR(255) NULL,
    `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
    `address` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Alumni table
CREATE TABLE IF NOT EXISTS `alumni` (
    `alumni_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `middle_name` VARCHAR(100) NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `birthdate` DATE NULL,
    `contact` VARCHAR(20) NULL,
    `gender` ENUM('Male', 'Female', 'Other') NULL,
    `civil_status` ENUM('Single', 'Married', 'Widowed', 'Divorced') NULL,
    `city` VARCHAR(100) NULL,
    `province` VARCHAR(100) NULL,
    `year_graduated` YEAR NULL,
    `college` VARCHAR(100) NULL,
    `course` VARCHAR(100) NULL,
    `verification_document` VARCHAR(255) NULL,
    `profile_pic` VARCHAR(255) NULL,
    `phone_number` VARCHAR(20) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Employer table
CREATE TABLE IF NOT EXISTS `employer` (
    `employer_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `company_name` VARCHAR(255) NOT NULL,
    `company_logo` VARCHAR(255) NULL,
    `company_location` VARCHAR(255) NULL,
    `contact_email` VARCHAR(255) NULL,
    `contact_number` VARCHAR(20) NULL,
    `industry_type` VARCHAR(100) NULL,
    `nature_of_business` TEXT NULL,
    `tin` VARCHAR(50) NULL,
    `date_established` DATE NULL,
    `company_type` ENUM('Corporation', 'Partnership', 'Sole Proprietorship', 'Cooperative', 'Other') NULL,
    `accreditation_status` ENUM('Accredited', 'Pending', 'Not Accredited') DEFAULT 'Pending',
    `document_file` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Jobs table
CREATE TABLE IF NOT EXISTS `jobs` (
    `job_id` INT AUTO_INCREMENT PRIMARY KEY,
    `employer_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `type` ENUM('Full-time', 'Part-time', 'Contract', 'Internship', 'Freelance') NOT NULL,
    `location` VARCHAR(255) NOT NULL,
    `salary` VARCHAR(100) NULL,
    `status` ENUM('Active', 'Inactive', 'Closed') DEFAULT 'Active',
    `description` TEXT NOT NULL,
    `requirements` TEXT NULL,
    `qualifications` TEXT NULL,
    `employer_question` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`employer_id`) REFERENCES `employer`(`employer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Applications table
CREATE TABLE IF NOT EXISTS `applications` (
    `application_id` INT AUTO_INCREMENT PRIMARY KEY,
    `alumni_id` INT NOT NULL,
    `job_id` INT NOT NULL,
    `status` ENUM('Pending', 'Under Review', 'Shortlisted', 'Interviewed', 'Hired', 'Rejected') DEFAULT 'Pending',
    `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`alumni_id`) REFERENCES `alumni`(`alumni_id`) ON DELETE CASCADE,
    FOREIGN KEY (`job_id`) REFERENCES `jobs`(`job_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_application` (`alumni_id`, `job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages table
CREATE TABLE IF NOT EXISTS `messages` (
    `message_id` INT AUTO_INCREMENT PRIMARY KEY,
    `sender_email` VARCHAR(255) NOT NULL,
    `receiver_email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `role` ENUM('admin', 'employer', 'alumni') NOT NULL,
    `folder` ENUM('inbox', 'sent', 'important', 'trash') DEFAULT 'inbox',
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications table
CREATE TABLE IF NOT EXISTS `notifications` (
    `notification_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `message` TEXT NOT NULL,
    `details` JSON NULL,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Alumni Education table
CREATE TABLE IF NOT EXISTS `alumni_education` (
    `education_id` INT AUTO_INCREMENT PRIMARY KEY,
    `alumni_id` INT NOT NULL,
    `degree` VARCHAR(255) NOT NULL,
    `school` VARCHAR(255) NOT NULL,
    `start_date` DATE NULL,
    `end_date` DATE NULL,
    `current` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`alumni_id`) REFERENCES `alumni`(`alumni_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Alumni Experience table
CREATE TABLE IF NOT EXISTS `alumni_experience` (
    `experience_id` INT AUTO_INCREMENT PRIMARY KEY,
    `alumni_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `company` VARCHAR(255) NOT NULL,
    `start_date` DATE NULL,
    `end_date` DATE NULL,
    `current` BOOLEAN DEFAULT FALSE,
    `description` TEXT NULL,
    `location_of_work` VARCHAR(255) NULL,
    `employment_status` ENUM('Regular', 'Contractual', 'Probationary', 'Part-time', 'Freelance', 'Self-employed') NULL,
    `employment_sector` ENUM('Private', 'Government', 'Non-government', 'Academe', 'Self-employed', 'Others') NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`alumni_id`) REFERENCES `alumni`(`alumni_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Alumni Skills table
CREATE TABLE IF NOT EXISTS `alumni_skill` (
    `skill_id` INT AUTO_INCREMENT PRIMARY KEY,
    `alumni_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `certificate` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`alumni_id`) REFERENCES `alumni`(`alumni_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Alumni Resume table
CREATE TABLE IF NOT EXISTS `alumni_resume` (
    `resume_id` INT AUTO_INCREMENT PRIMARY KEY,
    `alumni_id` INT NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`alumni_id`) REFERENCES `alumni`(`alumni_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Saved Jobs table
CREATE TABLE IF NOT EXISTS `saved_jobs` (
    `saved_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `job_id` INT NOT NULL,
    `saved_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`job_id`) REFERENCES `jobs`(`job_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_saved_job` (`user_id`, `job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Skills table (for general skills)
CREATE TABLE IF NOT EXISTS `skills` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `certificate` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- STEP 3: Create Reminder System Tables
-- =====================================================

-- Reminder logs table
CREATE TABLE IF NOT EXISTS `reminder_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `type` ENUM('email', 'sms') NOT NULL,
    `recipient` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
    `error_message` TEXT NULL,
    `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reminder settings table
CREATE TABLE IF NOT EXISTS `reminder_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT NOT NULL,
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reminder statistics table
CREATE TABLE IF NOT EXISTS `reminder_statistics` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `date` DATE NOT NULL,
    `total_users` INT DEFAULT 0,
    `emails_sent` INT DEFAULT 0,
    `emails_failed` INT DEFAULT 0,
    `sms_sent` INT DEFAULT 0,
    `sms_failed` INT DEFAULT 0,
    `total_sent` INT DEFAULT 0,
    `total_failed` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User reminder preferences table
CREATE TABLE IF NOT EXISTS `user_reminder_preferences` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `email_enabled` BOOLEAN DEFAULT TRUE,
    `sms_enabled` BOOLEAN DEFAULT TRUE,
    `frequency_hours` INT DEFAULT 24,
    `last_reminder_sent` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SMS logs table
CREATE TABLE IF NOT EXISTS `sms_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sender_email` VARCHAR(255) NOT NULL,
    `receiver_phone` VARCHAR(20) NOT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('sent', 'failed', 'pending') NOT NULL DEFAULT 'pending',
    `sent_at` DATETIME NOT NULL,
    `user_role` ENUM('admin', 'employer', 'alumni') NOT NULL,
    `error_message` TEXT DEFAULT NULL,
    `api_response` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- STEP 4: Create Indexes for Performance
-- =====================================================

-- User table indexes
CREATE INDEX idx_user_email ON `user`(email);
CREATE INDEX idx_user_role ON `user`(user_role);
CREATE INDEX idx_user_status ON `user`(status);

-- Alumni table indexes
CREATE INDEX idx_alumni_user_id ON `alumni`(user_id);
CREATE INDEX idx_alumni_college ON `alumni`(college);
CREATE INDEX idx_alumni_course ON `alumni`(course);

-- Employer table indexes
CREATE INDEX idx_employer_user_id ON `employer`(user_id);
CREATE INDEX idx_employer_company_name ON `employer`(company_name);

-- Jobs table indexes
CREATE INDEX idx_jobs_employer_id ON `jobs`(employer_id);
CREATE INDEX idx_jobs_status ON `jobs`(status);
CREATE INDEX idx_jobs_type ON `jobs`(type);

-- Applications table indexes
CREATE INDEX idx_applications_alumni_id ON `applications`(alumni_id);
CREATE INDEX idx_applications_job_id ON `applications`(job_id);
CREATE INDEX idx_applications_status ON `applications`(status);

-- Messages table indexes
CREATE INDEX idx_messages_sender ON `messages`(sender_email);
CREATE INDEX idx_messages_receiver ON `messages`(receiver_email);
CREATE INDEX idx_messages_folder ON `messages`(folder);

-- Notifications table indexes
CREATE INDEX idx_notifications_user_id ON `notifications`(user_id);
CREATE INDEX idx_notifications_is_read ON `notifications`(is_read);

-- Reminder system indexes
CREATE INDEX idx_reminder_logs_sent_at ON `reminder_logs`(sent_at);
CREATE INDEX idx_reminder_logs_status ON `reminder_logs`(status);
CREATE INDEX idx_reminder_logs_type ON `reminder_logs`(type);
CREATE INDEX idx_reminder_logs_recipient ON `reminder_logs`(recipient);
CREATE INDEX idx_reminder_statistics_date ON `reminder_statistics`(date);
CREATE INDEX idx_user_reminder_preferences_user_id ON `user_reminder_preferences`(user_id);

-- SMS logs indexes
CREATE INDEX idx_sms_logs_sender_email ON `sms_logs`(sender_email);
CREATE INDEX idx_sms_logs_receiver_phone ON `sms_logs`(receiver_phone);
CREATE INDEX idx_sms_logs_status ON `sms_logs`(status);
CREATE INDEX idx_sms_logs_sent_at ON `sms_logs`(sent_at);
CREATE INDEX idx_sms_logs_user_role ON `sms_logs`(user_role);

-- =====================================================
-- STEP 5: Insert Default Data
-- =====================================================

-- Insert default admin user (Password: admin123)
INSERT INTO `user` (`email`, `password`, `user_role`, `status`) VALUES
('admin@lspu.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Active')
ON DUPLICATE KEY UPDATE `status` = 'Active';

-- Insert admin profile
INSERT INTO `administrator` (`user_id`, `first_name`, `middle_name`, `last_name`, `gender`, `contact`, `position`, `department`, `status`) 
SELECT u.user_id, 'System', 'Admin', 'Administrator', 'Male', '+639123456789', 'System Administrator', 'IT Department', 'Active'
FROM `user` u WHERE u.email = 'admin@lspu.edu.ph' AND u.user_role = 'admin'
ON DUPLICATE KEY UPDATE `status` = 'Active';

-- Insert default reminder settings
INSERT INTO `reminder_settings` (`setting_key`, `setting_value`, `description`) VALUES
('business_hours_start', '9', 'Start hour for business hours (24-hour format)'),
('business_hours_end', '18', 'End hour for business hours (24-hour format)'),
('timezone', 'Asia/Manila', 'Timezone for the reminder system'),
('frequency_minutes', '1', 'How often to send reminders (in minutes)'),
('max_reminders_per_day', '3', 'Maximum reminders per user per day'),
('email_enabled', '1', 'Enable email reminders (1=yes, 0=no)'),
('sms_enabled', '1', 'Enable SMS reminders (1=yes, 0=no)'),
('email_subject', 'LSPU EIS - Automated Reminder', 'Default email subject'),
('email_message', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'Default email message'),
('sms_message', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'Default SMS message')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- Insert sample skills
INSERT INTO `skills` (`name`) VALUES
('JavaScript'), ('PHP'), ('MySQL'), ('HTML/CSS'), ('React'), ('Node.js'), ('Python'), ('Java'), ('C++'), ('C#'),
('Adobe Photoshop'), ('Adobe Illustrator'), ('Microsoft Office'), ('Google Workspace'), ('Project Management'),
('Digital Marketing'), ('Content Writing'), ('Graphic Design'), ('Web Design'), ('Mobile App Development'),
('Data Analysis'), ('Machine Learning'), ('Artificial Intelligence'), ('Cybersecurity'), ('Network Administration'),
('Database Administration'), ('System Administration'), ('Cloud Computing'), ('DevOps'), ('Agile Methodology')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- =====================================================
-- STEP 6: Create Views for Reports and Analytics
-- =====================================================

-- Dashboard statistics view
CREATE OR REPLACE VIEW `dashboard_stats` AS
SELECT 
    (SELECT COUNT(*) FROM `user` WHERE `user_role` = 'alumni' AND `status` = 'Active') as total_alumni,
    (SELECT COUNT(*) FROM `user` WHERE `user_role` = 'employer' AND `status` = 'Active') as total_employers,
    (SELECT COUNT(*) FROM `jobs` WHERE `status` = 'Active') as active_jobs,
    (SELECT COUNT(*) FROM `applications` WHERE `status` = 'Pending') as pending_applications,
    (SELECT COUNT(*) FROM `applications` WHERE `status` = 'Hired') as hired_alumni,
    (SELECT COUNT(*) FROM `user` WHERE `user_role` = 'alumni' AND DATE(`created_at`) = CURDATE()) as new_alumni_today,
    (SELECT COUNT(*) FROM `user` WHERE `user_role` = 'employer' AND DATE(`created_at`) = CURDATE()) as new_employers_today,
    (SELECT COUNT(*) FROM `jobs` WHERE DATE(`created_at`) = CURDATE()) as new_jobs_today;

-- Employment statistics by college view
CREATE OR REPLACE VIEW `employment_by_college` AS
SELECT 
    a.college,
    COUNT(DISTINCT a.alumni_id) as total_graduates,
    COUNT(DISTINCT CASE WHEN e.current = 1 THEN a.alumni_id END) as employed_graduates,
    ROUND((COUNT(DISTINCT CASE WHEN e.current = 1 THEN a.alumni_id END) / COUNT(DISTINCT a.alumni_id)) * 100, 2) as employment_rate
FROM `alumni` a
LEFT JOIN `alumni_experience` e ON a.alumni_id = e.alumni_id
WHERE a.college IS NOT NULL
GROUP BY a.college
ORDER BY employment_rate DESC;

-- Employment statistics by course view
CREATE OR REPLACE VIEW `employment_by_course` AS
SELECT 
    a.course,
    COUNT(DISTINCT a.alumni_id) as total_graduates,
    COUNT(DISTINCT CASE WHEN e.current = 1 THEN a.alumni_id END) as employed_graduates,
    ROUND((COUNT(DISTINCT CASE WHEN e.current = 1 THEN a.alumni_id END) / COUNT(DISTINCT a.alumni_id)) * 100, 2) as employment_rate
FROM `alumni` a
LEFT JOIN `alumni_experience` e ON a.alumni_id = e.alumni_id
WHERE a.course IS NOT NULL
GROUP BY a.course
ORDER BY employment_rate DESC;

-- Recent activities view
CREATE OR REPLACE VIEW `recent_activities` AS
SELECT 
    'New Alumni Registration' as activity_type,
    CONCAT(a.first_name, ' ', a.last_name) as user_name,
    a.created_at as activity_date,
    a.college,
    a.course
FROM `alumni` a
WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
UNION ALL
SELECT 
    'New Job Posted' as activity_type,
    j.title as user_name,
    j.created_at as activity_date,
    e.company_name as college,
    j.type as course
FROM `jobs` j
JOIN `employer` e ON j.employer_id = e.employer_id
WHERE j.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
UNION ALL
SELECT 
    'New Application' as activity_type,
    CONCAT(a.first_name, ' ', a.last_name) as user_name,
    app.applied_at as activity_date,
    j.title as college,
    app.status as course
FROM `applications` app
JOIN `alumni` a ON app.alumni_id = a.alumni_id
JOIN `jobs` j ON app.job_id = j.job_id
WHERE app.applied_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY activity_date DESC;

-- =====================================================
-- STEP 7: Final Setup Complete
-- =====================================================

SELECT 'LSPU EIS Database Setup Complete!' as status;
SELECT 'Default Admin Login:' as info;
SELECT 'Email: admin@lspu.edu.ph' as email;
SELECT 'Password: admin123' as password;
SELECT 'Please change the default password after first login.' as security_note; 