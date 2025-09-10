-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2025 at 03:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lspu_eis`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE `administrator` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administrator`
--

INSERT INTO `administrator` (`admin_id`, `user_id`, `first_name`, `middle_name`, `last_name`, `gender`, `contact`, `position`, `department`, `profile_pic`, `status`, `last_login`, `created_at`, `updated_at`, `address`) VALUES
(2, 13, 'Juan', 'Santos', 'Dela', 'Male', '09171234567', 'System Administrator', 'IT Department', 'uploads/profile_picture/6880ea2b1d304_admin.jpeg', 'Active', NULL, '2025-07-23 21:33:21', '2025-07-23 22:00:54', 'San, Bieszczady County, Poland');

-- --------------------------------------------------------

--
-- Table structure for table `alumni`
--

CREATE TABLE `alumni` (
  `alumni_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `contact` varchar(20) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `civil_status` varchar(20) NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `year_graduated` int(11) NOT NULL,
  `college` varchar(100) NOT NULL,
  `course` varchar(100) NOT NULL,
  `verification_document` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni`
--

INSERT INTO `alumni` (`alumni_id`, `user_id`, `first_name`, `middle_name`, `last_name`, `birthdate`, `contact`, `gender`, `civil_status`, `city`, `province`, `year_graduated`, `college`, `course`, `verification_document`, `created_at`, `profile_pic`) VALUES
(1, 1, 'Vince', 'Durano', 'Cristal', '2025-07-10', '09566888221', 'Male', 'Single', 'Santa Cruz', 'Zambales', 2020, 'College of Computer Studies', 'BS Information Technology', '68a3a47c90e9c_Resume__20250818024331.pdf', '2025-07-19 04:03:54', '68a84d56eba74_CRISTALVINCEALLEND.-1x1.jpg'),
(3, 3, 'Allen', 'Durano', 'Cristal', '0000-00-00', '', 'Male', '', 'Tanza', 'Cavite', 2020, 'College of Computer Studies', 'BS Computer Science', '', '2025-07-19 05:32:24', NULL),
(4, 17, 'Vince', 'Blake', 'Cyprus', '2003-12-24', '09566888221', 'Male', 'Single', 'Silang', 'Cavite', 2025, 'College of Arts and Sciences', 'BS Biology', '68b576ec79c65_Office of Assessor.pdf', '2025-09-01 10:35:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `alumni_education`
--

CREATE TABLE `alumni_education` (
  `education_id` int(11) NOT NULL,
  `alumni_id` int(11) NOT NULL,
  `degree` varchar(255) NOT NULL,
  `school` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `current` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni_education`
--

INSERT INTO `alumni_education` (`education_id`, `alumni_id`, `degree`, `school`, `start_date`, `end_date`, `current`, `created_at`) VALUES
(2, 1, 'Allen', 'Allen1', '2024-07-25', '2025-08-25', 0, '2025-08-25 05:03:13');

-- --------------------------------------------------------

--
-- Table structure for table `alumni_experience`
--

CREATE TABLE `alumni_experience` (
  `experience_id` int(11) NOT NULL,
  `alumni_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `current` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `location_of_work` varchar(50) DEFAULT NULL,
  `employment_status` varchar(50) DEFAULT NULL,
  `employment_sector` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni_experience`
--

INSERT INTO `alumni_experience` (`experience_id`, `alumni_id`, `title`, `company`, `start_date`, `end_date`, `current`, `description`, `created_at`, `updated_at`, `location_of_work`, `employment_status`, `employment_sector`) VALUES
(3, 1, 'Software Engineer', 'Sweetooth', '2025-07-11', '2025-07-23', 1, 'This Job', '2025-07-22 23:24:52', '2025-07-22 23:24:52', 'Local', 'Probational', 'Private');

-- --------------------------------------------------------

--
-- Table structure for table `alumni_resume`
--

CREATE TABLE `alumni_resume` (
  `resume_id` int(11) NOT NULL,
  `alumni_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni_resume`
--

INSERT INTO `alumni_resume` (`resume_id`, `alumni_id`, `file_name`, `uploaded_at`) VALUES
(2, 1, '687e251da4f85_AlumniPath.pdf', '2025-07-21 19:31:41');

-- --------------------------------------------------------

--
-- Table structure for table `alumni_skill`
--

CREATE TABLE `alumni_skill` (
  `skill_id` int(11) NOT NULL,
  `alumni_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `certificate` varchar(255) DEFAULT NULL,
  `certificate_file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni_skill`
--

INSERT INTO `alumni_skill` (`skill_id`, `alumni_id`, `name`, `certificate`, `certificate_file`, `created_at`) VALUES
(4, 1, 'JavaScript', '', '', '2025-08-17 08:31:04'),
(5, 1, 'C#', '', '', '2025-08-17 08:31:18'),
(7, 1, 'Intravenous Hydration', 'IBM', '68c14b60186e6_1757498208.pdf', '2025-09-10 09:56:48');

-- --------------------------------------------------------

--
-- Table structure for table `applicant_checklist_progress`
--

CREATE TABLE `applicant_checklist_progress` (
  `id` int(11) NOT NULL,
  `onboarding_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicant_checklist_progress`
--

INSERT INTO `applicant_checklist_progress` (`id`, `onboarding_id`, `item_id`, `is_completed`, `completed_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2025-09-08 08:54:24', NULL, '2025-09-08 14:39:37', '2025-09-08 14:54:24'),
(2, 1, 2, 1, '2025-09-08 08:54:26', NULL, '2025-09-08 14:39:37', '2025-09-08 14:54:26'),
(3, 1, 3, 1, '2025-09-08 08:54:28', NULL, '2025-09-08 14:39:37', '2025-09-08 14:54:28'),
(4, 2, 1, 0, NULL, NULL, '2025-09-08 14:52:56', '2025-09-08 14:52:56'),
(5, 2, 2, 0, NULL, NULL, '2025-09-08 14:52:56', '2025-09-08 14:52:56'),
(6, 2, 3, 0, NULL, NULL, '2025-09-08 14:52:56', '2025-09-08 14:52:56'),
(7, 1, 1, 1, '2025-09-08 15:16:46', NULL, '2025-09-08 15:16:46', '2025-09-08 15:16:46'),
(8, 1, 2, 1, '2025-09-08 15:16:46', NULL, '2025-09-08 15:16:46', '2025-09-08 15:16:46'),
(9, 1, 3, 1, '2025-09-08 15:16:46', NULL, '2025-09-08 15:16:46', '2025-09-08 15:16:46'),
(10, 2, 1, 1, '2025-09-08 15:17:01', NULL, '2025-09-08 15:17:01', '2025-09-08 15:17:01'),
(11, 2, 2, 1, '2025-09-08 15:17:01', NULL, '2025-09-08 15:17:01', '2025-09-08 15:17:01'),
(12, 2, 3, 1, '2025-09-08 15:17:01', NULL, '2025-09-08 15:17:01', '2025-09-08 15:17:01');

-- --------------------------------------------------------

--
-- Table structure for table `applicant_onboarding`
--

CREATE TABLE `applicant_onboarding` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `checklist_id` int(11) NOT NULL,
  `completion_percentage` int(11) DEFAULT 0,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicant_onboarding`
--

INSERT INTO `applicant_onboarding` (`id`, `application_id`, `checklist_id`, `completion_percentage`, `status`, `started_at`, `completed_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 7, 1, 100, 'completed', '2025-09-08 14:39:37', '2025-09-08 15:16:46', NULL, '2025-09-08 14:39:37', '2025-09-08 17:27:16'),
(2, 2, 1, 100, 'completed', '2025-09-08 14:52:56', '2025-09-08 15:17:01', 'hi', '2025-09-08 14:52:56', '2025-09-08 15:17:14');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `alumni_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `applied_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `alumni_id`, `job_id`, `status`, `applied_at`) VALUES
(2, 1, 1, 'Hired', '2025-07-21 20:15:22'),
(4, 1, 7, 'Pending', '2025-07-22 17:15:17'),
(6, 1, 32, 'Interview', '2025-08-17 20:59:51'),
(7, 1, 50, 'Hired', '2025-09-04 18:59:02');

-- --------------------------------------------------------

--
-- Table structure for table `employer`
--

CREATE TABLE `employer` (
  `employer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
  `company_location` varchar(255) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `industry_type` varchar(100) NOT NULL,
  `nature_of_business` varchar(255) NOT NULL,
  `tin` varchar(50) NOT NULL,
  `date_established` date NOT NULL,
  `company_type` varchar(50) NOT NULL,
  `accreditation_status` varchar(50) NOT NULL,
  `document_file` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employer`
--

INSERT INTO `employer` (`employer_id`, `user_id`, `company_name`, `company_logo`, `company_location`, `contact_email`, `contact_number`, `industry_type`, `nature_of_business`, `tin`, `date_established`, `company_type`, `accreditation_status`, `document_file`, `created_at`) VALUES
(1, 5, 'AT', '687edf06390f1_PSFix_20250419_083203.jpeg', 'Paule 1 (Rizal), Laguna, Philippines', 'trevorlouvenne@gmail.com', '09566888221', 'Technology', 'IT Services', '123-456-789', '2025-07-08', 'Corporation', 'CHED', '687b4eee6ffeb_job_postings (3).pdf', '2025-07-23 18:37:25'),
(2, 10, 'ESPN', '687b4eee6fae1_PSFix_20250419_083203.jpeg', 'Credit River Catholic Cemetery, 17845 Texas Avenue, Prior Lake, MN 55372, United States of America', 'brightprint24@gmail.com', '09566888221', 'Sports', 'Basketball', '123-444-789', '2025-07-10', 'ICT', 'ISO', '687ca0d052aac_companies (4).pdf', '2025-07-23 18:37:25');

-- --------------------------------------------------------

--
-- Table structure for table `interviews`
--

CREATE TABLE `interviews` (
  `interview_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `alumni_id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `interview_date` datetime NOT NULL,
  `duration` int(11) DEFAULT 30,
  `interview_type` enum('In-person','Phone','Video Call') DEFAULT 'Video Call',
  `location` varchar(255) DEFAULT NULL,
  `status` enum('Scheduled','Completed','Cancelled','No Show') DEFAULT 'Scheduled',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interviews`
--

INSERT INTO `interviews` (`interview_id`, `application_id`, `job_id`, `alumni_id`, `employer_id`, `interview_date`, `duration`, `interview_type`, `location`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 6, 32, 1, 5, '2025-09-12 08:53:00', 30, 'Video Call', 'https://meet.google.com/ajg-zpjw-xkx', 'Completed', 'Agenda', '2025-09-07 22:52:28', '2025-09-07 23:49:38'),
(2, 6, 32, 1, 5, '2025-10-04 07:29:00', 30, 'Video Call', 'uuhiuhiu', 'Cancelled', 'Agenda', '2025-09-07 23:30:00', '2025-09-07 23:41:37'),
(3, 6, 32, 1, 5, '2025-09-19 07:50:00', 30, 'Video Call', 'd7u6f7u', 'Scheduled', 'Agenda', '2025-09-07 23:50:26', '2025-09-07 23:50:26'),
(4, 6, 32, 1, 5, '2025-09-12 08:03:00', 30, 'Video Call', 'j6tuj', 'Scheduled', 'hyrht', '2025-09-08 00:03:39', '2025-09-08 00:03:39'),
(5, 6, 32, 1, 5, '2025-09-15 08:13:00', 30, 'Video Call', 'drh5yh', 'Scheduled', '56y65', '2025-09-08 00:13:14', '2025-09-08 00:13:14'),
(6, 6, 32, 1, 5, '2025-09-08 08:15:00', 30, 'Phone', 'get45t', 'Scheduled', 'gfdgd', '2025-09-08 00:15:13', '2025-09-08 01:42:24'),
(7, 6, 32, 1, 5, '2025-09-06 08:21:00', 30, 'Video Call', 'ntjht', 'Scheduled', 'thty', '2025-09-08 00:21:29', '2025-09-08 00:21:29'),
(8, 6, 32, 1, 5, '2025-09-04 08:31:00', 30, 'Video Call', 'rtytry', 'Scheduled', 'tryr', '2025-09-08 00:31:20', '2025-09-08 00:31:20'),
(9, 6, 32, 1, 5, '2025-09-11 09:07:00', 30, 'Video Call', 't5et4', 'Scheduled', '4t54', '2025-09-08 01:07:13', '2025-09-08 01:07:13'),
(10, 6, 32, 1, 5, '2025-09-06 09:13:00', 30, 'Video Call', 'trytr', 'Scheduled', 'tryr', '2025-09-08 01:13:45', '2025-09-08 01:13:45');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `salary` varchar(100) DEFAULT NULL,
  `status` enum('Active','Closed') DEFAULT 'Active',
  `created_at` date NOT NULL,
  `description` text NOT NULL,
  `requirements` text NOT NULL,
  `qualifications` text NOT NULL,
  `employer_question` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `employer_id`, `title`, `type`, `location`, `salary`, `status`, `created_at`, `description`, `requirements`, `qualifications`, `employer_question`) VALUES
(1, 5, 'Software Engineer', 'Full-time', 'San Pablo, Laguna, Philippines', '59, 000', 'Active', '2025-07-03', 'This is a job', 'Must be an IT\r\nMust 4 year Graduate', 'Must be an IT\r\nMust 4 year Graduate', 'What is your life motto?\r\nWhat is your name?'),
(7, 5, 'Web Developer', 'Full-time', 'San, Bieszczady County, Poland', '60', 'Active', '2025-07-18', 'T', 'YY', 'Y', 'Y'),
(32, 5, 'Mobile Developer', 'Full-time', 'Alma Mnalasts - Sd Ramon, Paso Street, Bongabon, 3128 Nueva Ecija, Philippines', '80', 'Active', '2025-08-08', 'This job for IT', 'Diploma', 'Excellent in C#', 'How much is you salary?'),
(50, 5, 'Senior Mobile Developer', 'Full-time', 'Nagcarlan, Laguna, Philippines', '90, 000', 'Active', '2025-09-04', 'This is a job proficient in mobile development.', 'Diploma\r\nGraduate in IT', 'Good at C#', '');

-- --------------------------------------------------------

--
-- Table structure for table `job_match_leaderboard`
--

CREATE TABLE `job_match_leaderboard` (
  `match_id` int(11) NOT NULL,
  `alumni_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `match_percentage` int(11) NOT NULL,
  `matched_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_match_leaderboard`
--

INSERT INTO `job_match_leaderboard` (`match_id`, `alumni_id`, `job_id`, `match_percentage`, `matched_at`, `notified`) VALUES
(24, 1, 50, 80, '2025-09-04 10:52:49', 1),
(25, 3, 50, 60, '2025-09-04 10:52:57', 1),
(26, 4, 50, 10, '2025-09-04 10:53:06', 0);

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `attempt_time` datetime NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `failure_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `email`, `ip_address`, `user_agent`, `attempt_time`, `status`, `failure_reason`) VALUES
(1, 13, 'admin.lspu1@example.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-09-04 17:47:12', 'success', NULL),
(2, 13, 'admin.lspu1@example.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 01:47:50', 'success', NULL),
(3, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 01:48:15', 'success', NULL),
(4, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 03:22:54', 'success', NULL),
(5, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 03:38:06', 'success', NULL),
(6, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 03:39:28', 'success', NULL),
(7, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 03:42:32', 'success', NULL),
(8, 13, 'admin.lspu1@example.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 03:44:25', 'success', NULL),
(9, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 03:45:24', 'success', NULL),
(10, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:18:33', 'failed', 'Failed to send 2FA email'),
(11, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:19:32', '', NULL),
(12, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:25:21', '', NULL),
(13, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:29:06', '', NULL),
(14, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:32:43', '', NULL),
(15, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 10:33:06', 'success', ''),
(16, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 04:34:53', '', NULL),
(17, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 10:35:04', 'success', ''),
(18, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 04:35:18', 'failed', 'Invalid credentials'),
(19, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 04:35:22', 'failed', 'Invalid credentials'),
(20, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 04:35:26', 'failed', 'Invalid credentials'),
(21, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 04:35:29', 'failed', 'Invalid credentials'),
(22, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 04:35:32', 'failed', 'Invalid credentials'),
(23, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 04:35:35', 'failed', 'Invalid credentials'),
(24, 5, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 04:49:59', 'success', NULL),
(25, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:50:09', 'success', NULL),
(26, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:16', 'success', NULL),
(27, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:30', 'failed', 'Invalid credentials'),
(28, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:34', 'failed', 'Invalid credentials'),
(29, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:38', 'failed', 'Invalid credentials'),
(30, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:42', 'failed', 'Invalid credentials'),
(31, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:47', 'failed', 'Invalid credentials'),
(32, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:54', 'failed', 'Invalid credentials'),
(33, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:55', 'failed', 'Invalid credentials'),
(34, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:55', 'failed', 'Invalid credentials'),
(35, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:55', 'failed', 'Invalid credentials'),
(36, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:56', 'failed', 'Invalid credentials'),
(37, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:56', 'failed', 'Invalid credentials'),
(38, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:56', 'failed', 'Invalid credentials'),
(39, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:57', 'failed', 'Invalid credentials'),
(40, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:57', 'failed', 'Invalid credentials'),
(41, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:57', 'failed', 'Invalid credentials'),
(42, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:57', 'failed', 'Invalid credentials'),
(43, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:57', 'failed', 'Invalid credentials'),
(44, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:57', 'failed', 'Invalid credentials'),
(45, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:57', 'failed', 'Invalid credentials'),
(46, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:58', 'failed', 'Invalid credentials'),
(47, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:58', 'failed', 'Invalid credentials'),
(48, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 04:53:58', 'failed', 'Invalid credentials'),
(49, 17, 'ethancyrus25@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 08:20:03', 'failed', 'Account not active'),
(50, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 08:20:13', 'success', NULL),
(51, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:24:56', 'success', NULL),
(52, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:26:33', 'success', NULL),
(53, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:32:47', '', '2FA required due to inactive login'),
(54, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:33:10', 'failed', 'Invalid 2FA code'),
(55, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:33:23', 'failed', 'Invalid 2FA code'),
(56, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:33:48', 'success', ''),
(57, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:38:28', 'success', NULL),
(58, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:39:23', 'success', NULL),
(59, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:42:00', 'success', NULL),
(60, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:42:23', 'success', NULL),
(61, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:44:56', 'success', NULL),
(62, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:47:57', 'success', NULL),
(63, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:49:15', 'success', NULL),
(64, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:52:01', 'success', NULL),
(65, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:53:00', '', '2FA required due to inactive login'),
(66, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 08:56:32', '', '2FA required due to inactive login'),
(67, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:00:02', '', '2FA required due to inactive login'),
(68, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:05:36', '', '2FA required due to inactive login'),
(69, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:06:01', 'success', ''),
(70, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:18', 'failed', 'Invalid credentials'),
(71, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:20', 'failed', 'Invalid credentials'),
(72, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:21', 'failed', 'Invalid credentials'),
(73, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:21', 'failed', 'Invalid credentials'),
(74, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:22', 'failed', 'Invalid credentials'),
(75, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:23', 'failed', 'Invalid credentials'),
(76, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:24', 'failed', 'Invalid credentials'),
(77, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:24', 'failed', 'Invalid credentials'),
(78, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:25', 'failed', 'Invalid credentials'),
(79, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:25', 'failed', 'Invalid credentials'),
(80, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:25', 'failed', 'Invalid credentials'),
(81, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:25', 'failed', 'Invalid credentials'),
(82, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:25', 'failed', 'Invalid credentials'),
(83, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:25', 'failed', 'Invalid credentials'),
(84, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 09:07:26', 'failed', 'Invalid credentials'),
(85, 1, 'allencristal12@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:46:18', 'success', NULL),
(86, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:46:32', 'failed', 'Invalid credentials'),
(87, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:46:39', 'failed', 'Invalid credentials'),
(88, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:46:40', 'failed', 'Invalid credentials'),
(89, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:46:41', 'failed', 'Invalid credentials'),
(90, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:46:42', 'failed', 'Invalid credentials'),
(91, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:46:42', 'failed', 'Invalid credentials'),
(92, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:46:43', 'failed', 'Invalid credentials'),
(93, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:53:49', 'success', NULL),
(94, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:06', 'failed', 'Invalid credentials'),
(95, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:10', 'failed', 'Invalid credentials'),
(96, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:14', 'failed', 'Invalid credentials'),
(97, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:19', 'failed', 'Invalid credentials'),
(98, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:23', 'failed', 'Invalid credentials'),
(99, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:24', 'failed', 'Invalid credentials'),
(100, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:25', 'failed', 'Invalid credentials'),
(101, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:25', 'failed', 'Invalid credentials'),
(102, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:25', 'failed', 'Invalid credentials'),
(103, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:25', 'failed', 'Invalid credentials'),
(104, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:25', 'failed', 'Invalid credentials'),
(105, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:26', 'failed', 'Invalid credentials'),
(106, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:26', 'failed', 'Invalid credentials'),
(107, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:26', 'failed', 'Invalid credentials'),
(108, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:26', 'failed', 'Invalid credentials'),
(109, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:26', 'failed', 'Invalid credentials'),
(110, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:27', 'failed', 'Invalid credentials'),
(111, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:27', 'failed', 'Invalid credentials'),
(112, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:54:27', 'failed', 'Invalid credentials'),
(113, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 11:56:01', 'success', NULL),
(114, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:06:53', 'failed', 'Invalid credentials'),
(115, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:06:54', 'failed', 'Invalid credentials'),
(116, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:06:55', 'failed', 'Invalid credentials'),
(117, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:06:56', 'failed', 'Invalid credentials'),
(118, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:06:57', 'failed', 'Invalid credentials'),
(119, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:06:58', 'failed', 'Invalid credentials'),
(120, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:07:02', 'failed', 'Invalid credentials'),
(121, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:10:06', 'success', NULL),
(122, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:10:17', 'failed', 'Invalid credentials'),
(123, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:10:18', 'failed', 'Invalid credentials'),
(124, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:10:19', 'failed', 'Invalid credentials'),
(125, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:10:20', 'failed', 'Invalid credentials'),
(126, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:10:21', 'failed', 'Invalid credentials'),
(127, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:10:22', 'failed', 'Invalid credentials'),
(128, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:10:22', 'failed', 'Invalid credentials'),
(129, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:10:22', 'failed', 'Invalid credentials'),
(130, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:10:23', 'failed', 'Invalid credentials'),
(131, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:10:23', 'failed', 'Invalid credentials'),
(132, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 12:10:24', 'failed', 'Invalid credentials'),
(133, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 13:11:57', 'success', NULL),
(134, 5, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 13:12:14', 'success', NULL),
(135, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 13:12:52', 'success', NULL),
(136, 13, 'admin.lspu1@example.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 13:14:36', 'success', NULL),
(137, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 13:15:50', 'success', NULL),
(138, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 14:52:57', 'success', NULL),
(139, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 15:02:02', 'success', NULL),
(140, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 15:02:14', 'failed', 'Invalid credentials'),
(141, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 15:02:15', 'failed', 'Invalid credentials'),
(142, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 15:02:16', 'failed', 'Invalid credentials'),
(143, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 15:02:16', 'failed', 'Invalid credentials'),
(144, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 15:02:17', 'failed', 'Invalid credentials'),
(145, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 15:02:18', 'failed', 'Invalid credentials'),
(146, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 15:02:18', 'failed', 'Invalid credentials'),
(147, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 15:02:19', 'failed', 'Invalid credentials'),
(148, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 15:02:19', 'failed', 'Invalid credentials'),
(149, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-05 15:02:20', 'failed', 'Invalid credentials'),
(150, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:02:32', 'failed', 'Invalid credentials'),
(151, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:15', 'success', NULL),
(152, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:26', 'failed', 'Invalid credentials'),
(153, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:27', 'failed', 'Invalid credentials'),
(154, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:28', 'failed', 'Invalid credentials'),
(155, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:29', 'failed', 'Invalid credentials'),
(156, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:30', 'failed', 'Invalid credentials'),
(157, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:30', 'failed', 'Invalid credentials'),
(158, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:31', 'failed', 'Invalid credentials'),
(159, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:32', 'failed', 'Invalid credentials'),
(160, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:33', 'failed', 'Invalid credentials'),
(161, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:33', 'failed', 'Invalid credentials'),
(162, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:34', 'failed', 'Invalid credentials'),
(163, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:34', 'failed', 'Invalid credentials'),
(164, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:35', 'failed', 'Invalid credentials'),
(165, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:35', 'failed', 'Invalid credentials'),
(166, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:35', 'failed', 'Invalid credentials'),
(167, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:36', 'failed', 'Invalid credentials'),
(168, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:37', 'failed', 'Invalid credentials'),
(169, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:08:38', 'failed', 'Invalid credentials'),
(170, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:16:41', 'failed', 'Invalid credentials'),
(171, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:16:46', 'failed', 'Invalid credentials'),
(172, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:16:50', 'failed', 'Invalid credentials'),
(173, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:16:55', 'failed', 'Invalid credentials'),
(174, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:17:00', 'failed', 'Invalid credentials'),
(175, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:17:03', 'failed', 'Invalid credentials'),
(176, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:17:14', 'failed', 'Invalid credentials'),
(177, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:17:20', 'failed', 'Invalid credentials'),
(178, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:17:31', 'failed', 'Invalid credentials'),
(179, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:17:34', 'failed', 'Invalid credentials'),
(180, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:17:35', 'failed', 'Invalid credentials'),
(181, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:34:11', 'failed', 'Invalid credentials'),
(182, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:34:12', 'failed', 'Invalid credentials'),
(183, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:34:13', 'failed', 'Invalid credentials'),
(184, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:34:14', 'failed', 'Invalid credentials'),
(185, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 15:34:14', 'failed', 'Invalid credentials'),
(186, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 16:00:05', 'success', NULL),
(187, NULL, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 16:03:02', 'failed', 'Invalid credentials'),
(188, NULL, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 16:03:04', 'failed', 'Invalid credentials'),
(189, NULL, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 16:03:05', 'failed', 'Invalid credentials'),
(190, NULL, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 16:03:06', 'failed', 'Invalid credentials'),
(191, NULL, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 16:03:07', 'failed', 'Invalid credentials'),
(192, 5, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-05 16:09:27', 'success', NULL),
(193, 5, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-08 00:48:02', 'success', NULL),
(194, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-08 02:22:34', 'failed', 'Invalid credentials'),
(195, NULL, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-08 02:22:41', 'failed', 'Invalid credentials'),
(196, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-08 02:22:56', 'success', NULL),
(197, 5, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-08 03:06:52', 'success', NULL),
(198, 5, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-08 14:13:54', 'success', NULL),
(199, 5, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-08 16:14:22', 'success', NULL),
(200, 5, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-08 17:39:22', 'success', NULL),
(201, 13, 'admin.lspu1@example.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-08 19:28:32', 'success', NULL),
(202, 5, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-08 19:30:33', 'success', NULL),
(203, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-08 19:30:41', 'success', NULL),
(204, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-09 14:25:08', 'success', NULL),
(205, 13, 'admin.lspu1@example.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-09 14:35:12', 'success', NULL),
(206, 5, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-09 15:59:56', 'success', NULL),
(207, 13, 'admin.lspu1@example.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-09 16:17:50', 'success', NULL),
(208, 5, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-09 16:33:57', 'success', NULL),
(209, 1, 'allencristal12@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-10 10:23:56', 'success', NULL),
(210, 13, 'admin.lspu1@example.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-10 12:58:04', 'success', NULL),
(211, 5, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-10 13:06:11', 'success', NULL),
(212, 5, 'trevorlouvenne@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-10 14:08:59', 'success', NULL),
(213, NULL, 'allencristal23@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-10 14:45:48', 'failed', 'Invalid credentials'),
(214, 1, 'allencristal12@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-10 14:45:54', 'success', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `receiver_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `folder` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_email`, `receiver_email`, `subject`, `message`, `role`, `created_at`, `folder`) VALUES
(1, 'admin.lspu1@example.com', 'admin.lspu1@example.com', 'gtrrtyg', '<p>ytry</p>', 'admin', '2025-07-24 02:35:50', 'inbox'),
(2, 'admin.lspu1@example.com', 'allencristal12@gmail.com', 'rwsfger', '<p>egtterter</p>', 'alumni', '2025-07-24 02:38:12', 'sent'),
(3, 'trevorlouvenne@gmail.com', 'admin.lspu1@example.com', 'yht', '<p>yutyu</p>', 'admin', '2025-07-27 14:11:22', 'inbox'),
(4, 'allencristal12@gmail.com', 'trevorlouvenne@gmail.com', 'hi', '<p>hi</p>', 'Employer', '2025-08-19 07:04:58', 'sent'),
(5, 'admin.lspu1@example.com', 'trevorlouvenne@gmail.com', 'Re: yht', '<p><br></p><p><br></p><blockquote>On 7/27/2025, 2:11:22 PM, trevorlouvenne@gmail.com wrote:</blockquote><blockquote>&gt; yutyu</blockquote><blockquote>HII</blockquote><p><br></p>', 'employer', '2025-08-19 08:36:44', 'sent'),
(6, 'admin.lspu1@example.com', 'admin.lspu1@example.com', 'Re: gtrrtyg', '<p><br></p><p><br></p><blockquote>On 7/24/2025, 2:35:50 AM, admin.lspu1@example.com wrote:</blockquote><blockquote>&gt; ytry</blockquote><p>hello</p>', 'admin', '2025-08-19 08:44:40', 'inbox'),
(7, 'trevorlouvenne@gmail.com', 'admin.lspu1@example.com', 'hi', '<p>joo</p>', 'admin', '2025-08-19 22:08:18', 'sent'),
(8, 'trevorlouvenne@gmail.com', 'allencristal12@gmail.com', 'hi', '<p>hi</p>', 'admin', '2025-08-30 12:55:26', NULL),
(9, 'trevorlouvenne@gmail.com', 'allencristal12@gmail.com', 'Interview Scheduled: Mobile Developer at AT', 'Your interview for Mobile Developer at AT has been scheduled for September 6, 2025 at 8:21 AM. Interview type: Video Call. Notes: thty', 'employer', '0000-00-00 00:00:00', 'sent'),
(10, 'trevorlouvenne@gmail.com', 'allencristal12@gmail.com', 'Interview Scheduled: Mobile Developer at AT', 'Your interview for Mobile Developer at AT has been scheduled for September 4, 2025 at 8:31 AM. Interview type: Video Call. Notes: tryr', 'employer', '2025-09-08 08:31:24', 'sent'),
(11, 'trevorlouvenne@gmail.com', 'allencristal12@gmail.com', 'Interview Scheduled: Mobile Developer at AT', 'Your interview for Mobile Developer at AT has been scheduled for September 11, 2025 at 9:07 AM. Interview type: Video Call. Notes: 4t54', 'employer', '2025-09-08 03:07:17', 'sent'),
(12, 'trevorlouvenne@gmail.com', 'allencristal12@gmail.com', 'Interview Scheduled: Mobile Developer at AT', 'Your interview for Mobile Developer at AT has been scheduled for September 11, 2025 at 9:07 AM. Interview type: Video Call. Notes: 4t54', 'alumni', '2025-09-08 03:07:17', 'inbox'),
(13, 'trevorlouvenne@gmail.com', 'allencristal12@gmail.com', 'Interview Scheduled: Mobile Developer at AT', 'Your interview for Mobile Developer at AT has been scheduled for September 6, 2025 at 9:13 AM. Interview type: Video Call. Video call link: trytr. Notes: tryr', 'employer', '2025-09-08 03:13:49', 'sent'),
(14, 'trevorlouvenne@gmail.com', 'allencristal12@gmail.com', 'Interview Scheduled: Mobile Developer at AT', 'Your interview for Mobile Developer at AT has been scheduled for September 6, 2025 at 9:13 AM. Interview type: Video Call. Video call link: trytr. Notes: tryr', 'alumni', '2025-09-08 03:13:49', 'inbox'),
(15, 'trevorlouvenne@gmail.com', 'allencristal12@gmail.com', 'Interview Rescheduled: Mobile Developer at AT', 'Your interview for Mobile Developer at AT has been rescheduled to September 20, 2025 at 9:15 PM.\n\nInterview type: Phone\nVideo call link: get45t\nNotes: gfdgd\n', 'employer', '2025-09-08 03:15:20', 'sent'),
(16, 'trevorlouvenne@gmail.com', 'allencristal12@gmail.com', 'Interview Rescheduled: Mobile Developer at AT', 'Your interview for Mobile Developer at AT has been rescheduled to September 20, 2025 at 9:15 PM.\n\nInterview type: Phone\nVideo call link: get45t\nNotes: gfdgd\n', 'alumni', '2025-09-08 03:15:20', 'inbox'),
(17, 'trevorlouvenne@gmail.com', 'allencristal12@gmail.com', 'Interview Rescheduled: Mobile Developer at AT', 'Your interview for Mobile Developer at AT has been rescheduled to September 8, 2025 at 8:15 AM.\n\nInterview type: Phone\nVideo call link: get45t\nNotes: gfdgd\n', 'employer', '2025-09-08 03:42:28', 'sent'),
(18, 'trevorlouvenne@gmail.com', 'allencristal12@gmail.com', 'Interview Rescheduled: Mobile Developer at AT', 'Your interview for Mobile Developer at AT has been rescheduled to September 8, 2025 at 8:15 AM.\n\nInterview type: Phone\nVideo call link: get45t\nNotes: gfdgd\n', 'alumni', '2025-09-08 03:42:28', 'inbox');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('application','hired','password','job_match','system') NOT NULL,
  `message` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `details`, `job_id`, `is_read`, `created_at`) VALUES
(1, 1, 'application', 'You successfully applied for a job.', 'You have applied for the position of Web Developer. Please wait for further announcement.', 0, 1, '2025-07-22 12:57:33'),
(2, 1, 'job_match', 'New job matches your course!', 'A new job posting for \'Web Developer\' matches your course: BS Information Technology. Check it out!', NULL, 1, '2025-07-22 13:55:51'),
(3, 3, 'job_match', 'New job matches your course!', 'A new job posting for \'Web Developer\' matches your course: BS Computer Science. Check it out!', NULL, 0, '2025-07-22 13:55:53'),
(4, 1, 'application', 'You successfully applied for a job.', 'You have applied for the position of Web Developer. Please wait for further announcement.', NULL, 1, '2025-07-22 17:15:17'),
(5, 1, 'application', 'Your application status has been updated', 'Congratulations! You have been hired for the position at AT.', NULL, 1, '2025-07-22 11:15:40'),
(6, 1, 'job_match', 'New job matches your course!', 'A new job posting for \'Software Engineer\' matches your course: BS Information Technology. Check it out!', NULL, 1, '2025-08-15 10:19:14'),
(7, 3, 'job_match', 'New job matches your course!', 'A new job posting for \'Software Engineer\' matches your course: BS Computer Science. Check it out!', NULL, 0, '2025-08-15 10:19:15'),
(8, 1, 'application', 'You successfully applied for a job.', 'You have applied for the position of Software Engineer. Please wait for further announcement.', NULL, 1, '2025-08-15 10:23:33'),
(9, 1, 'application', 'Your application status has been updated', 'Congratulations! You have been hired for the position at AT.', NULL, 1, '2025-08-15 04:24:39'),
(10, 1, 'application', 'Your application status has been updated', 'Congratulations! You have been hired for the position at AT.', NULL, 1, '2025-08-15 04:24:46'),
(11, 1, 'application', 'Your application status has been updated', 'Congratulations! You have been hired for the position at AT.', NULL, 1, '2025-08-15 04:24:53'),
(12, 1, 'application', 'Your application status has been updated', 'You have been invited for an interview for the position at AT.', NULL, 1, '2025-08-17 10:14:52'),
(13, 1, 'job_match', 'New job matches your profile!', 'A new job posting for \'Web Developer\' matches your background. Check it out!', NULL, 1, '2025-08-17 17:09:09'),
(14, 3, 'job_match', 'New job matches your profile!', 'A new job posting for \'Web Developer\' matches your background. Check it out!', NULL, 0, '2025-08-17 17:09:10'),
(15, 1, 'job_match', 'New job matches your profile!', 'A new job posting for \'Mobile Developer\' matches your background. Check it out!', NULL, 1, '2025-08-17 17:25:12'),
(16, 1, 'application', 'You successfully applied for a job.', 'You have applied for the position of Mobile Developer. Please wait for further announcement.', NULL, 1, '2025-08-17 20:59:51'),
(17, 1, 'job_match', 'New job matches your profile!', 'A new job posting for \'Software Engineer\' matches your background with a % match rate. Check it out!', NULL, 1, '2025-08-30 08:34:24'),
(18, 3, 'job_match', 'New job matches your profile!', 'A new job posting for \'Software Engineer\' matches your background with a % match rate. Check it out!', NULL, 0, '2025-08-30 08:34:33'),
(19, 1, 'job_match', 'New job matches your profile!', 'A new job posting for \'Software Engineer\' matches your background with a % match rate. Check it out!', NULL, 1, '2025-08-30 08:36:23'),
(20, 3, 'job_match', 'New job matches your profile!', 'A new job posting for \'Software Engineer\' matches your background with a % match rate. Check it out!', NULL, 0, '2025-08-30 08:36:33'),
(21, 1, 'job_match', 'New job match found (80%)!', 'The \'Software Engineer\' position has a 80% match with your skills and background. We think it\'s a great fit!', NULL, 1, '2025-08-30 08:42:53'),
(22, 3, 'job_match', 'New job match found (80%)!', 'The \'Software Engineer\' position has a 80% match with your skills and background. We think it\'s a great fit!', NULL, 0, '2025-08-30 08:43:01'),
(23, 1, 'job_match', 'New job match found (75%)!', 'The \'Software Engineer\' position has a 75% match with your skills and background. We think it\'s a great fit!', NULL, 1, '2025-08-30 10:37:09'),
(24, 1, 'job_match', 'New job match found (75%)!', 'The \'Software Engineer\' position has a 75% match with your skills and background. We think it\'s a great fit! [JOB_ID:40]', NULL, 1, '2025-08-31 04:41:01'),
(25, 3, 'job_match', 'New job match found (70%)!', 'The \'Software Engineer\' position has a 70% match with your skills and background. We think it\'s a great fit! [JOB_ID:40]', NULL, 0, '2025-08-31 04:41:10'),
(26, 1, 'job_match', 'New job match found (60%)!', 'The \'Web Developer\' position has a 60% match with your skills and background. We think it\'s a great fit! [JOB_ID:45]', 45, 1, '2025-09-04 16:41:40'),
(27, 1, 'job_match', 'New job match found (80%)!', 'New job match found (80%)!', 47, 1, '2025-09-04 18:25:25'),
(28, 3, 'job_match', 'New job match found (60%)!', 'New job match found (60%)!', 47, 0, '2025-09-04 18:25:34'),
(29, 1, 'job_match', 'New job matches your profile!', 'A new job posting for \'Senior Mobile Developer\' (80% match) matches your background. Check it out!', 50, 1, '2025-09-04 18:52:49'),
(30, 3, 'job_match', 'New job matches your profile!', 'A new job posting for \'Senior Mobile Developer\' (60% match) matches your background. Check it out!', 50, 0, '2025-09-04 18:52:57'),
(31, 1, 'application', 'You successfully applied for a job.', 'You have applied for the position of Senior Mobile Developer. Please wait for further announcement.', 50, 1, '2025-09-04 18:59:02'),
(32, 1, 'application', 'Your application status has been updated', 'Congratulations! You have been hired for the position at AT.', NULL, 1, '2025-09-04 14:51:28'),
(33, 1, 'application', 'Your application status has been updated', 'You have been invited for an interview for the position at AT.', NULL, 1, '2025-09-04 15:22:31'),
(34, 1, 'application', 'Your application status has been updated', 'We regret to inform you that you were not selected for the position at AT.', NULL, 1, '2025-09-04 15:46:23'),
(35, 1, 'application', 'Your application status has been updated', 'Congratulations! You have been hired for the position at AT.', NULL, 1, '2025-09-05 13:12:21'),
(36, 1, 'application', 'Your application status has been updated', 'Congratulations! You have been hired for the position at AT.', NULL, 1, '2025-09-05 13:12:29'),
(37, 1, 'application', 'Your application status has been updated', 'Congratulations! You have been hired for the position at AT.', NULL, 1, '2025-09-05 13:12:35'),
(38, 1, '', 'Interview scheduled for Mobile Developer at AT', '{\"interview_id\":7,\"job_id\":32,\"job_title\":\"Mobile Developer\",\"company_name\":\"AT\",\"interview_date\":\"2025-09-06 08:21:00\",\"interview_type\":\"Video Call\",\"location\":\"ntjht\",\"action\":\"scheduled\"}', 32, 1, '2025-09-08 08:21:29'),
(39, 1, '', 'Interview scheduled for Mobile Developer at AT', '{\"interview_id\":8,\"job_id\":32,\"job_title\":\"Mobile Developer\",\"company_name\":\"AT\",\"interview_date\":\"2025-09-04 08:31:00\",\"interview_type\":\"Video Call\",\"location\":\"rtytry\",\"action\":\"scheduled\"}', 32, 1, '2025-09-08 08:31:20'),
(40, 1, '', 'Interview scheduled for Mobile Developer at AT', 'You have scheduled an interview for Mobile Developer at AT. Date: . Type: . Location: . Please check your email and messages for complete details.', 32, 1, '2025-09-08 09:07:14'),
(41, 1, '', 'Interview scheduled for Mobile Developer at AT', 'You have scheduled an interview for Mobile Developer at AT. Date: . Type: . Location: . Please check your email and messages for complete details.', 32, 1, '2025-09-08 09:13:45'),
(42, 1, '', 'Interview rescheduled for Mobile Developer at AT', 'You have scheduled an interview for Mobile Developer at AT. Date: . Type: . Location: . Please check your email and messages for complete details.', 32, 1, '2025-09-08 09:15:17'),
(43, 1, '', 'Interview rescheduled for Mobile Developer at AT', 'You have scheduled an interview for Mobile Developer at AT. Please check your email and messages for complete details.', 32, 1, '2025-09-08 09:42:24');

-- --------------------------------------------------------

--
-- Table structure for table `onboarding_checklist`
--

CREATE TABLE `onboarding_checklist` (
  `id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_custom` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `onboarding_checklist`
--

INSERT INTO `onboarding_checklist` (`id`, `employer_id`, `title`, `description`, `is_custom`, `created_at`, `updated_at`) VALUES
(1, 5, 'None', 'None', 1, '2025-09-08 13:30:13', '2025-09-08 13:30:13');

-- --------------------------------------------------------

--
-- Table structure for table `onboarding_checklist_items`
--

CREATE TABLE `onboarding_checklist_items` (
  `id` int(11) NOT NULL,
  `checklist_id` int(11) NOT NULL,
  `item_text` varchar(500) NOT NULL,
  `is_required` tinyint(1) DEFAULT 0,
  `item_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `onboarding_checklist_items`
--

INSERT INTO `onboarding_checklist_items` (`id`, `checklist_id`, `item_text`, `is_required`, `item_order`, `created_at`) VALUES
(1, 1, 'Send welcome email', 1, 1, '2025-09-08 13:30:13'),
(2, 1, 'Provide login credentials', 1, 2, '2025-09-08 13:30:13'),
(3, 1, 'Schedule orientation', 1, 3, '2025-09-08 13:30:13');

-- --------------------------------------------------------

--
-- Table structure for table `onboarding_emails`
--

CREATE TABLE `onboarding_emails` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `email_type` varchar(50) NOT NULL,
  `sent_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `onboarding_emails`
--

INSERT INTO `onboarding_emails` (`id`, `application_id`, `email_type`, `sent_at`, `created_at`) VALUES
(1, 7, 'welcome', '2025-09-09 01:25:46', '2025-09-08 17:13:58');

-- --------------------------------------------------------

--
-- Table structure for table `reminder_logs`
--

CREATE TABLE `reminder_logs` (
  `id` int(11) NOT NULL,
  `type` enum('email','sms') NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('sent','failed','pending') DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reminder_logs`
--

INSERT INTO `reminder_logs` (`id`, `type`, `recipient`, `subject`, `message`, `status`, `error_message`, `sent_at`, `created_at`) VALUES
(1, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:08:41', '2025-07-27 09:08:41'),
(2, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-07-27 09:08:41', '2025-07-27 09:08:41'),
(3, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:21:47', '2025-07-27 09:21:47'),
(4, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 09:21:47', '2025-07-27 09:21:47'),
(5, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:26:09', '2025-07-27 09:26:09'),
(6, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 09:26:09', '2025-07-27 09:26:09'),
(7, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:28:40', '2025-07-27 09:28:40'),
(8, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:28:40', '2025-07-27 09:28:40'),
(9, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:30:47', '2025-07-27 09:30:47'),
(10, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:30:47', '2025-07-27 09:30:47'),
(11, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:33:39', '2025-07-27 09:33:39'),
(12, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:33:40', '2025-07-27 09:33:40'),
(13, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:33:40', '2025-07-27 09:33:40'),
(14, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:33:41', '2025-07-27 09:33:41'),
(15, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:33:49', '2025-07-27 09:33:49'),
(16, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:33:49', '2025-07-27 09:33:49'),
(17, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:34:40', '2025-07-27 09:34:40'),
(18, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:34:41', '2025-07-27 09:34:41'),
(19, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:38:04', '2025-07-27 09:38:04'),
(20, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 09:38:04', '2025-07-27 09:38:04'),
(21, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:38:40', '2025-07-27 09:38:40'),
(22, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 09:38:40', '2025-07-27 09:38:40'),
(23, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:43:41', '2025-07-27 09:43:41'),
(24, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 09:43:41', '2025-07-27 09:43:41'),
(25, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:45:58', '2025-07-27 09:45:58'),
(26, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 405 - ', '2025-07-27 09:45:58', '2025-07-27 09:45:58'),
(27, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:48:41', '2025-07-27 09:48:41'),
(28, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 405 - ', '2025-07-27 09:48:42', '2025-07-27 09:48:42'),
(29, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:53:41', '2025-07-27 09:53:41'),
(30, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'Semaphore API key is required. Please configure it in the settings.', '2025-07-27 09:53:41', '2025-07-27 09:53:41'),
(31, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 09:59:07', '2025-07-27 09:59:07'),
(32, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 09:59:07', '2025-07-27 09:59:07'),
(33, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 10:07:28', '2025-07-27 10:07:28'),
(34, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 10:07:28', '2025-07-27 10:07:28'),
(35, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 10:08:41', '2025-07-27 10:08:41'),
(36, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 10:08:41', '2025-07-27 10:08:41'),
(37, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 10:13:41', '2025-07-27 10:13:41'),
(38, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 10:13:41', '2025-07-27 10:13:41'),
(39, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 10:18:41', '2025-07-27 10:18:41'),
(40, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 10:18:41', '2025-07-27 10:18:41'),
(41, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 10:28:25', '2025-07-27 10:28:25'),
(42, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 10:28:26', '2025-07-27 10:28:26'),
(43, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 10:28:39', '2025-07-27 10:28:39'),
(44, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 10:28:39', '2025-07-27 10:28:39'),
(45, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 10:28:44', '2025-07-27 10:28:44'),
(46, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 10:28:44', '2025-07-27 10:28:44'),
(47, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:16:54', '2025-07-27 14:16:54'),
(48, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:16:55', '2025-07-27 14:16:55'),
(49, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:18:42', '2025-07-27 14:18:42'),
(50, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:18:42', '2025-07-27 14:18:42'),
(51, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:21:54', '2025-07-27 14:21:54'),
(52, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:21:54', '2025-07-27 14:21:54'),
(53, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:23:41', '2025-07-27 14:23:41'),
(54, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:23:41', '2025-07-27 14:23:41'),
(55, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:26:56', '2025-07-27 14:26:56'),
(56, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:26:56', '2025-07-27 14:26:56'),
(57, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:28:42', '2025-07-27 14:28:42'),
(58, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:28:43', '2025-07-27 14:28:43'),
(59, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:31:55', '2025-07-27 14:31:55'),
(60, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:31:55', '2025-07-27 14:31:55'),
(61, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:33:44', '2025-07-27 14:33:44'),
(62, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:33:44', '2025-07-27 14:33:44'),
(63, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:36:55', '2025-07-27 14:36:55'),
(64, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:36:55', '2025-07-27 14:36:55'),
(65, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:38:43', '2025-07-27 14:38:43'),
(66, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:38:43', '2025-07-27 14:38:43'),
(67, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:41:54', '2025-07-27 14:41:54'),
(68, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:41:55', '2025-07-27 14:41:55'),
(69, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:43:43', '2025-07-27 14:43:43'),
(70, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:43:43', '2025-07-27 14:43:43'),
(71, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:46:54', '2025-07-27 14:46:54'),
(72, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:46:54', '2025-07-27 14:46:54'),
(73, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:48:43', '2025-07-27 14:48:43'),
(74, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:48:43', '2025-07-27 14:48:43'),
(75, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:51:54', '2025-07-27 14:51:54'),
(76, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:51:54', '2025-07-27 14:51:54'),
(77, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:53:44', '2025-07-27 14:53:44'),
(78, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:53:44', '2025-07-27 14:53:44'),
(79, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-07-27 14:56:57', '2025-07-27 14:56:57'),
(80, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 429 - error code: 1027', '2025-07-27 14:56:57', '2025-07-27 14:56:57'),
(81, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 03:53:43', '2025-08-06 03:53:43'),
(82, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 03:53:44', '2025-08-06 03:53:44'),
(83, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 03:54:19', '2025-08-06 03:54:19'),
(84, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 03:54:19', '2025-08-06 03:54:19'),
(85, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 03:58:43', '2025-08-06 03:58:43'),
(86, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 03:58:43', '2025-08-06 03:58:43'),
(87, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 03:59:17', '2025-08-06 03:59:17'),
(88, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 03:59:18', '2025-08-06 03:59:18'),
(89, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 04:03:43', '2025-08-06 04:03:43'),
(90, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - <!DOCTYPE html>\n<!--[if lt IE 7]> <html class=\"no-js ie6 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 7]>    <html class=\"no-js ie7 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 8]>    <html class=\"no-js ie8 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if gt IE 8]><!--> <html class=\"no-js\" lang=\"en-US\"> <!--<![endif]-->\n<head>\n<title>Worker threw exception | free-sms-api.svxtract.workers.dev | Cloudflare</title>\n<meta charset=\"UTF-8\" />\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\" />\n<meta name=\"robots\" content=\"noindex, nofollow\" />\n<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\" />\n<link rel=\"stylesheet\" id=\"cf_styles-css\" href=\"/cdn-cgi/styles/cf.errors.css\" />\n<!--[if lt IE 9]><link rel=\"stylesheet\" id=\'cf_styles-ie-css\' href=\"/cdn-cgi/styles/cf.errors.ie.css\" /><![endif]-->\n<style>body{margin:0;padding:0}</style>\n\n\n<!--[if gte IE 10]><!-->\n<script>\n  if (!navigator.cookieEnabled) {\n    window.addEventListener(\'DOMContentLoaded\', function () {\n      var cookieEl = document.getElementById(\'cookie-alert\');\n      cookieEl.style.display = \'block\';\n    })\n  }\n</script>\n<!--<![endif]-->\n\n\n</head>\n<body>\n  <div id=\"cf-wrapper\">\n    <div class=\"cf-alert cf-alert-error cf-cookie-error\" id=\"cookie-alert\" data-translate=\"enable_cookies\">Please enable cookies.</div>\n    <div id=\"cf-error-details\" class=\"cf-error-details-wrapper\">\n      <div class=\"cf-wrapper cf-header cf-error-overview\">\n        <h1>\n          <span class=\"cf-error-type\" data-translate=\"error\">Error</span>\n          <span class=\"cf-error-code\">1101</span>\n          <small class=\"heading-ray-id\">Ray ID: 96aba56c48c99cf4 &bull; 2025-08-06 04:03:44 UTC</small>\n        </h1>\n        <h2 class=\"cf-subheadline\" data-translate=\"error_desc\">Worker threw exception</h2>\n      </div><!-- /.header -->\n\n      <section></section><!-- spacer -->\n\n      <div class=\"cf-section cf-wrapper\">\n        <div class=\"cf-columns two\">\n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_happened\">What happened?</h2>\n            <p>You\'ve requested a page on a website (free-sms-api.svxtract.workers.dev) that is on the <a href=\"https://www.cloudflare.com/5xx-error-landing/\" target=\"_blank\">Cloudflare</a> network. An unknown error occurred while rendering the page.</p>\n          </div>\n\n          \n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_can_i_do\">What can I do?</h2>\n            <p><strong>If you are the owner of this website:</strong><br />refer to <a href=\"https://developers.cloudflare.com/workers/observability/errors/\" target=\"_blank\">Workers - Errors and Exceptions</a> and check Workers Logs for free-sms-api.svxtract.workers.dev.</p>\n          </div>\n          \n        </div>\n      </div><!-- /.section -->\n\n      <div class=\"cf-error-footer cf-wrapper w-240 lg:w-full py-10 sm:py-4 sm:px-8 mx-auto text-center sm:text-left border-solid border-0 border-t border-gray-300\">\n  <p class=\"text-13\">\n    <span class=\"cf-footer-item sm:block sm:mb-1\">Cloudflare Ray ID: <strong class=\"font-semibold\">96aba56c48c99cf4</strong></span>\n    <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    <span id=\"cf-footer-item-ip\" class=\"cf-footer-item hidden sm:block sm:mb-1\">\n      Your IP:\n      <button type=\"button\" id=\"cf-footer-ip-reveal\" class=\"cf-footer-ip-reveal-btn\">Click to reveal</button>\n      <span class=\"hidden\" id=\"cf-footer-ip\">110.54.143.46</span>\n      <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    </span>\n    <span class=\"cf-footer-item sm:block sm:mb-1\"><span>Performance &amp; security by</span> <a rel=\"noopener noreferrer\" href=\"https://www.cloudflare.com/5xx-error-landing\" id=\"brand_link\" target=\"_blank\">Cloudflare</a></span>\n    \n  </p>\n  <script>(function(){function d(){var b=a.getElementById(\"cf-footer-item-ip\"),c=a.getElementById(\"cf-footer-ip-reveal\");b&&\"classList\"in b&&(b.classList.remove(\"hidden\"),c.addEventListener(\"click\",function(){c.classList.add(\"hidden\");a.getElementById(\"cf-footer-ip\").classList.remove(\"hidden\")}))}var a=document;document.addEventListener&&a.addEventListener(\"DOMContentLoaded\",d)})();</script>\n</div><!-- /.error-footer -->\n\n\n    </div><!-- /#cf-error-details -->\n  </div><!-- /#cf-wrapper -->\n\n  <script>\n  window._cf_translation = {};\n  \n  \n</script>\n\n</body>\n</html>\n', '2025-08-06 04:03:43', '2025-08-06 04:03:43'),
(91, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 04:04:19', '2025-08-06 04:04:19'),
(92, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 04:04:19', '2025-08-06 04:04:19'),
(93, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 04:08:41', '2025-08-06 04:08:41'),
(94, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 04:08:41', '2025-08-06 04:08:41'),
(95, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 04:09:16', '2025-08-06 04:09:16'),
(96, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 04:09:16', '2025-08-06 04:09:16'),
(97, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 12:08:52', '2025-08-06 12:08:52'),
(98, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 12:08:52', '2025-08-06 12:08:52'),
(99, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 12:13:43', '2025-08-06 12:13:43'),
(100, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 12:13:44', '2025-08-06 12:13:44'),
(101, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 12:18:43', '2025-08-06 12:18:43'),
(102, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 12:18:44', '2025-08-06 12:18:44'),
(103, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 12:23:41', '2025-08-06 12:23:41'),
(104, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 12:23:42', '2025-08-06 12:23:42'),
(105, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 12:28:42', '2025-08-06 12:28:42'),
(106, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 12:28:43', '2025-08-06 12:28:43'),
(107, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 12:33:41', '2025-08-06 12:33:41'),
(108, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 12:33:42', '2025-08-06 12:33:42'),
(109, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 12:38:42', '2025-08-06 12:38:42'),
(110, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 12:38:43', '2025-08-06 12:38:43'),
(111, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 12:43:42', '2025-08-06 12:43:42'),
(112, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 12:43:42', '2025-08-06 12:43:42'),
(113, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 12:48:42', '2025-08-06 12:48:42'),
(114, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 12:48:43', '2025-08-06 12:48:43'),
(115, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 12:53:42', '2025-08-06 12:53:42'),
(116, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 12:53:42', '2025-08-06 12:53:42'),
(117, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 12:58:42', '2025-08-06 12:58:42'),
(118, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - <!DOCTYPE html>\n<!--[if lt IE 7]> <html class=\"no-js ie6 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 7]>    <html class=\"no-js ie7 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 8]>    <html class=\"no-js ie8 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if gt IE 8]><!--> <html class=\"no-js\" lang=\"en-US\"> <!--<![endif]-->\n<head>\n<title>Worker threw exception | free-sms-api.svxtract.workers.dev | Cloudflare</title>\n<meta charset=\"UTF-8\" />\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\" />\n<meta name=\"robots\" content=\"noindex, nofollow\" />\n<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\" />\n<link rel=\"stylesheet\" id=\"cf_styles-css\" href=\"/cdn-cgi/styles/cf.errors.css\" />\n<!--[if lt IE 9]><link rel=\"stylesheet\" id=\'cf_styles-ie-css\' href=\"/cdn-cgi/styles/cf.errors.ie.css\" /><![endif]-->\n<style>body{margin:0;padding:0}</style>\n\n\n<!--[if gte IE 10]><!-->\n<script>\n  if (!navigator.cookieEnabled) {\n    window.addEventListener(\'DOMContentLoaded\', function () {\n      var cookieEl = document.getElementById(\'cookie-alert\');\n      cookieEl.style.display = \'block\';\n    })\n  }\n</script>\n<!--<![endif]-->\n\n\n</head>\n<body>\n  <div id=\"cf-wrapper\">\n    <div class=\"cf-alert cf-alert-error cf-cookie-error\" id=\"cookie-alert\" data-translate=\"enable_cookies\">Please enable cookies.</div>\n    <div id=\"cf-error-details\" class=\"cf-error-details-wrapper\">\n      <div class=\"cf-wrapper cf-header cf-error-overview\">\n        <h1>\n          <span class=\"cf-error-type\" data-translate=\"error\">Error</span>\n          <span class=\"cf-error-code\">1101</span>\n          <small class=\"heading-ray-id\">Ray ID: 96aeb50ef880f91c &bull; 2025-08-06 12:58:42 UTC</small>\n        </h1>\n        <h2 class=\"cf-subheadline\" data-translate=\"error_desc\">Worker threw exception</h2>\n      </div><!-- /.header -->\n\n      <section></section><!-- spacer -->\n\n      <div class=\"cf-section cf-wrapper\">\n        <div class=\"cf-columns two\">\n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_happened\">What happened?</h2>\n            <p>You\'ve requested a page on a website (free-sms-api.svxtract.workers.dev) that is on the <a href=\"https://www.cloudflare.com/5xx-error-landing/\" target=\"_blank\">Cloudflare</a> network. An unknown error occurred while rendering the page.</p>\n          </div>\n\n          \n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_can_i_do\">What can I do?</h2>\n            <p><strong>If you are the owner of this website:</strong><br />refer to <a href=\"https://developers.cloudflare.com/workers/observability/errors/\" target=\"_blank\">Workers - Errors and Exceptions</a> and check Workers Logs for free-sms-api.svxtract.workers.dev.</p>\n          </div>\n          \n        </div>\n      </div><!-- /.section -->\n\n      <div class=\"cf-error-footer cf-wrapper w-240 lg:w-full py-10 sm:py-4 sm:px-8 mx-auto text-center sm:text-left border-solid border-0 border-t border-gray-300\">\n  <p class=\"text-13\">\n    <span class=\"cf-footer-item sm:block sm:mb-1\">Cloudflare Ray ID: <strong class=\"font-semibold\">96aeb50ef880f91c</strong></span>\n    <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    <span id=\"cf-footer-item-ip\" class=\"cf-footer-item hidden sm:block sm:mb-1\">\n      Your IP:\n      <button type=\"button\" id=\"cf-footer-ip-reveal\" class=\"cf-footer-ip-reveal-btn\">Click to reveal</button>\n      <span class=\"hidden\" id=\"cf-footer-ip\">110.54.143.46</span>\n      <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    </span>\n    <span class=\"cf-footer-item sm:block sm:mb-1\"><span>Performance &amp; security by</span> <a rel=\"noopener noreferrer\" href=\"https://www.cloudflare.com/5xx-error-landing\" id=\"brand_link\" target=\"_blank\">Cloudflare</a></span>\n    \n  </p>\n  <script>(function(){function d(){var b=a.getElementById(\"cf-footer-item-ip\"),c=a.getElementById(\"cf-footer-ip-reveal\");b&&\"classList\"in b&&(b.classList.remove(\"hidden\"),c.addEventListener(\"click\",function(){c.classList.add(\"hidden\");a.getElementById(\"cf-footer-ip\").classList.remove(\"hidden\")}))}var a=document;document.addEventListener&&a.addEventListener(\"DOMContentLoaded\",d)})();</script>\n</div><!-- /.error-footer -->\n\n\n    </div><!-- /#cf-error-details -->\n  </div><!-- /#cf-wrapper -->\n\n  <script>\n  window._cf_translation = {};\n  \n  \n</script>\n\n</body>\n</html>\n', '2025-08-06 12:58:42', '2025-08-06 12:58:42'),
(119, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 13:03:41', '2025-08-06 13:03:41'),
(120, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 13:03:41', '2025-08-06 13:03:41'),
(121, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 13:08:43', '2025-08-06 13:08:43'),
(122, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 13:08:43', '2025-08-06 13:08:43'),
(123, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 13:13:45', '2025-08-06 13:13:45'),
(124, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 13:13:45', '2025-08-06 13:13:45'),
(125, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 13:18:44', '2025-08-06 13:18:44'),
(126, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 13:18:44', '2025-08-06 13:18:44'),
(127, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 13:23:42', '2025-08-06 13:23:42'),
(128, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 13:23:42', '2025-08-06 13:23:42'),
(129, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 13:28:41', '2025-08-06 13:28:41'),
(130, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 13:28:41', '2025-08-06 13:28:41'),
(131, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 13:33:43', '2025-08-06 13:33:43');
INSERT INTO `reminder_logs` (`id`, `type`, `recipient`, `subject`, `message`, `status`, `error_message`, `sent_at`, `created_at`) VALUES
(132, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - <!DOCTYPE html>\n<!--[if lt IE 7]> <html class=\"no-js ie6 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 7]>    <html class=\"no-js ie7 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 8]>    <html class=\"no-js ie8 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if gt IE 8]><!--> <html class=\"no-js\" lang=\"en-US\"> <!--<![endif]-->\n<head>\n<title>Worker threw exception | free-sms-api.svxtract.workers.dev | Cloudflare</title>\n<meta charset=\"UTF-8\" />\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\" />\n<meta name=\"robots\" content=\"noindex, nofollow\" />\n<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\" />\n<link rel=\"stylesheet\" id=\"cf_styles-css\" href=\"/cdn-cgi/styles/cf.errors.css\" />\n<!--[if lt IE 9]><link rel=\"stylesheet\" id=\'cf_styles-ie-css\' href=\"/cdn-cgi/styles/cf.errors.ie.css\" /><![endif]-->\n<style>body{margin:0;padding:0}</style>\n\n\n<!--[if gte IE 10]><!-->\n<script>\n  if (!navigator.cookieEnabled) {\n    window.addEventListener(\'DOMContentLoaded\', function () {\n      var cookieEl = document.getElementById(\'cookie-alert\');\n      cookieEl.style.display = \'block\';\n    })\n  }\n</script>\n<!--<![endif]-->\n\n\n</head>\n<body>\n  <div id=\"cf-wrapper\">\n    <div class=\"cf-alert cf-alert-error cf-cookie-error\" id=\"cookie-alert\" data-translate=\"enable_cookies\">Please enable cookies.</div>\n    <div id=\"cf-error-details\" class=\"cf-error-details-wrapper\">\n      <div class=\"cf-wrapper cf-header cf-error-overview\">\n        <h1>\n          <span class=\"cf-error-type\" data-translate=\"error\">Error</span>\n          <span class=\"cf-error-code\">1101</span>\n          <small class=\"heading-ray-id\">Ray ID: 96aee85ccaa86061 &bull; 2025-08-06 13:33:43 UTC</small>\n        </h1>\n        <h2 class=\"cf-subheadline\" data-translate=\"error_desc\">Worker threw exception</h2>\n      </div><!-- /.header -->\n\n      <section></section><!-- spacer -->\n\n      <div class=\"cf-section cf-wrapper\">\n        <div class=\"cf-columns two\">\n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_happened\">What happened?</h2>\n            <p>You\'ve requested a page on a website (free-sms-api.svxtract.workers.dev) that is on the <a href=\"https://www.cloudflare.com/5xx-error-landing/\" target=\"_blank\">Cloudflare</a> network. An unknown error occurred while rendering the page.</p>\n          </div>\n\n          \n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_can_i_do\">What can I do?</h2>\n            <p><strong>If you are the owner of this website:</strong><br />refer to <a href=\"https://developers.cloudflare.com/workers/observability/errors/\" target=\"_blank\">Workers - Errors and Exceptions</a> and check Workers Logs for free-sms-api.svxtract.workers.dev.</p>\n          </div>\n          \n        </div>\n      </div><!-- /.section -->\n\n      <div class=\"cf-error-footer cf-wrapper w-240 lg:w-full py-10 sm:py-4 sm:px-8 mx-auto text-center sm:text-left border-solid border-0 border-t border-gray-300\">\n  <p class=\"text-13\">\n    <span class=\"cf-footer-item sm:block sm:mb-1\">Cloudflare Ray ID: <strong class=\"font-semibold\">96aee85ccaa86061</strong></span>\n    <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    <span id=\"cf-footer-item-ip\" class=\"cf-footer-item hidden sm:block sm:mb-1\">\n      Your IP:\n      <button type=\"button\" id=\"cf-footer-ip-reveal\" class=\"cf-footer-ip-reveal-btn\">Click to reveal</button>\n      <span class=\"hidden\" id=\"cf-footer-ip\">110.54.143.46</span>\n      <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    </span>\n    <span class=\"cf-footer-item sm:block sm:mb-1\"><span>Performance &amp; security by</span> <a rel=\"noopener noreferrer\" href=\"https://www.cloudflare.com/5xx-error-landing\" id=\"brand_link\" target=\"_blank\">Cloudflare</a></span>\n    \n  </p>\n  <script>(function(){function d(){var b=a.getElementById(\"cf-footer-item-ip\"),c=a.getElementById(\"cf-footer-ip-reveal\");b&&\"classList\"in b&&(b.classList.remove(\"hidden\"),c.addEventListener(\"click\",function(){c.classList.add(\"hidden\");a.getElementById(\"cf-footer-ip\").classList.remove(\"hidden\")}))}var a=document;document.addEventListener&&a.addEventListener(\"DOMContentLoaded\",d)})();</script>\n</div><!-- /.error-footer -->\n\n\n    </div><!-- /#cf-error-details -->\n  </div><!-- /#cf-wrapper -->\n\n  <script>\n  window._cf_translation = {};\n  \n  \n</script>\n\n</body>\n</html>\n', '2025-08-06 13:33:44', '2025-08-06 13:33:44'),
(133, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 13:38:43', '2025-08-06 13:38:43'),
(134, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 13:38:43', '2025-08-06 13:38:43'),
(135, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 13:43:43', '2025-08-06 13:43:43'),
(136, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 13:43:44', '2025-08-06 13:43:44'),
(137, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 13:48:44', '2025-08-06 13:48:44'),
(138, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 13:48:44', '2025-08-06 13:48:44'),
(139, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 13:53:43', '2025-08-06 13:53:43'),
(140, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 13:53:43', '2025-08-06 13:53:43'),
(141, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 13:58:45', '2025-08-06 13:58:45'),
(142, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 13:58:45', '2025-08-06 13:58:45'),
(143, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-06 14:03:42', '2025-08-06 14:03:42'),
(144, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-06 14:03:43', '2025-08-06 14:03:43'),
(145, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 12:47:08', '2025-08-09 12:47:08'),
(146, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 12:47:09', '2025-08-09 12:47:09'),
(147, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 12:48:41', '2025-08-09 12:48:41'),
(148, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 12:48:42', '2025-08-09 12:48:42'),
(149, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 12:52:03', '2025-08-09 12:52:03'),
(150, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 12:52:03', '2025-08-09 12:52:03'),
(151, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 12:53:41', '2025-08-09 12:53:41'),
(152, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - <!DOCTYPE html>\n<!--[if lt IE 7]> <html class=\"no-js ie6 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 7]>    <html class=\"no-js ie7 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 8]>    <html class=\"no-js ie8 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if gt IE 8]><!--> <html class=\"no-js\" lang=\"en-US\"> <!--<![endif]-->\n<head>\n<title>Worker threw exception | free-sms-api.svxtract.workers.dev | Cloudflare</title>\n<meta charset=\"UTF-8\" />\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\" />\n<meta name=\"robots\" content=\"noindex, nofollow\" />\n<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\" />\n<link rel=\"stylesheet\" id=\"cf_styles-css\" href=\"/cdn-cgi/styles/cf.errors.css\" />\n<!--[if lt IE 9]><link rel=\"stylesheet\" id=\'cf_styles-ie-css\' href=\"/cdn-cgi/styles/cf.errors.ie.css\" /><![endif]-->\n<style>body{margin:0;padding:0}</style>\n\n\n<!--[if gte IE 10]><!-->\n<script>\n  if (!navigator.cookieEnabled) {\n    window.addEventListener(\'DOMContentLoaded\', function () {\n      var cookieEl = document.getElementById(\'cookie-alert\');\n      cookieEl.style.display = \'block\';\n    })\n  }\n</script>\n<!--<![endif]-->\n\n\n</head>\n<body>\n  <div id=\"cf-wrapper\">\n    <div class=\"cf-alert cf-alert-error cf-cookie-error\" id=\"cookie-alert\" data-translate=\"enable_cookies\">Please enable cookies.</div>\n    <div id=\"cf-error-details\" class=\"cf-error-details-wrapper\">\n      <div class=\"cf-wrapper cf-header cf-error-overview\">\n        <h1>\n          <span class=\"cf-error-type\" data-translate=\"error\">Error</span>\n          <span class=\"cf-error-code\">1101</span>\n          <small class=\"heading-ray-id\">Ray ID: 96c765dccca9dfc6 &bull; 2025-08-09 12:53:42 UTC</small>\n        </h1>\n        <h2 class=\"cf-subheadline\" data-translate=\"error_desc\">Worker threw exception</h2>\n      </div><!-- /.header -->\n\n      <section></section><!-- spacer -->\n\n      <div class=\"cf-section cf-wrapper\">\n        <div class=\"cf-columns two\">\n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_happened\">What happened?</h2>\n            <p>You\'ve requested a page on a website (free-sms-api.svxtract.workers.dev) that is on the <a href=\"https://www.cloudflare.com/5xx-error-landing/\" target=\"_blank\">Cloudflare</a> network. An unknown error occurred while rendering the page.</p>\n          </div>\n\n          \n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_can_i_do\">What can I do?</h2>\n            <p><strong>If you are the owner of this website:</strong><br />refer to <a href=\"https://developers.cloudflare.com/workers/observability/errors/\" target=\"_blank\">Workers - Errors and Exceptions</a> and check Workers Logs for free-sms-api.svxtract.workers.dev.</p>\n          </div>\n          \n        </div>\n      </div><!-- /.section -->\n\n      <div class=\"cf-error-footer cf-wrapper w-240 lg:w-full py-10 sm:py-4 sm:px-8 mx-auto text-center sm:text-left border-solid border-0 border-t border-gray-300\">\n  <p class=\"text-13\">\n    <span class=\"cf-footer-item sm:block sm:mb-1\">Cloudflare Ray ID: <strong class=\"font-semibold\">96c765dccca9dfc6</strong></span>\n    <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    <span id=\"cf-footer-item-ip\" class=\"cf-footer-item hidden sm:block sm:mb-1\">\n      Your IP:\n      <button type=\"button\" id=\"cf-footer-ip-reveal\" class=\"cf-footer-ip-reveal-btn\">Click to reveal</button>\n      <span class=\"hidden\" id=\"cf-footer-ip\">209.35.171.180</span>\n      <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    </span>\n    <span class=\"cf-footer-item sm:block sm:mb-1\"><span>Performance &amp; security by</span> <a rel=\"noopener noreferrer\" href=\"https://www.cloudflare.com/5xx-error-landing\" id=\"brand_link\" target=\"_blank\">Cloudflare</a></span>\n    \n  </p>\n  <script>(function(){function d(){var b=a.getElementById(\"cf-footer-item-ip\"),c=a.getElementById(\"cf-footer-ip-reveal\");b&&\"classList\"in b&&(b.classList.remove(\"hidden\"),c.addEventListener(\"click\",function(){c.classList.add(\"hidden\");a.getElementById(\"cf-footer-ip\").classList.remove(\"hidden\")}))}var a=document;document.addEventListener&&a.addEventListener(\"DOMContentLoaded\",d)})();</script>\n</div><!-- /.error-footer -->\n\n\n    </div><!-- /#cf-error-details -->\n  </div><!-- /#cf-wrapper -->\n\n  <script>\n  window._cf_translation = {};\n  \n  \n</script>\n\n</body>\n</html>\n', '2025-08-09 12:53:42', '2025-08-09 12:53:42'),
(153, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 12:57:02', '2025-08-09 12:57:02'),
(154, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 12:57:02', '2025-08-09 12:57:02'),
(155, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 12:58:41', '2025-08-09 12:58:41'),
(156, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 12:58:41', '2025-08-09 12:58:41'),
(157, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:02:03', '2025-08-09 13:02:03'),
(158, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:02:03', '2025-08-09 13:02:03'),
(159, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:03:41', '2025-08-09 13:03:41'),
(160, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:03:41', '2025-08-09 13:03:41'),
(161, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:07:03', '2025-08-09 13:07:03'),
(162, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:07:04', '2025-08-09 13:07:04'),
(163, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:08:41', '2025-08-09 13:08:41'),
(164, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:08:41', '2025-08-09 13:08:41'),
(165, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:12:03', '2025-08-09 13:12:03'),
(166, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:12:04', '2025-08-09 13:12:04'),
(167, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:13:41', '2025-08-09 13:13:41'),
(168, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:13:41', '2025-08-09 13:13:41'),
(169, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:17:02', '2025-08-09 13:17:02'),
(170, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:17:03', '2025-08-09 13:17:03'),
(171, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:18:42', '2025-08-09 13:18:42'),
(172, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - <!DOCTYPE html>\n<!--[if lt IE 7]> <html class=\"no-js ie6 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 7]>    <html class=\"no-js ie7 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 8]>    <html class=\"no-js ie8 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if gt IE 8]><!--> <html class=\"no-js\" lang=\"en-US\"> <!--<![endif]-->\n<head>\n<title>Worker threw exception | free-sms-api.svxtract.workers.dev | Cloudflare</title>\n<meta charset=\"UTF-8\" />\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\" />\n<meta name=\"robots\" content=\"noindex, nofollow\" />\n<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\" />\n<link rel=\"stylesheet\" id=\"cf_styles-css\" href=\"/cdn-cgi/styles/cf.errors.css\" />\n<!--[if lt IE 9]><link rel=\"stylesheet\" id=\'cf_styles-ie-css\' href=\"/cdn-cgi/styles/cf.errors.ie.css\" /><![endif]-->\n<style>body{margin:0;padding:0}</style>\n\n\n<!--[if gte IE 10]><!-->\n<script>\n  if (!navigator.cookieEnabled) {\n    window.addEventListener(\'DOMContentLoaded\', function () {\n      var cookieEl = document.getElementById(\'cookie-alert\');\n      cookieEl.style.display = \'block\';\n    })\n  }\n</script>\n<!--<![endif]-->\n\n\n</head>\n<body>\n  <div id=\"cf-wrapper\">\n    <div class=\"cf-alert cf-alert-error cf-cookie-error\" id=\"cookie-alert\" data-translate=\"enable_cookies\">Please enable cookies.</div>\n    <div id=\"cf-error-details\" class=\"cf-error-details-wrapper\">\n      <div class=\"cf-wrapper cf-header cf-error-overview\">\n        <h1>\n          <span class=\"cf-error-type\" data-translate=\"error\">Error</span>\n          <span class=\"cf-error-code\">1101</span>\n          <small class=\"heading-ray-id\">Ray ID: 96c78a7d5af9a8ea &bull; 2025-08-09 13:18:42 UTC</small>\n        </h1>\n        <h2 class=\"cf-subheadline\" data-translate=\"error_desc\">Worker threw exception</h2>\n      </div><!-- /.header -->\n\n      <section></section><!-- spacer -->\n\n      <div class=\"cf-section cf-wrapper\">\n        <div class=\"cf-columns two\">\n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_happened\">What happened?</h2>\n            <p>You\'ve requested a page on a website (free-sms-api.svxtract.workers.dev) that is on the <a href=\"https://www.cloudflare.com/5xx-error-landing/\" target=\"_blank\">Cloudflare</a> network. An unknown error occurred while rendering the page.</p>\n          </div>\n\n          \n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_can_i_do\">What can I do?</h2>\n            <p><strong>If you are the owner of this website:</strong><br />refer to <a href=\"https://developers.cloudflare.com/workers/observability/errors/\" target=\"_blank\">Workers - Errors and Exceptions</a> and check Workers Logs for free-sms-api.svxtract.workers.dev.</p>\n          </div>\n          \n        </div>\n      </div><!-- /.section -->\n\n      <div class=\"cf-error-footer cf-wrapper w-240 lg:w-full py-10 sm:py-4 sm:px-8 mx-auto text-center sm:text-left border-solid border-0 border-t border-gray-300\">\n  <p class=\"text-13\">\n    <span class=\"cf-footer-item sm:block sm:mb-1\">Cloudflare Ray ID: <strong class=\"font-semibold\">96c78a7d5af9a8ea</strong></span>\n    <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    <span id=\"cf-footer-item-ip\" class=\"cf-footer-item hidden sm:block sm:mb-1\">\n      Your IP:\n      <button type=\"button\" id=\"cf-footer-ip-reveal\" class=\"cf-footer-ip-reveal-btn\">Click to reveal</button>\n      <span class=\"hidden\" id=\"cf-footer-ip\">209.35.171.180</span>\n      <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    </span>\n    <span class=\"cf-footer-item sm:block sm:mb-1\"><span>Performance &amp; security by</span> <a rel=\"noopener noreferrer\" href=\"https://www.cloudflare.com/5xx-error-landing\" id=\"brand_link\" target=\"_blank\">Cloudflare</a></span>\n    \n  </p>\n  <script>(function(){function d(){var b=a.getElementById(\"cf-footer-item-ip\"),c=a.getElementById(\"cf-footer-ip-reveal\");b&&\"classList\"in b&&(b.classList.remove(\"hidden\"),c.addEventListener(\"click\",function(){c.classList.add(\"hidden\");a.getElementById(\"cf-footer-ip\").classList.remove(\"hidden\")}))}var a=document;document.addEventListener&&a.addEventListener(\"DOMContentLoaded\",d)})();</script>\n</div><!-- /.error-footer -->\n\n\n    </div><!-- /#cf-error-details -->\n  </div><!-- /#cf-wrapper -->\n\n  <script>\n  window._cf_translation = {};\n  \n  \n</script>\n\n</body>\n</html>\n', '2025-08-09 13:18:42', '2025-08-09 13:18:42'),
(173, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:22:02', '2025-08-09 13:22:02'),
(174, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:22:02', '2025-08-09 13:22:02'),
(175, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:23:40', '2025-08-09 13:23:40'),
(176, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:23:40', '2025-08-09 13:23:40'),
(177, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:27:02', '2025-08-09 13:27:02'),
(178, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:27:02', '2025-08-09 13:27:02'),
(179, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:28:40', '2025-08-09 13:28:40'),
(180, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:28:41', '2025-08-09 13:28:41'),
(181, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:32:01', '2025-08-09 13:32:01'),
(182, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:32:02', '2025-08-09 13:32:02'),
(183, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:33:40', '2025-08-09 13:33:40'),
(184, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:33:41', '2025-08-09 13:33:41'),
(185, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:37:02', '2025-08-09 13:37:02'),
(186, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:37:03', '2025-08-09 13:37:03'),
(187, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:38:40', '2025-08-09 13:38:40'),
(188, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:38:41', '2025-08-09 13:38:41'),
(189, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:42:02', '2025-08-09 13:42:02'),
(190, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:42:02', '2025-08-09 13:42:02'),
(191, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:43:40', '2025-08-09 13:43:40'),
(192, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:43:41', '2025-08-09 13:43:41'),
(193, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:47:02', '2025-08-09 13:47:02'),
(194, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:47:03', '2025-08-09 13:47:03'),
(195, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:48:40', '2025-08-09 13:48:40'),
(196, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:48:40', '2025-08-09 13:48:40'),
(197, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:52:03', '2025-08-09 13:52:03'),
(198, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:52:04', '2025-08-09 13:52:04'),
(199, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:53:40', '2025-08-09 13:53:40'),
(200, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:53:40', '2025-08-09 13:53:40'),
(201, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:57:02', '2025-08-09 13:57:02'),
(202, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 13:57:02', '2025-08-09 13:57:02'),
(203, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 13:58:41', '2025-08-09 13:58:41'),
(204, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - <!DOCTYPE html>\n<!--[if lt IE 7]> <html class=\"no-js ie6 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 7]>    <html class=\"no-js ie7 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 8]>    <html class=\"no-js ie8 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if gt IE 8]><!--> <html class=\"no-js\" lang=\"en-US\"> <!--<![endif]-->\n<head>\n<title>Worker threw exception | free-sms-api.svxtract.workers.dev | Cloudflare</title>\n<meta charset=\"UTF-8\" />\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\" />\n<meta name=\"robots\" content=\"noindex, nofollow\" />\n<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\" />\n<link rel=\"stylesheet\" id=\"cf_styles-css\" href=\"/cdn-cgi/styles/cf.errors.css\" />\n<!--[if lt IE 9]><link rel=\"stylesheet\" id=\'cf_styles-ie-css\' href=\"/cdn-cgi/styles/cf.errors.ie.css\" /><![endif]-->\n<style>body{margin:0;padding:0}</style>\n\n\n<!--[if gte IE 10]><!-->\n<script>\n  if (!navigator.cookieEnabled) {\n    window.addEventListener(\'DOMContentLoaded\', function () {\n      var cookieEl = document.getElementById(\'cookie-alert\');\n      cookieEl.style.display = \'block\';\n    })\n  }\n</script>\n<!--<![endif]-->\n\n\n</head>\n<body>\n  <div id=\"cf-wrapper\">\n    <div class=\"cf-alert cf-alert-error cf-cookie-error\" id=\"cookie-alert\" data-translate=\"enable_cookies\">Please enable cookies.</div>\n    <div id=\"cf-error-details\" class=\"cf-error-details-wrapper\">\n      <div class=\"cf-wrapper cf-header cf-error-overview\">\n        <h1>\n          <span class=\"cf-error-type\" data-translate=\"error\">Error</span>\n          <span class=\"cf-error-code\">1101</span>\n          <small class=\"heading-ray-id\">Ray ID: 96c7c50ed9ff3da7 &bull; 2025-08-09 13:58:41 UTC</small>\n        </h1>\n        <h2 class=\"cf-subheadline\" data-translate=\"error_desc\">Worker threw exception</h2>\n      </div><!-- /.header -->\n\n      <section></section><!-- spacer -->\n\n      <div class=\"cf-section cf-wrapper\">\n        <div class=\"cf-columns two\">\n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_happened\">What happened?</h2>\n            <p>You\'ve requested a page on a website (free-sms-api.svxtract.workers.dev) that is on the <a href=\"https://www.cloudflare.com/5xx-error-landing/\" target=\"_blank\">Cloudflare</a> network. An unknown error occurred while rendering the page.</p>\n          </div>\n\n          \n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_can_i_do\">What can I do?</h2>\n            <p><strong>If you are the owner of this website:</strong><br />refer to <a href=\"https://developers.cloudflare.com/workers/observability/errors/\" target=\"_blank\">Workers - Errors and Exceptions</a> and check Workers Logs for free-sms-api.svxtract.workers.dev.</p>\n          </div>\n          \n        </div>\n      </div><!-- /.section -->\n\n      <div class=\"cf-error-footer cf-wrapper w-240 lg:w-full py-10 sm:py-4 sm:px-8 mx-auto text-center sm:text-left border-solid border-0 border-t border-gray-300\">\n  <p class=\"text-13\">\n    <span class=\"cf-footer-item sm:block sm:mb-1\">Cloudflare Ray ID: <strong class=\"font-semibold\">96c7c50ed9ff3da7</strong></span>\n    <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    <span id=\"cf-footer-item-ip\" class=\"cf-footer-item hidden sm:block sm:mb-1\">\n      Your IP:\n      <button type=\"button\" id=\"cf-footer-ip-reveal\" class=\"cf-footer-ip-reveal-btn\">Click to reveal</button>\n      <span class=\"hidden\" id=\"cf-footer-ip\">209.35.171.180</span>\n      <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    </span>\n    <span class=\"cf-footer-item sm:block sm:mb-1\"><span>Performance &amp; security by</span> <a rel=\"noopener noreferrer\" href=\"https://www.cloudflare.com/5xx-error-landing\" id=\"brand_link\" target=\"_blank\">Cloudflare</a></span>\n    \n  </p>\n  <script>(function(){function d(){var b=a.getElementById(\"cf-footer-item-ip\"),c=a.getElementById(\"cf-footer-ip-reveal\");b&&\"classList\"in b&&(b.classList.remove(\"hidden\"),c.addEventListener(\"click\",function(){c.classList.add(\"hidden\");a.getElementById(\"cf-footer-ip\").classList.remove(\"hidden\")}))}var a=document;document.addEventListener&&a.addEventListener(\"DOMContentLoaded\",d)})();</script>\n</div><!-- /.error-footer -->\n\n\n    </div><!-- /#cf-error-details -->\n  </div><!-- /#cf-wrapper -->\n\n  <script>\n  window._cf_translation = {};\n  \n  \n</script>\n\n</body>\n</html>\n', '2025-08-09 13:58:41', '2025-08-09 13:58:41'),
(205, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:02:02', '2025-08-09 14:02:02'),
(206, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:02:03', '2025-08-09 14:02:03'),
(207, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:03:40', '2025-08-09 14:03:40'),
(208, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:03:41', '2025-08-09 14:03:41'),
(209, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:07:02', '2025-08-09 14:07:02'),
(210, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:07:02', '2025-08-09 14:07:02'),
(211, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:08:40', '2025-08-09 14:08:40'),
(212, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:08:41', '2025-08-09 14:08:41'),
(213, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:12:02', '2025-08-09 14:12:02'),
(214, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:12:03', '2025-08-09 14:12:03'),
(215, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:13:40', '2025-08-09 14:13:40'),
(216, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:13:41', '2025-08-09 14:13:41'),
(217, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:17:03', '2025-08-09 14:17:03'),
(218, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - <!DOCTYPE html>\n<!--[if lt IE 7]> <html class=\"no-js ie6 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 7]>    <html class=\"no-js ie7 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 8]>    <html class=\"no-js ie8 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if gt IE 8]><!--> <html class=\"no-js\" lang=\"en-US\"> <!--<![endif]-->\n<head>\n<title>Worker threw exception | free-sms-api.svxtract.workers.dev | Cloudflare</title>\n<meta charset=\"UTF-8\" />\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\" />\n<meta name=\"robots\" content=\"noindex, nofollow\" />\n<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\" />\n<link rel=\"stylesheet\" id=\"cf_styles-css\" href=\"/cdn-cgi/styles/cf.errors.css\" />\n<!--[if lt IE 9]><link rel=\"stylesheet\" id=\'cf_styles-ie-css\' href=\"/cdn-cgi/styles/cf.errors.ie.css\" /><![endif]-->\n<style>body{margin:0;padding:0}</style>\n\n\n<!--[if gte IE 10]><!-->\n<script>\n  if (!navigator.cookieEnabled) {\n    window.addEventListener(\'DOMContentLoaded\', function () {\n      var cookieEl = document.getElementById(\'cookie-alert\');\n      cookieEl.style.display = \'block\';\n    })\n  }\n</script>\n<!--<![endif]-->\n\n\n</head>\n<body>\n  <div id=\"cf-wrapper\">\n    <div class=\"cf-alert cf-alert-error cf-cookie-error\" id=\"cookie-alert\" data-translate=\"enable_cookies\">Please enable cookies.</div>\n    <div id=\"cf-error-details\" class=\"cf-error-details-wrapper\">\n      <div class=\"cf-wrapper cf-header cf-error-overview\">\n        <h1>\n          <span class=\"cf-error-type\" data-translate=\"error\">Error</span>\n          <span class=\"cf-error-code\">1101</span>\n          <small class=\"heading-ray-id\">Ray ID: 96c7dff70f2ff8de &bull; 2025-08-09 14:17:03 UTC</small>\n        </h1>\n        <h2 class=\"cf-subheadline\" data-translate=\"error_desc\">Worker threw exception</h2>\n      </div><!-- /.header -->\n\n      <section></section><!-- spacer -->\n\n      <div class=\"cf-section cf-wrapper\">\n        <div class=\"cf-columns two\">\n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_happened\">What happened?</h2>\n            <p>You\'ve requested a page on a website (free-sms-api.svxtract.workers.dev) that is on the <a href=\"https://www.cloudflare.com/5xx-error-landing/\" target=\"_blank\">Cloudflare</a> network. An unknown error occurred while rendering the page.</p>\n          </div>\n\n          \n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_can_i_do\">What can I do?</h2>\n            <p><strong>If you are the owner of this website:</strong><br />refer to <a href=\"https://developers.cloudflare.com/workers/observability/errors/\" target=\"_blank\">Workers - Errors and Exceptions</a> and check Workers Logs for free-sms-api.svxtract.workers.dev.</p>\n          </div>\n          \n        </div>\n      </div><!-- /.section -->\n\n      <div class=\"cf-error-footer cf-wrapper w-240 lg:w-full py-10 sm:py-4 sm:px-8 mx-auto text-center sm:text-left border-solid border-0 border-t border-gray-300\">\n  <p class=\"text-13\">\n    <span class=\"cf-footer-item sm:block sm:mb-1\">Cloudflare Ray ID: <strong class=\"font-semibold\">96c7dff70f2ff8de</strong></span>\n    <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    <span id=\"cf-footer-item-ip\" class=\"cf-footer-item hidden sm:block sm:mb-1\">\n      Your IP:\n      <button type=\"button\" id=\"cf-footer-ip-reveal\" class=\"cf-footer-ip-reveal-btn\">Click to reveal</button>\n      <span class=\"hidden\" id=\"cf-footer-ip\">209.35.171.180</span>\n      <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    </span>\n    <span class=\"cf-footer-item sm:block sm:mb-1\"><span>Performance &amp; security by</span> <a rel=\"noopener noreferrer\" href=\"https://www.cloudflare.com/5xx-error-landing\" id=\"brand_link\" target=\"_blank\">Cloudflare</a></span>\n    \n  </p>\n  <script>(function(){function d(){var b=a.getElementById(\"cf-footer-item-ip\"),c=a.getElementById(\"cf-footer-ip-reveal\");b&&\"classList\"in b&&(b.classList.remove(\"hidden\"),c.addEventListener(\"click\",function(){c.classList.add(\"hidden\");a.getElementById(\"cf-footer-ip\").classList.remove(\"hidden\")}))}var a=document;document.addEventListener&&a.addEventListener(\"DOMContentLoaded\",d)})();</script>\n</div><!-- /.error-footer -->\n\n\n    </div><!-- /#cf-error-details -->\n  </div><!-- /#cf-wrapper -->\n\n  <script>\n  window._cf_translation = {};\n  \n  \n</script>\n\n</body>\n</html>\n', '2025-08-09 14:17:03', '2025-08-09 14:17:03'),
(219, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:18:40', '2025-08-09 14:18:40'),
(220, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:18:40', '2025-08-09 14:18:40'),
(221, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:22:02', '2025-08-09 14:22:02');
INSERT INTO `reminder_logs` (`id`, `type`, `recipient`, `subject`, `message`, `status`, `error_message`, `sent_at`, `created_at`) VALUES
(222, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:22:03', '2025-08-09 14:22:03'),
(223, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:23:40', '2025-08-09 14:23:40'),
(224, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - <!DOCTYPE html>\n<!--[if lt IE 7]> <html class=\"no-js ie6 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 7]>    <html class=\"no-js ie7 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 8]>    <html class=\"no-js ie8 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if gt IE 8]><!--> <html class=\"no-js\" lang=\"en-US\"> <!--<![endif]-->\n<head>\n<title>Worker threw exception | free-sms-api.svxtract.workers.dev | Cloudflare</title>\n<meta charset=\"UTF-8\" />\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\" />\n<meta name=\"robots\" content=\"noindex, nofollow\" />\n<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\" />\n<link rel=\"stylesheet\" id=\"cf_styles-css\" href=\"/cdn-cgi/styles/cf.errors.css\" />\n<!--[if lt IE 9]><link rel=\"stylesheet\" id=\'cf_styles-ie-css\' href=\"/cdn-cgi/styles/cf.errors.ie.css\" /><![endif]-->\n<style>body{margin:0;padding:0}</style>\n\n\n<!--[if gte IE 10]><!-->\n<script>\n  if (!navigator.cookieEnabled) {\n    window.addEventListener(\'DOMContentLoaded\', function () {\n      var cookieEl = document.getElementById(\'cookie-alert\');\n      cookieEl.style.display = \'block\';\n    })\n  }\n</script>\n<!--<![endif]-->\n\n\n</head>\n<body>\n  <div id=\"cf-wrapper\">\n    <div class=\"cf-alert cf-alert-error cf-cookie-error\" id=\"cookie-alert\" data-translate=\"enable_cookies\">Please enable cookies.</div>\n    <div id=\"cf-error-details\" class=\"cf-error-details-wrapper\">\n      <div class=\"cf-wrapper cf-header cf-error-overview\">\n        <h1>\n          <span class=\"cf-error-type\" data-translate=\"error\">Error</span>\n          <span class=\"cf-error-code\">1101</span>\n          <small class=\"heading-ray-id\">Ray ID: 96c7e9aa4ce1410c &bull; 2025-08-09 14:23:41 UTC</small>\n        </h1>\n        <h2 class=\"cf-subheadline\" data-translate=\"error_desc\">Worker threw exception</h2>\n      </div><!-- /.header -->\n\n      <section></section><!-- spacer -->\n\n      <div class=\"cf-section cf-wrapper\">\n        <div class=\"cf-columns two\">\n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_happened\">What happened?</h2>\n            <p>You\'ve requested a page on a website (free-sms-api.svxtract.workers.dev) that is on the <a href=\"https://www.cloudflare.com/5xx-error-landing/\" target=\"_blank\">Cloudflare</a> network. An unknown error occurred while rendering the page.</p>\n          </div>\n\n          \n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_can_i_do\">What can I do?</h2>\n            <p><strong>If you are the owner of this website:</strong><br />refer to <a href=\"https://developers.cloudflare.com/workers/observability/errors/\" target=\"_blank\">Workers - Errors and Exceptions</a> and check Workers Logs for free-sms-api.svxtract.workers.dev.</p>\n          </div>\n          \n        </div>\n      </div><!-- /.section -->\n\n      <div class=\"cf-error-footer cf-wrapper w-240 lg:w-full py-10 sm:py-4 sm:px-8 mx-auto text-center sm:text-left border-solid border-0 border-t border-gray-300\">\n  <p class=\"text-13\">\n    <span class=\"cf-footer-item sm:block sm:mb-1\">Cloudflare Ray ID: <strong class=\"font-semibold\">96c7e9aa4ce1410c</strong></span>\n    <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    <span id=\"cf-footer-item-ip\" class=\"cf-footer-item hidden sm:block sm:mb-1\">\n      Your IP:\n      <button type=\"button\" id=\"cf-footer-ip-reveal\" class=\"cf-footer-ip-reveal-btn\">Click to reveal</button>\n      <span class=\"hidden\" id=\"cf-footer-ip\">209.35.171.180</span>\n      <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    </span>\n    <span class=\"cf-footer-item sm:block sm:mb-1\"><span>Performance &amp; security by</span> <a rel=\"noopener noreferrer\" href=\"https://www.cloudflare.com/5xx-error-landing\" id=\"brand_link\" target=\"_blank\">Cloudflare</a></span>\n    \n  </p>\n  <script>(function(){function d(){var b=a.getElementById(\"cf-footer-item-ip\"),c=a.getElementById(\"cf-footer-ip-reveal\");b&&\"classList\"in b&&(b.classList.remove(\"hidden\"),c.addEventListener(\"click\",function(){c.classList.add(\"hidden\");a.getElementById(\"cf-footer-ip\").classList.remove(\"hidden\")}))}var a=document;document.addEventListener&&a.addEventListener(\"DOMContentLoaded\",d)})();</script>\n</div><!-- /.error-footer -->\n\n\n    </div><!-- /#cf-error-details -->\n  </div><!-- /#cf-wrapper -->\n\n  <script>\n  window._cf_translation = {};\n  \n  \n</script>\n\n</body>\n</html>\n', '2025-08-09 14:23:40', '2025-08-09 14:23:40'),
(225, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:27:02', '2025-08-09 14:27:02'),
(226, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - <!DOCTYPE html>\n<!--[if lt IE 7]> <html class=\"no-js ie6 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 7]>    <html class=\"no-js ie7 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 8]>    <html class=\"no-js ie8 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if gt IE 8]><!--> <html class=\"no-js\" lang=\"en-US\"> <!--<![endif]-->\n<head>\n<title>Worker threw exception | free-sms-api.svxtract.workers.dev | Cloudflare</title>\n<meta charset=\"UTF-8\" />\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\" />\n<meta name=\"robots\" content=\"noindex, nofollow\" />\n<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\" />\n<link rel=\"stylesheet\" id=\"cf_styles-css\" href=\"/cdn-cgi/styles/cf.errors.css\" />\n<!--[if lt IE 9]><link rel=\"stylesheet\" id=\'cf_styles-ie-css\' href=\"/cdn-cgi/styles/cf.errors.ie.css\" /><![endif]-->\n<style>body{margin:0;padding:0}</style>\n\n\n<!--[if gte IE 10]><!-->\n<script>\n  if (!navigator.cookieEnabled) {\n    window.addEventListener(\'DOMContentLoaded\', function () {\n      var cookieEl = document.getElementById(\'cookie-alert\');\n      cookieEl.style.display = \'block\';\n    })\n  }\n</script>\n<!--<![endif]-->\n\n\n</head>\n<body>\n  <div id=\"cf-wrapper\">\n    <div class=\"cf-alert cf-alert-error cf-cookie-error\" id=\"cookie-alert\" data-translate=\"enable_cookies\">Please enable cookies.</div>\n    <div id=\"cf-error-details\" class=\"cf-error-details-wrapper\">\n      <div class=\"cf-wrapper cf-header cf-error-overview\">\n        <h1>\n          <span class=\"cf-error-type\" data-translate=\"error\">Error</span>\n          <span class=\"cf-error-code\">1101</span>\n          <small class=\"heading-ray-id\">Ray ID: 96c7ee9abdc7f882 &bull; 2025-08-09 14:27:03 UTC</small>\n        </h1>\n        <h2 class=\"cf-subheadline\" data-translate=\"error_desc\">Worker threw exception</h2>\n      </div><!-- /.header -->\n\n      <section></section><!-- spacer -->\n\n      <div class=\"cf-section cf-wrapper\">\n        <div class=\"cf-columns two\">\n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_happened\">What happened?</h2>\n            <p>You\'ve requested a page on a website (free-sms-api.svxtract.workers.dev) that is on the <a href=\"https://www.cloudflare.com/5xx-error-landing/\" target=\"_blank\">Cloudflare</a> network. An unknown error occurred while rendering the page.</p>\n          </div>\n\n          \n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_can_i_do\">What can I do?</h2>\n            <p><strong>If you are the owner of this website:</strong><br />refer to <a href=\"https://developers.cloudflare.com/workers/observability/errors/\" target=\"_blank\">Workers - Errors and Exceptions</a> and check Workers Logs for free-sms-api.svxtract.workers.dev.</p>\n          </div>\n          \n        </div>\n      </div><!-- /.section -->\n\n      <div class=\"cf-error-footer cf-wrapper w-240 lg:w-full py-10 sm:py-4 sm:px-8 mx-auto text-center sm:text-left border-solid border-0 border-t border-gray-300\">\n  <p class=\"text-13\">\n    <span class=\"cf-footer-item sm:block sm:mb-1\">Cloudflare Ray ID: <strong class=\"font-semibold\">96c7ee9abdc7f882</strong></span>\n    <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    <span id=\"cf-footer-item-ip\" class=\"cf-footer-item hidden sm:block sm:mb-1\">\n      Your IP:\n      <button type=\"button\" id=\"cf-footer-ip-reveal\" class=\"cf-footer-ip-reveal-btn\">Click to reveal</button>\n      <span class=\"hidden\" id=\"cf-footer-ip\">209.35.171.180</span>\n      <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    </span>\n    <span class=\"cf-footer-item sm:block sm:mb-1\"><span>Performance &amp; security by</span> <a rel=\"noopener noreferrer\" href=\"https://www.cloudflare.com/5xx-error-landing\" id=\"brand_link\" target=\"_blank\">Cloudflare</a></span>\n    \n  </p>\n  <script>(function(){function d(){var b=a.getElementById(\"cf-footer-item-ip\"),c=a.getElementById(\"cf-footer-ip-reveal\");b&&\"classList\"in b&&(b.classList.remove(\"hidden\"),c.addEventListener(\"click\",function(){c.classList.add(\"hidden\");a.getElementById(\"cf-footer-ip\").classList.remove(\"hidden\")}))}var a=document;document.addEventListener&&a.addEventListener(\"DOMContentLoaded\",d)})();</script>\n</div><!-- /.error-footer -->\n\n\n    </div><!-- /#cf-error-details -->\n  </div><!-- /#cf-wrapper -->\n\n  <script>\n  window._cf_translation = {};\n  \n  \n</script>\n\n</body>\n</html>\n', '2025-08-09 14:27:03', '2025-08-09 14:27:03'),
(227, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:28:41', '2025-08-09 14:28:41'),
(228, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:28:41', '2025-08-09 14:28:41'),
(229, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:32:02', '2025-08-09 14:32:02'),
(230, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:32:03', '2025-08-09 14:32:03'),
(231, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:33:41', '2025-08-09 14:33:41'),
(232, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - <!DOCTYPE html>\n<!--[if lt IE 7]> <html class=\"no-js ie6 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 7]>    <html class=\"no-js ie7 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if IE 8]>    <html class=\"no-js ie8 oldie\" lang=\"en-US\"> <![endif]-->\n<!--[if gt IE 8]><!--> <html class=\"no-js\" lang=\"en-US\"> <!--<![endif]-->\n<head>\n<title>Worker threw exception | free-sms-api.svxtract.workers.dev | Cloudflare</title>\n<meta charset=\"UTF-8\" />\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\" />\n<meta name=\"robots\" content=\"noindex, nofollow\" />\n<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\" />\n<link rel=\"stylesheet\" id=\"cf_styles-css\" href=\"/cdn-cgi/styles/cf.errors.css\" />\n<!--[if lt IE 9]><link rel=\"stylesheet\" id=\'cf_styles-ie-css\' href=\"/cdn-cgi/styles/cf.errors.ie.css\" /><![endif]-->\n<style>body{margin:0;padding:0}</style>\n\n\n<!--[if gte IE 10]><!-->\n<script>\n  if (!navigator.cookieEnabled) {\n    window.addEventListener(\'DOMContentLoaded\', function () {\n      var cookieEl = document.getElementById(\'cookie-alert\');\n      cookieEl.style.display = \'block\';\n    })\n  }\n</script>\n<!--<![endif]-->\n\n\n</head>\n<body>\n  <div id=\"cf-wrapper\">\n    <div class=\"cf-alert cf-alert-error cf-cookie-error\" id=\"cookie-alert\" data-translate=\"enable_cookies\">Please enable cookies.</div>\n    <div id=\"cf-error-details\" class=\"cf-error-details-wrapper\">\n      <div class=\"cf-wrapper cf-header cf-error-overview\">\n        <h1>\n          <span class=\"cf-error-type\" data-translate=\"error\">Error</span>\n          <span class=\"cf-error-code\">1101</span>\n          <small class=\"heading-ray-id\">Ray ID: 96c7f8548de3f93f &bull; 2025-08-09 14:33:41 UTC</small>\n        </h1>\n        <h2 class=\"cf-subheadline\" data-translate=\"error_desc\">Worker threw exception</h2>\n      </div><!-- /.header -->\n\n      <section></section><!-- spacer -->\n\n      <div class=\"cf-section cf-wrapper\">\n        <div class=\"cf-columns two\">\n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_happened\">What happened?</h2>\n            <p>You\'ve requested a page on a website (free-sms-api.svxtract.workers.dev) that is on the <a href=\"https://www.cloudflare.com/5xx-error-landing/\" target=\"_blank\">Cloudflare</a> network. An unknown error occurred while rendering the page.</p>\n          </div>\n\n          \n          <div class=\"cf-column\">\n            <h2 data-translate=\"what_can_i_do\">What can I do?</h2>\n            <p><strong>If you are the owner of this website:</strong><br />refer to <a href=\"https://developers.cloudflare.com/workers/observability/errors/\" target=\"_blank\">Workers - Errors and Exceptions</a> and check Workers Logs for free-sms-api.svxtract.workers.dev.</p>\n          </div>\n          \n        </div>\n      </div><!-- /.section -->\n\n      <div class=\"cf-error-footer cf-wrapper w-240 lg:w-full py-10 sm:py-4 sm:px-8 mx-auto text-center sm:text-left border-solid border-0 border-t border-gray-300\">\n  <p class=\"text-13\">\n    <span class=\"cf-footer-item sm:block sm:mb-1\">Cloudflare Ray ID: <strong class=\"font-semibold\">96c7f8548de3f93f</strong></span>\n    <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    <span id=\"cf-footer-item-ip\" class=\"cf-footer-item hidden sm:block sm:mb-1\">\n      Your IP:\n      <button type=\"button\" id=\"cf-footer-ip-reveal\" class=\"cf-footer-ip-reveal-btn\">Click to reveal</button>\n      <span class=\"hidden\" id=\"cf-footer-ip\">209.35.171.180</span>\n      <span class=\"cf-footer-separator sm:hidden\">&bull;</span>\n    </span>\n    <span class=\"cf-footer-item sm:block sm:mb-1\"><span>Performance &amp; security by</span> <a rel=\"noopener noreferrer\" href=\"https://www.cloudflare.com/5xx-error-landing\" id=\"brand_link\" target=\"_blank\">Cloudflare</a></span>\n    \n  </p>\n  <script>(function(){function d(){var b=a.getElementById(\"cf-footer-item-ip\"),c=a.getElementById(\"cf-footer-ip-reveal\");b&&\"classList\"in b&&(b.classList.remove(\"hidden\"),c.addEventListener(\"click\",function(){c.classList.add(\"hidden\");a.getElementById(\"cf-footer-ip\").classList.remove(\"hidden\")}))}var a=document;document.addEventListener&&a.addEventListener(\"DOMContentLoaded\",d)})();</script>\n</div><!-- /.error-footer -->\n\n\n    </div><!-- /#cf-error-details -->\n  </div><!-- /#cf-wrapper -->\n\n  <script>\n  window._cf_translation = {};\n  \n  \n</script>\n\n</body>\n</html>\n', '2025-08-09 14:33:41', '2025-08-09 14:33:41'),
(233, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:37:02', '2025-08-09 14:37:02'),
(234, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:37:02', '2025-08-09 14:37:02'),
(235, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:38:40', '2025-08-09 14:38:40'),
(236, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:38:41', '2025-08-09 14:38:41'),
(237, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:42:02', '2025-08-09 14:42:02'),
(238, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:42:03', '2025-08-09 14:42:03'),
(239, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:43:40', '2025-08-09 14:43:40'),
(240, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:43:40', '2025-08-09 14:43:40'),
(241, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:47:01', '2025-08-09 14:47:01'),
(242, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:47:02', '2025-08-09 14:47:02'),
(243, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:48:40', '2025-08-09 14:48:40'),
(244, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:48:40', '2025-08-09 14:48:40'),
(245, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-09 14:52:05', '2025-08-09 14:52:05'),
(246, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 500 - error code: 1101', '2025-08-09 14:52:05', '2025-08-09 14:52:05'),
(247, 'email', 'trevorlouvenne@gmail.com', 'LSPU EIS Reminder', 'This is a reminder from LSPU Employment and Information System.', 'sent', NULL, '2025-08-09 15:20:56', '2025-08-09 15:20:56'),
(248, 'email', '0322-1945@lspu.edu.ph', 'LSPU EIS Reminder', 'This is a reminder from LSPU Employment and Information System.', 'sent', NULL, '2025-08-09 15:21:01', '2025-08-09 15:21:01'),
(249, 'email', 'brightprint24@gmail.com', 'LSPU EIS Reminder', 'This is a reminder from LSPU Employment and Information System.', 'sent', NULL, '2025-08-09 15:21:06', '2025-08-09 15:21:06'),
(250, 'email', 'allencristal12@gmail.com', 'LSPU EIS Reminder', 'This is a reminder from LSPU Employment and Information System.', 'sent', NULL, '2025-08-09 15:21:10', '2025-08-09 15:21:10'),
(251, 'email', 'allencristal22@gmail.com', 'LSPU EIS Reminder', 'This is a reminder from LSPU Employment and Information System.', 'sent', NULL, '2025-08-09 15:21:15', '2025-08-09 15:21:15'),
(252, 'email', 'admin.lspu1@example.com', 'LSPU EIS Reminder', 'This is a reminder from LSPU Employment and Information System.', 'sent', NULL, '2025-08-09 15:21:21', '2025-08-09 15:21:21'),
(253, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-13 08:22:50', '2025-08-13 08:22:50'),
(254, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-13 08:22:52', '2025-08-13 08:22:52'),
(255, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-14 10:30:48', '2025-08-14 10:30:48'),
(256, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-14 10:30:50', '2025-08-14 10:30:50'),
(257, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-14 10:33:43', '2025-08-14 10:33:43'),
(258, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-14 10:33:45', '2025-08-14 10:33:45'),
(259, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-14 10:35:48', '2025-08-14 10:35:48'),
(260, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-14 10:35:50', '2025-08-14 10:35:50'),
(261, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-14 10:38:41', '2025-08-14 10:38:41'),
(262, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-14 10:38:43', '2025-08-14 10:38:43'),
(263, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-14 10:40:51', '2025-08-14 10:40:51'),
(264, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-14 10:40:53', '2025-08-14 10:40:53'),
(265, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:41:55', '2025-08-17 07:41:55'),
(266, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:41:57', '2025-08-17 07:41:57'),
(267, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:43:43', '2025-08-17 07:43:43'),
(268, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:43:45', '2025-08-17 07:43:45'),
(269, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:46:54', '2025-08-17 07:46:54'),
(270, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:46:56', '2025-08-17 07:46:56'),
(271, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:48:43', '2025-08-17 07:48:43'),
(272, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:48:45', '2025-08-17 07:48:45'),
(273, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:51:53', '2025-08-17 07:51:53'),
(274, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:51:55', '2025-08-17 07:51:55'),
(275, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:53:43', '2025-08-17 07:53:43'),
(276, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:53:45', '2025-08-17 07:53:45'),
(277, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:56:52', '2025-08-17 07:56:52'),
(278, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:56:53', '2025-08-17 07:56:53'),
(279, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:58:41', '2025-08-17 07:58:41'),
(280, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 07:58:44', '2025-08-17 07:58:44'),
(281, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 08:01:52', '2025-08-17 08:01:52'),
(282, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 08:01:54', '2025-08-17 08:01:54'),
(283, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 08:03:43', '2025-08-17 08:03:43'),
(284, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 08:03:45', '2025-08-17 08:03:45'),
(285, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 08:06:52', '2025-08-17 08:06:52'),
(286, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 08:06:54', '2025-08-17 08:06:54'),
(287, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 08:08:43', '2025-08-17 08:08:43'),
(288, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 08:08:45', '2025-08-17 08:08:45'),
(289, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 09:36:10', '2025-08-17 09:36:10'),
(290, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 09:36:12', '2025-08-17 09:36:12'),
(291, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 09:37:47', '2025-08-17 09:37:47'),
(292, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 09:37:48', '2025-08-17 09:37:48'),
(293, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 09:37:48', '2025-08-17 09:37:48'),
(294, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 09:37:50', '2025-08-17 09:37:50'),
(295, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:23:06', '2025-08-17 13:23:06'),
(296, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:23:09', '2025-08-17 13:23:09'),
(297, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:23:42', '2025-08-17 13:23:42'),
(298, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:23:44', '2025-08-17 13:23:44'),
(299, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:26:53', '2025-08-17 13:26:53'),
(300, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:26:55', '2025-08-17 13:26:55'),
(301, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:28:42', '2025-08-17 13:28:42'),
(302, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:28:44', '2025-08-17 13:28:44'),
(303, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:31:52', '2025-08-17 13:31:52'),
(304, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:31:54', '2025-08-17 13:31:54'),
(305, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:33:41', '2025-08-17 13:33:41'),
(306, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:33:44', '2025-08-17 13:33:44'),
(307, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:36:54', '2025-08-17 13:36:54'),
(308, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'HTTP Error: 200 - lang_undefined', '2025-08-17 13:37:00', '2025-08-17 13:37:00'),
(309, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:38:43', '2025-08-17 13:38:43'),
(310, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:38:45', '2025-08-17 13:38:45'),
(311, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:41:53', '2025-08-17 13:41:53'),
(312, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:41:55', '2025-08-17 13:41:55'),
(313, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:43:43', '2025-08-17 13:43:43'),
(314, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:43:45', '2025-08-17 13:43:45'),
(315, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:46:54', '2025-08-17 13:46:54'),
(316, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:46:56', '2025-08-17 13:46:56'),
(317, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:48:42', '2025-08-17 13:48:42'),
(318, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:48:43', '2025-08-17 13:48:43'),
(319, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:51:52', '2025-08-17 13:51:52'),
(320, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:51:54', '2025-08-17 13:51:54'),
(321, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:53:43', '2025-08-17 13:53:43'),
(322, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:53:45', '2025-08-17 13:53:45'),
(323, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:56:54', '2025-08-17 13:56:54'),
(324, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:56:56', '2025-08-17 13:56:56'),
(325, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:58:43', '2025-08-17 13:58:43'),
(326, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 13:58:45', '2025-08-17 13:58:45'),
(327, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:01:52', '2025-08-17 14:01:52'),
(328, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:01:59', '2025-08-17 14:01:59'),
(329, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:03:43', '2025-08-17 14:03:43'),
(330, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:03:45', '2025-08-17 14:03:45'),
(331, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:06:53', '2025-08-17 14:06:53'),
(332, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:06:55', '2025-08-17 14:06:55'),
(333, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:08:42', '2025-08-17 14:08:42'),
(334, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:08:44', '2025-08-17 14:08:44'),
(335, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:11:52', '2025-08-17 14:11:52'),
(336, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:11:54', '2025-08-17 14:11:54'),
(337, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:13:42', '2025-08-17 14:13:42'),
(338, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:13:44', '2025-08-17 14:13:44'),
(339, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:16:52', '2025-08-17 14:16:52'),
(340, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:16:54', '2025-08-17 14:16:54'),
(341, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:18:44', '2025-08-17 14:18:44'),
(342, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:18:45', '2025-08-17 14:18:45'),
(343, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:21:52', '2025-08-17 14:21:52'),
(344, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:21:54', '2025-08-17 14:21:54'),
(345, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:23:43', '2025-08-17 14:23:43'),
(346, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:23:46', '2025-08-17 14:23:46'),
(347, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:26:52', '2025-08-17 14:26:52'),
(348, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:26:54', '2025-08-17 14:26:54'),
(349, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:28:41', '2025-08-17 14:28:41');
INSERT INTO `reminder_logs` (`id`, `type`, `recipient`, `subject`, `message`, `status`, `error_message`, `sent_at`, `created_at`) VALUES
(350, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:28:43', '2025-08-17 14:28:43'),
(351, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:31:52', '2025-08-17 14:31:52'),
(352, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:31:54', '2025-08-17 14:31:54'),
(353, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:33:43', '2025-08-17 14:33:43'),
(354, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:33:45', '2025-08-17 14:33:45'),
(355, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:36:52', '2025-08-17 14:36:52'),
(356, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:36:54', '2025-08-17 14:36:54'),
(357, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:38:43', '2025-08-17 14:38:43'),
(358, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:38:45', '2025-08-17 14:38:45'),
(359, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:41:53', '2025-08-17 14:41:53'),
(360, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:41:55', '2025-08-17 14:41:55'),
(361, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:43:41', '2025-08-17 14:43:41'),
(362, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:43:43', '2025-08-17 14:43:43'),
(363, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:46:51', '2025-08-17 14:46:51'),
(364, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:46:53', '2025-08-17 14:46:53'),
(365, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:48:41', '2025-08-17 14:48:41'),
(366, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:48:44', '2025-08-17 14:48:44'),
(367, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:51:54', '2025-08-17 14:51:54'),
(368, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:51:56', '2025-08-17 14:51:56'),
(369, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:53:43', '2025-08-17 14:53:43'),
(370, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:53:44', '2025-08-17 14:53:44'),
(371, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:56:54', '2025-08-17 14:56:54'),
(372, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:56:56', '2025-08-17 14:56:56'),
(373, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:58:43', '2025-08-17 14:58:43'),
(374, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-17 14:58:45', '2025-08-17 14:58:45'),
(375, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:28:31', '2025-08-19 11:28:31'),
(376, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:28:33', '2025-08-19 11:28:33'),
(377, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:28:42', '2025-08-19 11:28:42'),
(378, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:28:44', '2025-08-19 11:28:44'),
(379, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:33:31', '2025-08-19 11:33:31'),
(380, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:33:34', '2025-08-19 11:33:34'),
(381, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:33:44', '2025-08-19 11:33:44'),
(382, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:33:46', '2025-08-19 11:33:46'),
(383, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:38:40', '2025-08-19 11:38:40'),
(384, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:38:42', '2025-08-19 11:38:42'),
(385, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:43:33', '2025-08-19 11:43:33'),
(386, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:43:35', '2025-08-19 11:43:35'),
(387, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:48:32', '2025-08-19 11:48:32'),
(388, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:48:34', '2025-08-19 11:48:34'),
(389, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:48:44', '2025-08-19 11:48:44'),
(390, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:48:46', '2025-08-19 11:48:46'),
(391, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:53:30', '2025-08-19 11:53:30'),
(392, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:53:33', '2025-08-19 11:53:33'),
(393, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:53:44', '2025-08-19 11:53:44'),
(394, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-19 11:53:48', '2025-08-19 11:53:48'),
(395, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:00:55', '2025-08-22 14:00:55'),
(396, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:00:59', '2025-08-22 14:00:59'),
(397, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:03:43', '2025-08-22 14:03:43'),
(398, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:03:49', '2025-08-22 14:03:49'),
(399, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:05:56', '2025-08-22 14:05:56'),
(400, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:05:58', '2025-08-22 14:05:58'),
(401, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:08:43', '2025-08-22 14:08:43'),
(402, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:08:53', '2025-08-22 14:08:53'),
(403, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:10:56', '2025-08-22 14:10:56'),
(404, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:11:00', '2025-08-22 14:11:00'),
(405, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:13:42', '2025-08-22 14:13:42'),
(406, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:13:44', '2025-08-22 14:13:44'),
(407, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:15:54', '2025-08-22 14:15:54'),
(408, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:15:57', '2025-08-22 14:15:57'),
(409, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:18:42', '2025-08-22 14:18:42'),
(410, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:18:48', '2025-08-22 14:18:48'),
(411, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:20:56', '2025-08-22 14:20:56'),
(412, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:20:59', '2025-08-22 14:20:59'),
(413, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:23:41', '2025-08-22 14:23:41'),
(414, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:23:45', '2025-08-22 14:23:45'),
(415, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:25:56', '2025-08-22 14:25:56'),
(416, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:26:02', '2025-08-22 14:26:02'),
(417, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:28:40', '2025-08-22 14:28:40'),
(418, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:28:43', '2025-08-22 14:28:43'),
(419, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:30:54', '2025-08-22 14:30:54'),
(420, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:30:57', '2025-08-22 14:30:57'),
(421, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:33:41', '2025-08-22 14:33:41'),
(422, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:33:43', '2025-08-22 14:33:43'),
(423, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:35:55', '2025-08-22 14:35:55'),
(424, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:36:00', '2025-08-22 14:36:00'),
(425, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:38:42', '2025-08-22 14:38:42'),
(426, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:38:47', '2025-08-22 14:38:47'),
(427, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:40:56', '2025-08-22 14:40:56'),
(428, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:41:08', '2025-08-22 14:41:08'),
(429, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:43:43', '2025-08-22 14:43:43'),
(430, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:43:54', '2025-08-22 14:43:54'),
(431, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:45:54', '2025-08-22 14:45:54'),
(432, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:46:04', '2025-08-22 14:46:04'),
(433, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:48:42', '2025-08-22 14:48:42'),
(434, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:48:44', '2025-08-22 14:48:44'),
(435, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:50:55', '2025-08-22 14:50:55'),
(436, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:50:58', '2025-08-22 14:50:58'),
(437, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:53:40', '2025-08-22 14:53:40'),
(438, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:53:44', '2025-08-22 14:53:44'),
(439, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:55:55', '2025-08-22 14:55:55'),
(440, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:55:59', '2025-08-22 14:55:59'),
(441, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:58:40', '2025-08-22 14:58:40'),
(442, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-22 14:59:56', '2025-08-22 14:59:56'),
(443, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-23 09:12:12', '2025-08-23 09:12:12'),
(444, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-23 09:12:14', '2025-08-23 09:12:14'),
(445, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:44:02', '2025-08-24 09:44:02'),
(446, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:44:04', '2025-08-24 09:44:04'),
(447, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:47:29', '2025-08-24 09:47:29'),
(448, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:47:32', '2025-08-24 09:47:32'),
(449, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:48:41', '2025-08-24 09:48:41'),
(450, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:48:43', '2025-08-24 09:48:43'),
(451, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:48:56', '2025-08-24 09:48:56'),
(452, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:48:58', '2025-08-24 09:48:58'),
(453, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:52:29', '2025-08-24 09:52:29'),
(454, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:52:31', '2025-08-24 09:52:31'),
(455, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:53:42', '2025-08-24 09:53:42'),
(456, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:53:44', '2025-08-24 09:53:44'),
(457, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:53:55', '2025-08-24 09:53:55'),
(458, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:53:57', '2025-08-24 09:53:57'),
(459, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:57:29', '2025-08-24 09:57:29'),
(460, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:57:31', '2025-08-24 09:57:31'),
(461, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:58:41', '2025-08-24 09:58:41'),
(462, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:58:44', '2025-08-24 09:58:44'),
(463, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:58:56', '2025-08-24 09:58:56'),
(464, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 09:58:58', '2025-08-24 09:58:58'),
(465, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:02:29', '2025-08-24 10:02:29'),
(466, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:02:31', '2025-08-24 10:02:31'),
(467, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:03:41', '2025-08-24 10:03:41'),
(468, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:03:43', '2025-08-24 10:03:43'),
(469, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:03:56', '2025-08-24 10:03:56'),
(470, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:03:58', '2025-08-24 10:03:58'),
(471, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:07:28', '2025-08-24 10:07:28'),
(472, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:07:30', '2025-08-24 10:07:30'),
(473, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:08:42', '2025-08-24 10:08:42'),
(474, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:08:44', '2025-08-24 10:08:44'),
(475, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:08:56', '2025-08-24 10:08:56'),
(476, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:08:58', '2025-08-24 10:08:58'),
(477, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:12:31', '2025-08-24 10:12:31'),
(478, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:12:33', '2025-08-24 10:12:33'),
(479, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:13:41', '2025-08-24 10:13:41'),
(480, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:13:43', '2025-08-24 10:13:43'),
(481, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:13:55', '2025-08-24 10:13:55'),
(482, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:13:57', '2025-08-24 10:13:57'),
(483, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:17:28', '2025-08-24 10:17:28'),
(484, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:17:31', '2025-08-24 10:17:31'),
(485, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:18:41', '2025-08-24 10:18:41'),
(486, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:18:43', '2025-08-24 10:18:43'),
(487, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:18:56', '2025-08-24 10:18:56'),
(488, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:18:58', '2025-08-24 10:18:58'),
(489, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:22:29', '2025-08-24 10:22:29'),
(490, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:22:31', '2025-08-24 10:22:31'),
(491, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:23:41', '2025-08-24 10:23:41'),
(492, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:23:43', '2025-08-24 10:23:43'),
(493, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:23:56', '2025-08-24 10:23:56'),
(494, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-24 10:23:58', '2025-08-24 10:23:58'),
(495, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'failed', 'Email could not be sent. Mailer Error: SMTP Error: Could not connect to SMTP host. Failed to connect to server SMTP server error: Failed to connect to server Additional SMTP info: php_network_getaddresses: getaddrinfo for smtp.gmail.com failed: No such host is known. ', '2025-08-24 11:15:36', '2025-08-24 11:15:36'),
(496, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'cURL Error: Could not resolve host: www.cloud.smschef.com', '2025-08-24 11:15:36', '2025-08-24 11:15:36'),
(497, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:44:48', '2025-08-25 06:44:48'),
(498, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:44:51', '2025-08-25 06:44:51'),
(499, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:48:40', '2025-08-25 06:48:40'),
(500, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:48:42', '2025-08-25 06:48:42'),
(501, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:48:55', '2025-08-25 06:48:55'),
(502, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:48:57', '2025-08-25 06:48:57'),
(503, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:49:48', '2025-08-25 06:49:48'),
(504, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:49:50', '2025-08-25 06:49:50'),
(505, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:53:40', '2025-08-25 06:53:40'),
(506, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:53:43', '2025-08-25 06:53:43'),
(507, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:53:55', '2025-08-25 06:53:55'),
(508, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:53:56', '2025-08-25 06:53:56'),
(509, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:54:48', '2025-08-25 06:54:48'),
(510, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:54:50', '2025-08-25 06:54:50'),
(511, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:58:44', '2025-08-25 06:58:44'),
(512, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:58:46', '2025-08-25 06:58:46'),
(513, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:58:55', '2025-08-25 06:58:55'),
(514, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:58:57', '2025-08-25 06:58:57'),
(515, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:59:47', '2025-08-25 06:59:47'),
(516, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 06:59:49', '2025-08-25 06:59:49'),
(517, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:03:40', '2025-08-25 07:03:40'),
(518, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:03:42', '2025-08-25 07:03:42'),
(519, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:03:55', '2025-08-25 07:03:55'),
(520, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:03:57', '2025-08-25 07:03:57'),
(521, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:04:49', '2025-08-25 07:04:49'),
(522, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:04:51', '2025-08-25 07:04:51'),
(523, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:08:41', '2025-08-25 07:08:41'),
(524, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:08:43', '2025-08-25 07:08:43');
INSERT INTO `reminder_logs` (`id`, `type`, `recipient`, `subject`, `message`, `status`, `error_message`, `sent_at`, `created_at`) VALUES
(525, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:08:56', '2025-08-25 07:08:56'),
(526, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:08:58', '2025-08-25 07:08:58'),
(527, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:09:48', '2025-08-25 07:09:48'),
(528, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:09:50', '2025-08-25 07:09:50'),
(529, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:13:40', '2025-08-25 07:13:40'),
(530, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:13:42', '2025-08-25 07:13:42'),
(531, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:13:55', '2025-08-25 07:13:55'),
(532, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:13:57', '2025-08-25 07:13:57'),
(533, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:14:49', '2025-08-25 07:14:49'),
(534, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:14:51', '2025-08-25 07:14:51'),
(535, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:18:40', '2025-08-25 07:18:40'),
(536, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:18:42', '2025-08-25 07:18:42'),
(537, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:18:55', '2025-08-25 07:18:55'),
(538, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:18:57', '2025-08-25 07:18:57'),
(539, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:19:48', '2025-08-25 07:19:48'),
(540, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-25 07:19:50', '2025-08-25 07:19:50'),
(541, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 05:02:04', '2025-08-26 05:02:04'),
(542, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 05:05:08', '2025-08-26 05:05:08'),
(543, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 05:05:14', '2025-08-26 05:05:14'),
(544, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 05:08:02', '2025-08-26 05:08:02'),
(545, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 05:08:08', '2025-08-26 05:08:08'),
(546, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 10:39:50', '2025-08-26 10:39:50'),
(547, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 10:39:52', '2025-08-26 10:39:52'),
(548, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 10:45:06', '2025-08-26 10:45:06'),
(549, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 10:45:08', '2025-08-26 10:45:08'),
(550, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 10:46:57', '2025-08-26 10:46:57'),
(551, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 10:46:58', '2025-08-26 10:46:58'),
(552, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:25:12', '2025-08-26 11:25:12'),
(553, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:25:14', '2025-08-26 11:25:14'),
(554, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:28:41', '2025-08-26 11:28:41'),
(555, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:28:43', '2025-08-26 11:28:43'),
(556, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:29:00', '2025-08-26 11:29:00'),
(557, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'cURL Error: Recv failure: Connection was reset', '2025-08-26 11:29:28', '2025-08-26 11:29:28'),
(558, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:30:08', '2025-08-26 11:30:08'),
(559, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:30:10', '2025-08-26 11:30:10'),
(560, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:33:43', '2025-08-26 11:33:43'),
(561, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:33:44', '2025-08-26 11:33:44'),
(562, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:33:58', '2025-08-26 11:33:58'),
(563, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:34:00', '2025-08-26 11:34:00'),
(564, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:35:07', '2025-08-26 11:35:07'),
(565, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:35:09', '2025-08-26 11:35:09'),
(566, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:38:43', '2025-08-26 11:38:43'),
(567, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:38:45', '2025-08-26 11:38:45'),
(568, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:38:58', '2025-08-26 11:38:58'),
(569, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:39:00', '2025-08-26 11:39:00'),
(570, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:40:09', '2025-08-26 11:40:09'),
(571, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:40:11', '2025-08-26 11:40:11'),
(572, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:43:43', '2025-08-26 11:43:43'),
(573, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:43:45', '2025-08-26 11:43:45'),
(574, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:43:56', '2025-08-26 11:43:56'),
(575, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:43:58', '2025-08-26 11:43:58'),
(576, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:45:09', '2025-08-26 11:45:09'),
(577, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:45:11', '2025-08-26 11:45:11'),
(578, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:48:58', '2025-08-26 11:48:58'),
(579, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:49:01', '2025-08-26 11:49:01'),
(580, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:50:09', '2025-08-26 11:50:09'),
(581, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:50:11', '2025-08-26 11:50:11'),
(582, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:53:42', '2025-08-26 11:53:42'),
(583, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:53:44', '2025-08-26 11:53:44'),
(584, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:53:58', '2025-08-26 11:53:58'),
(585, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:54:00', '2025-08-26 11:54:00'),
(586, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:55:09', '2025-08-26 11:55:09'),
(587, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:55:12', '2025-08-26 11:55:12'),
(588, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:58:42', '2025-08-26 11:58:42'),
(589, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:58:44', '2025-08-26 11:58:44'),
(590, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:58:57', '2025-08-26 11:58:57'),
(591, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 11:58:59', '2025-08-26 11:58:59'),
(592, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 12:00:12', '2025-08-26 12:00:12'),
(593, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 12:00:15', '2025-08-26 12:00:15'),
(594, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 12:03:41', '2025-08-26 12:03:41'),
(595, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 12:03:43', '2025-08-26 12:03:43'),
(596, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 12:03:57', '2025-08-26 12:03:57'),
(597, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 12:03:59', '2025-08-26 12:03:59'),
(598, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 12:05:08', '2025-08-26 12:05:08'),
(599, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 12:05:10', '2025-08-26 12:05:10'),
(600, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 12:08:41', '2025-08-26 12:08:41'),
(601, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 12:08:43', '2025-08-26 12:08:43'),
(602, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 12:08:57', '2025-08-26 12:08:57'),
(603, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 12:08:59', '2025-08-26 12:08:59'),
(604, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 12:10:08', '2025-08-26 12:10:08'),
(605, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-26 12:10:11', '2025-08-26 12:10:11'),
(606, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-29 01:43:56', '2025-08-29 01:43:56'),
(607, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-29 01:43:58', '2025-08-29 01:43:58'),
(608, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:08:41', '2025-08-30 02:08:41'),
(609, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:08:43', '2025-08-30 02:08:43'),
(610, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:08:57', '2025-08-30 02:08:57'),
(611, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:09:00', '2025-08-30 02:09:00'),
(612, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:13:43', '2025-08-30 02:13:43'),
(613, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:13:45', '2025-08-30 02:13:45'),
(614, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:13:58', '2025-08-30 02:13:58'),
(615, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:14:01', '2025-08-30 02:14:01'),
(616, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:18:41', '2025-08-30 02:18:41'),
(617, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:18:43', '2025-08-30 02:18:43'),
(618, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:18:58', '2025-08-30 02:18:58'),
(619, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:19:00', '2025-08-30 02:19:00'),
(620, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:23:43', '2025-08-30 02:23:43'),
(621, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:23:44', '2025-08-30 02:23:44'),
(622, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:23:57', '2025-08-30 02:23:57'),
(623, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'failed', 'cURL Error: Recv failure: Connection was reset', '2025-08-30 02:24:21', '2025-08-30 02:24:21'),
(624, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:28:42', '2025-08-30 02:28:42'),
(625, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:28:44', '2025-08-30 02:28:44'),
(626, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:28:58', '2025-08-30 02:28:58'),
(627, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:29:03', '2025-08-30 02:29:03'),
(628, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:33:40', '2025-08-30 02:33:40'),
(629, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:33:42', '2025-08-30 02:33:42'),
(630, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:33:58', '2025-08-30 02:33:58'),
(631, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:34:00', '2025-08-30 02:34:00'),
(632, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:38:58', '2025-08-30 02:38:58'),
(633, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:39:00', '2025-08-30 02:39:00'),
(634, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:43:40', '2025-08-30 02:43:40'),
(635, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:43:42', '2025-08-30 02:43:42'),
(636, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:43:58', '2025-08-30 02:43:58'),
(637, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 02:44:00', '2025-08-30 02:44:00'),
(638, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 04:10:57', '2025-08-30 04:10:57'),
(639, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 04:10:59', '2025-08-30 04:10:59'),
(640, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 04:13:41', '2025-08-30 04:13:41'),
(641, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 04:13:43', '2025-08-30 04:13:43'),
(642, 'email', 'allencristal12@gmail.com', 'LSPU EIS - Automated Reminder', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 04:15:53', '2025-08-30 04:15:53'),
(643, 'sms', '09566888221', 'SMS Reminder', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'sent', NULL, '2025-08-30 04:15:56', '2025-08-30 04:15:56');

-- --------------------------------------------------------

--
-- Table structure for table `reminder_settings`
--

CREATE TABLE `reminder_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reminder_settings`
--

INSERT INTO `reminder_settings` (`id`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'business_hours_start', '9', 'Start hour for business hours (24-hour format)', '2025-07-27 09:05:12', '2025-08-13 08:12:33'),
(2, 'business_hours_end', '23', 'End hour for business hours (24-hour format)', '2025-07-27 09:05:12', '2025-08-13 08:12:33'),
(3, 'timezone', 'Asia/Manila', 'Timezone for the reminder system', '2025-07-27 09:05:12', '2025-07-27 09:05:12'),
(4, 'frequency_minutes', '1', 'How often to send reminders (in minutes)', '2025-07-27 09:05:12', '2025-07-27 09:05:12'),
(5, 'max_reminders_per_day', '3', 'Maximum reminders per user per day', '2025-07-27 09:05:12', '2025-07-27 09:05:12'),
(6, 'email_enabled', '1', 'Enable email reminders (1=yes, 0=no)', '2025-07-27 09:05:12', '2025-07-27 09:05:12'),
(7, 'sms_enabled', '1', 'Enable SMS reminders (1=yes, 0=no)', '2025-07-27 09:05:12', '2025-07-27 09:05:12'),
(8, 'email_subject', 'LSPU EIS - Automated Reminder', 'Default email subject', '2025-07-27 09:05:12', '2025-07-27 09:05:12'),
(9, 'email_message', 'Hello! This is your automated reminder from LSPU Employment and Information System. Please check your account for any updates, job opportunities, or important notifications. Stay connected with your alma mater!', 'Default email message', '2025-07-27 09:05:12', '2025-07-27 09:05:12'),
(10, 'sms_message', 'LSPU EIS Reminder: Check your account for updates and job opportunities. Stay connected with your alma mater!', 'Default SMS message', '2025-07-27 09:05:12', '2025-08-07 08:38:54');

-- --------------------------------------------------------

--
-- Table structure for table `reminder_statistics`
--

CREATE TABLE `reminder_statistics` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `total_users` int(11) DEFAULT 0,
  `emails_sent` int(11) DEFAULT 0,
  `emails_failed` int(11) DEFAULT 0,
  `sms_sent` int(11) DEFAULT 0,
  `sms_failed` int(11) DEFAULT 0,
  `total_sent` int(11) DEFAULT 0,
  `total_failed` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reminder_statistics`
--

INSERT INTO `reminder_statistics` (`id`, `date`, `total_users`, `emails_sent`, `emails_failed`, `sms_sent`, `sms_failed`, `total_sent`, `total_failed`, `created_at`, `updated_at`) VALUES
(1, '2025-07-27', 1, 1, 0, 0, 1, 1, 1, '2025-07-27 09:21:47', '2025-07-27 14:56:57'),
(40, '2025-08-06', 1, 1, 0, 0, 1, 1, 1, '2025-08-06 03:53:44', '2025-08-06 14:03:43'),
(72, '2025-08-09', 1, 1, 0, 0, 1, 1, 1, '2025-08-09 12:47:09', '2025-08-09 14:52:05'),
(123, '2025-08-13', 1, 1, 0, 1, 0, 2, 0, '2025-08-13 08:22:52', '2025-08-13 08:22:52'),
(124, '2025-08-14', 1, 1, 0, 1, 0, 2, 0, '2025-08-14 10:30:50', '2025-08-14 10:40:53'),
(129, '2025-08-17', 1, 1, 0, 1, 0, 2, 0, '2025-08-17 07:41:57', '2025-08-17 14:58:45'),
(184, '2025-08-19', 1, 1, 0, 1, 0, 2, 0, '2025-08-19 11:28:33', '2025-08-19 11:53:48'),
(194, '2025-08-22', 1, 1, 0, 1, 0, 2, 0, '2025-08-22 14:00:59', '2025-08-22 14:59:56'),
(218, '2025-08-23', 1, 1, 0, 1, 0, 2, 0, '2025-08-23 09:12:14', '2025-08-23 09:12:14'),
(219, '2025-08-24', 1, 0, 1, 0, 1, 0, 2, '2025-08-24 09:44:04', '2025-08-24 11:15:36'),
(245, '2025-08-25', 1, 1, 0, 1, 0, 2, 0, '2025-08-25 06:44:51', '2025-08-25 07:19:50'),
(267, '2025-08-26', 1, 1, 0, 1, 0, 2, 0, '2025-08-26 05:05:14', '2025-08-26 12:10:11'),
(299, '2025-08-29', 1, 1, 0, 1, 0, 2, 0, '2025-08-29 01:43:59', '2025-08-29 01:43:59'),
(300, '2025-08-30', 1, 1, 0, 1, 0, 2, 0, '2025-08-30 02:08:44', '2025-08-30 04:15:56');

-- --------------------------------------------------------

--
-- Table structure for table `saved_jobs`
--

CREATE TABLE `saved_jobs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_jobs`
--

INSERT INTO `saved_jobs` (`id`, `user_id`, `job_id`, `saved_at`) VALUES
(5, 1, 1, '2025-07-21 11:07:57'),
(6, 1, 7, '2025-07-22 08:40:39'),
(7, 1, 32, '2025-08-17 15:23:53'),
(9, 1, 50, '2025-09-04 13:49:12');

-- --------------------------------------------------------

--
-- Table structure for table `success_stories`
--

CREATE TABLE `success_stories` (
  `story_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `success_stories`
--

INSERT INTO `success_stories` (`story_id`, `user_id`, `title`, `content`, `status`, `created_at`, `updated_at`) VALUES
(3, 1, 'hi', 'hi', 'published', '2025-08-30 00:22:48', '2025-08-31 11:51:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `secondary_email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `user_role` enum('alumni','employer','admin') NOT NULL DEFAULT 'alumni',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Active') NOT NULL DEFAULT 'Pending',
  `last_login` datetime DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `tutorial_completed` tinyint(1) DEFAULT 0,
  `tutorial_completed_date` datetime DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `two_factor_method` enum('email','authenticator') DEFAULT 'email',
  `two_factor_code` varchar(10) DEFAULT NULL,
  `two_factor_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `email`, `secondary_email`, `password`, `user_role`, `created_at`, `status`, `last_login`, `reset_token`, `reset_token_expiry`, `tutorial_completed`, `tutorial_completed_date`, `two_factor_enabled`, `two_factor_method`, `two_factor_code`, `two_factor_expires`) VALUES
(1, 'allencristal12@gmail.com', 'allencristal23@gmail.com', '$2y$10$xJHdbJ1299DPlfgo8q/kse6vBwykLnNpzKuLOURDcqTNsPBsJOzJK', 'alumni', '2025-07-19 04:03:54', 'Active', '2025-09-10 20:45:54', 'f4cacf91e845bf0b366b75b40d7ed5c7b73ecfeff378c71daf361fa3ceeeafc8', '2025-07-19 10:25:31', 1, '2025-09-02 11:16:46', 0, 'email', NULL, NULL),
(3, 'allencristal22@gmail.com', 'vinceallencristal@gmail.com', '$2y$10$4gd.Dsc2ZTHIO.LvvGNDJ.RV93fuZgae0Z9yO5b3Z0MYa9azFJAmi', 'alumni', '2025-07-19 05:32:24', 'Active', NULL, NULL, NULL, 0, NULL, 0, 'email', NULL, NULL),
(5, 'trevorlouvenne@gmail.com', NULL, '$2y$10$rV3unp18kbmbNiboIK4rVOz5eU.uH9UW5ji1mQqVZdgBZKIcQGNZy', 'employer', '2025-07-19 07:53:18', 'Active', '2025-09-10 20:08:59', '614647212140fd22d82e86ac9286c7a03ad64ef8f0b57c293f45fc592c310889', '2025-08-07 12:10:19', 0, NULL, 0, 'email', NULL, NULL),
(10, '0322-1945@lspu.edu.ph', NULL, '$2y$10$UZ41FK4sn0Ap2DptjCSBzOz.RkbpQVUrLff9GsmOuPnoLfgzTfa52', 'employer', '2025-07-20 07:54:56', 'Active', NULL, NULL, NULL, 0, NULL, 0, 'email', NULL, NULL),
(13, 'admin.lspu1@example.com', NULL, '$2y$10$zvUrlGvtiWr0iqB.7Scc0eFCsO99kyrnvpkCwNFmV1MSu7RWStvrO', 'admin', '2025-07-23 13:31:42', 'Active', '2025-09-10 18:58:04', NULL, NULL, 0, NULL, 0, 'email', NULL, NULL),
(14, 'brightprint24@gmail.com', NULL, '', 'employer', '2025-07-23 14:49:23', 'Active', NULL, NULL, NULL, 0, NULL, 0, 'email', NULL, NULL),
(17, 'ethancyrus25@gmail.com', 'arnaultbrian740@gmail.com', '$2y$10$61Uu2l8eDWvgBE0vH1Cjl..5.aJD17.SeNXDtv3YOwrPvnyfpviuG', 'alumni', '2025-09-01 10:35:24', 'Pending', NULL, NULL, NULL, 1, '2025-09-01 22:37:32', 0, 'email', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_reminder_preferences`
--

CREATE TABLE `user_reminder_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_enabled` tinyint(1) DEFAULT 1,
  `sms_enabled` tinyint(1) DEFAULT 1,
  `frequency_hours` int(11) DEFAULT 24,
  `last_reminder_sent` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `alumni`
--
ALTER TABLE `alumni`
  ADD PRIMARY KEY (`alumni_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `alumni_education`
--
ALTER TABLE `alumni_education`
  ADD PRIMARY KEY (`education_id`),
  ADD KEY `alumni_id` (`alumni_id`);

--
-- Indexes for table `alumni_experience`
--
ALTER TABLE `alumni_experience`
  ADD PRIMARY KEY (`experience_id`),
  ADD KEY `alumni_id` (`alumni_id`);

--
-- Indexes for table `alumni_resume`
--
ALTER TABLE `alumni_resume`
  ADD PRIMARY KEY (`resume_id`),
  ADD KEY `alumni_id` (`alumni_id`);

--
-- Indexes for table `alumni_skill`
--
ALTER TABLE `alumni_skill`
  ADD PRIMARY KEY (`skill_id`),
  ADD KEY `alumni_id` (`alumni_id`);

--
-- Indexes for table `applicant_checklist_progress`
--
ALTER TABLE `applicant_checklist_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `onboarding_id` (`onboarding_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `applicant_onboarding`
--
ALTER TABLE `applicant_onboarding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `checklist_id` (`checklist_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `alumni_id` (`alumni_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `employer`
--
ALTER TABLE `employer`
  ADD PRIMARY KEY (`employer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `interviews`
--
ALTER TABLE `interviews`
  ADD PRIMARY KEY (`interview_id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `alumni_id` (`alumni_id`),
  ADD KEY `employer_id` (`employer_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `employer_id` (`employer_id`);

--
-- Indexes for table `job_match_leaderboard`
--
ALTER TABLE `job_match_leaderboard`
  ADD PRIMARY KEY (`match_id`),
  ADD UNIQUE KEY `unique_alumni_job_match` (`alumni_id`,`job_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `onboarding_checklist`
--
ALTER TABLE `onboarding_checklist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employer_id` (`employer_id`);

--
-- Indexes for table `onboarding_checklist_items`
--
ALTER TABLE `onboarding_checklist_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `checklist_id` (`checklist_id`);

--
-- Indexes for table `onboarding_emails`
--
ALTER TABLE `onboarding_emails`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email_per_application` (`application_id`,`email_type`);

--
-- Indexes for table `reminder_logs`
--
ALTER TABLE `reminder_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reminder_logs_sent_at` (`sent_at`),
  ADD KEY `idx_reminder_logs_status` (`status`),
  ADD KEY `idx_reminder_logs_type` (`type`),
  ADD KEY `idx_reminder_logs_recipient` (`recipient`);

--
-- Indexes for table `reminder_settings`
--
ALTER TABLE `reminder_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `reminder_statistics`
--
ALTER TABLE `reminder_statistics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_date` (`date`),
  ADD KEY `idx_reminder_statistics_date` (`date`);

--
-- Indexes for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `success_stories`
--
ALTER TABLE `success_stories`
  ADD PRIMARY KEY (`story_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_reminder_preferences`
--
ALTER TABLE `user_reminder_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user` (`user_id`),
  ADD KEY `idx_user_reminder_preferences_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrator`
--
ALTER TABLE `administrator`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `alumni`
--
ALTER TABLE `alumni`
  MODIFY `alumni_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `alumni_education`
--
ALTER TABLE `alumni_education`
  MODIFY `education_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `alumni_experience`
--
ALTER TABLE `alumni_experience`
  MODIFY `experience_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `alumni_resume`
--
ALTER TABLE `alumni_resume`
  MODIFY `resume_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `alumni_skill`
--
ALTER TABLE `alumni_skill`
  MODIFY `skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `applicant_checklist_progress`
--
ALTER TABLE `applicant_checklist_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `applicant_onboarding`
--
ALTER TABLE `applicant_onboarding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `employer`
--
ALTER TABLE `employer`
  MODIFY `employer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `interview_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `job_match_leaderboard`
--
ALTER TABLE `job_match_leaderboard`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=215;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `onboarding_checklist`
--
ALTER TABLE `onboarding_checklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `onboarding_checklist_items`
--
ALTER TABLE `onboarding_checklist_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `onboarding_emails`
--
ALTER TABLE `onboarding_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reminder_logs`
--
ALTER TABLE `reminder_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=644;

--
-- AUTO_INCREMENT for table `reminder_settings`
--
ALTER TABLE `reminder_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=301;

--
-- AUTO_INCREMENT for table `reminder_statistics`
--
ALTER TABLE `reminder_statistics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=318;

--
-- AUTO_INCREMENT for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `success_stories`
--
ALTER TABLE `success_stories`
  MODIFY `story_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_reminder_preferences`
--
ALTER TABLE `user_reminder_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `administrator`
--
ALTER TABLE `administrator`
  ADD CONSTRAINT `administrator_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `alumni`
--
ALTER TABLE `alumni`
  ADD CONSTRAINT `alumni_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `alumni_education`
--
ALTER TABLE `alumni_education`
  ADD CONSTRAINT `alumni_education_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `alumni` (`alumni_id`) ON DELETE CASCADE;

--
-- Constraints for table `alumni_experience`
--
ALTER TABLE `alumni_experience`
  ADD CONSTRAINT `alumni_experience_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `alumni` (`alumni_id`) ON DELETE CASCADE;

--
-- Constraints for table `alumni_resume`
--
ALTER TABLE `alumni_resume`
  ADD CONSTRAINT `alumni_resume_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `alumni` (`alumni_id`) ON DELETE CASCADE;

--
-- Constraints for table `alumni_skill`
--
ALTER TABLE `alumni_skill`
  ADD CONSTRAINT `alumni_skill_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `alumni` (`alumni_id`) ON DELETE CASCADE;

--
-- Constraints for table `applicant_checklist_progress`
--
ALTER TABLE `applicant_checklist_progress`
  ADD CONSTRAINT `applicant_checklist_progress_ibfk_1` FOREIGN KEY (`onboarding_id`) REFERENCES `applicant_onboarding` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applicant_checklist_progress_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `onboarding_checklist_items` (`id`);

--
-- Constraints for table `applicant_onboarding`
--
ALTER TABLE `applicant_onboarding`
  ADD CONSTRAINT `applicant_onboarding_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`),
  ADD CONSTRAINT `applicant_onboarding_ibfk_2` FOREIGN KEY (`checklist_id`) REFERENCES `onboarding_checklist` (`id`);

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `alumni` (`alumni_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE;

--
-- Constraints for table `employer`
--
ALTER TABLE `employer`
  ADD CONSTRAINT `employer_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `interviews`
--
ALTER TABLE `interviews`
  ADD CONSTRAINT `interviews_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `interviews_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `interviews_ibfk_3` FOREIGN KEY (`alumni_id`) REFERENCES `alumni` (`alumni_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `interviews_ibfk_4` FOREIGN KEY (`employer_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`employer_id`) REFERENCES `employer` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `job_match_leaderboard`
--
ALTER TABLE `job_match_leaderboard`
  ADD CONSTRAINT `job_match_leaderboard_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `alumni` (`alumni_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_match_leaderboard_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE;

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `onboarding_checklist`
--
ALTER TABLE `onboarding_checklist`
  ADD CONSTRAINT `onboarding_checklist_ibfk_1` FOREIGN KEY (`employer_id`) REFERENCES `employer` (`user_id`);

--
-- Constraints for table `onboarding_checklist_items`
--
ALTER TABLE `onboarding_checklist_items`
  ADD CONSTRAINT `onboarding_checklist_items_ibfk_1` FOREIGN KEY (`checklist_id`) REFERENCES `onboarding_checklist` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `onboarding_emails`
--
ALTER TABLE `onboarding_emails`
  ADD CONSTRAINT `onboarding_emails_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD CONSTRAINT `saved_jobs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `saved_jobs_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`);

--
-- Constraints for table `success_stories`
--
ALTER TABLE `success_stories`
  ADD CONSTRAINT `success_stories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_reminder_preferences`
--
ALTER TABLE `user_reminder_preferences`
  ADD CONSTRAINT `user_reminder_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
