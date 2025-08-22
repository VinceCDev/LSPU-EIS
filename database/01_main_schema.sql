-- LSPU Employment and Information System (EIS) Database Schema
-- Main Database Tables
-- Run this file first to create the core database structure

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS lspu_eis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lspu_eis;

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

-- Create indexes for better performance
CREATE INDEX idx_user_email ON `user`(email);
CREATE INDEX idx_user_role ON `user`(user_role);
CREATE INDEX idx_user_status ON `user`(user_status);
CREATE INDEX idx_alumni_user_id ON `alumni`(user_id);
CREATE INDEX idx_employer_user_id ON `employer`(user_id);
CREATE INDEX idx_jobs_employer_id ON `jobs`(employer_id);
CREATE INDEX idx_jobs_status ON `jobs`(status);
CREATE INDEX idx_applications_alumni_id ON `applications`(alumni_id);
CREATE INDEX idx_applications_job_id ON `applications`(job_id);
CREATE INDEX idx_messages_sender ON `messages`(sender_email);
CREATE INDEX idx_messages_receiver ON `messages`(receiver_email);
CREATE INDEX idx_notifications_user_id ON `notifications`(user_id);
CREATE INDEX idx_notifications_is_read ON `notifications`(is_read); 