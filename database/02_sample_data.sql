-- LSPU EIS Sample Data
-- Insert initial data for testing and demonstration
-- Run this after creating the main schema

USE lspu_eis;

-- Insert default admin user
-- Password: admin123 (hashed with PASSWORD_DEFAULT)
INSERT INTO `user` (`email`, `password`, `user_role`, `status`) VALUES
('admin@lspu.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Active')
ON DUPLICATE KEY UPDATE `status` = 'Active';

-- Insert admin profile
INSERT INTO `administrator` (`user_id`, `first_name`, `middle_name`, `last_name`, `gender`, `contact`, `position`, `department`, `status`) 
SELECT u.user_id, 'System', 'Admin', 'Administrator', 'Male', '+639123456789', 'System Administrator', 'IT Department', 'Active'
FROM `user` u WHERE u.email = 'admin@lspu.edu.ph' AND u.user_role = 'admin'
ON DUPLICATE KEY UPDATE `status` = 'Active';

-- Insert sample colleges and courses
-- This data can be used for dropdowns in the application
INSERT INTO `skills` (`name`) VALUES
('JavaScript'), ('PHP'), ('MySQL'), ('HTML/CSS'), ('React'), ('Node.js'), ('Python'), ('Java'), ('C++'), ('C#'),
('Adobe Photoshop'), ('Adobe Illustrator'), ('Microsoft Office'), ('Google Workspace'), ('Project Management'),
('Digital Marketing'), ('Content Writing'), ('Graphic Design'), ('Web Design'), ('Mobile App Development'),
('Data Analysis'), ('Machine Learning'), ('Artificial Intelligence'), ('Cybersecurity'), ('Network Administration'),
('Database Administration'), ('System Administration'), ('Cloud Computing'), ('DevOps'), ('Agile Methodology')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert sample job types for reference
-- These can be used as default values or reference data
INSERT INTO `employer` (`user_id`, `company_name`, `industry_type`, `accreditation_status`) VALUES
(1, 'Sample Company 1', 'Technology', 'Accredited'),
(1, 'Sample Company 2', 'Healthcare', 'Pending'),
(1, 'Sample Company 3', 'Education', 'Accredited')
ON DUPLICATE KEY UPDATE `accreditation_status` = VALUES(`accreditation_status`);

-- Insert sample jobs (if you have employer data)
-- Note: This requires valid employer_id values
-- INSERT INTO `jobs` (`employer_id`, `title`, `type`, `location`, `salary`, `description`, `requirements`) VALUES
-- (1, 'Software Developer', 'Full-time', 'Manila', '25000-35000', 'We are looking for a skilled software developer...', 'Bachelor\'s degree in Computer Science or related field...'),
-- (1, 'Web Designer', 'Part-time', 'Remote', '15000-25000', 'Creative web designer needed for our growing team...', 'Experience with HTML, CSS, JavaScript...'),
-- (2, 'Data Analyst', 'Full-time', 'Quezon City', '30000-40000', 'Join our data team to help drive business decisions...', 'Strong analytical skills, experience with SQL...');

-- Insert sample messages for testing
INSERT INTO `messages` (`sender_email`, `receiver_email`, `subject`, `message`, `role`) VALUES
('admin@lspu.edu.ph', 'alumni@example.com', 'Welcome to LSPU EIS', 'Welcome to the LSPU Employment and Information System!', 'admin'),
('admin@lspu.edu.ph', 'employer@example.com', 'Account Verification', 'Your employer account has been verified successfully.', 'admin');

-- Insert sample notifications
INSERT INTO `notifications` (`user_id`, `type`, `message`, `details`) 
SELECT u.user_id, 'welcome', 'Welcome to LSPU EIS! Your account has been created successfully.', '{"action": "view_profile"}'
FROM `user` u WHERE u.email = 'admin@lspu.edu.ph'
ON DUPLICATE KEY UPDATE `message` = VALUES(`message`);

-- Create a view for dashboard statistics
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

-- Create a view for employment statistics by college
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

-- Create a view for employment statistics by course
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

-- Create a view for recent activities
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