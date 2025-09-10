<?php

require_once '../conn/db_conn.php';
header('Content-Type: application/json');

session_start();

// Check if user is logged in as employer using email
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$email = $_SESSION['email'];

try {
    $db = Database::getInstance()->getConnection();

    // First get employer_id from user email
    $employer_stmt = $db->prepare('
        SELECT e.*
        FROM employer e 
        JOIN user u ON e.user_id = u.user_id 
        WHERE u.email = ?
    ');
    $employer_stmt->bind_param('s', $email);
    $employer_stmt->execute();
    $employer_result = $employer_stmt->get_result();

    if ($employer_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Employer not found']);
        exit;
    }

    $employer = $employer_result->fetch_assoc();
    $employer_id = $employer['user_id'];
    $employer_stmt->close();

    // Get employer's jobs
    $stmt_jobs = $db->prepare('
        SELECT job_id, title 
        FROM jobs 
        WHERE employer_id = ? 
        ORDER BY created_at DESC
    ');
    $stmt_jobs->bind_param('i', $employer_id);
    $stmt_jobs->execute();
    $jobs_result = $stmt_jobs->get_result();
    $jobs = [];
    while ($job = $jobs_result->fetch_assoc()) {
        $jobs[] = $job;
    }
    $stmt_jobs->close();

    // Debug: Check if we have jobs
    error_log("Employer ID: $employer_id, Jobs count: ".count($jobs));

    // Get leaderboard data with alumni details - FIXED THE SQL SYNTAX
    $stmt = $db->prepare('
        SELECT 
            jml.match_id,
            jml.alumni_id,
            jml.job_id,
            jml.match_percentage,
            jml.matched_at,
            jml.notified,
            a.first_name,
            a.last_name,
            a.course,
            a.year_graduated,
            a.contact,
            a.birthdate,
            a.gender,
            a.civil_status,
            a.college,
            a.profile_pic,  -- ADDED: Profile picture field
            j.title as job_title,
            j.type as job_type,
            j.location as job_location,
            j.salary as job_salary,
            e.company_name,
            e.user_id,
            u.email as alumni_email
        FROM job_match_leaderboard jml
        JOIN alumni a ON jml.alumni_id = a.alumni_id
        JOIN user u ON a.user_id = u.user_id
        JOIN jobs j ON jml.job_id = j.job_id
        JOIN employer e ON j.employer_id = e.user_id
        WHERE j.employer_id = ?
        ORDER BY jml.match_percentage DESC, jml.matched_at DESC
    ');

    $stmt->bind_param('i', $employer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $leaderboard = [];
    while ($row = $result->fetch_assoc()) {
        // Get alumni skills with certificate information
        $skills_stmt = $db->prepare('
            SELECT skill_id, name, certificate, certificate_file 
            FROM alumni_skill 
            WHERE alumni_id = ?
        ');
        $skills_stmt->bind_param('i', $row['alumni_id']);
        $skills_stmt->execute();
        $skills_result = $skills_stmt->get_result();
        $skills = [];
        while ($skill = $skills_result->fetch_assoc()) {
            $skills[] = [
                'skill_id' => $skill['skill_id'],
                'name' => $skill['name'],
                'certificate' => $skill['certificate'],
                'certificate_file' => $skill['certificate_file'],
            ];
        }
        $skills_stmt->close();

        // Get work experiences
        $exp_stmt = $db->prepare('
            SELECT experience_id, title, company, start_date, end_date, current, description
            FROM alumni_experience 
            WHERE alumni_id = ?
            ORDER BY start_date DESC
        ');
        $exp_stmt->bind_param('i', $row['alumni_id']);
        $exp_stmt->execute();
        $exp_result = $exp_stmt->get_result();
        $experiences = [];
        while ($exp = $exp_result->fetch_assoc()) {
            $experiences[] = $exp;
        }
        $exp_stmt->close();

        // Get education
        $edu_stmt = $db->prepare('
            SELECT education_id, degree, school, start_date, end_date, current
            FROM alumni_education 
            WHERE alumni_id = ?
            ORDER BY start_date DESC
        ');
        $edu_stmt->bind_param('i', $row['alumni_id']);
        $edu_stmt->execute();
        $edu_result = $edu_stmt->get_result();
        $educations = [];
        while ($edu = $edu_result->fetch_assoc()) {
            $educations[] = $edu;
        }
        $edu_stmt->close();

        // Get resume file if exists
        $resume_stmt = $db->prepare('
            SELECT file_name 
            FROM alumni_resume 
            WHERE alumni_id = ? 
            ORDER BY uploaded_at DESC 
            LIMIT 1
        ');
        $resume_stmt->bind_param('i', $row['alumni_id']);
        $resume_stmt->execute();
        $resume_result = $resume_stmt->get_result();
        $resume_file = $resume_result->fetch_assoc()['file_name'] ?? null;
        $resume_stmt->close();

        // Handle profile picture path
        $profile_picture = null;
        if (!empty($row['profile_pic'])) {
            // Check if the file exists in the uploads/profile_picture folder
            $profile_path = '../uploads/profile_picture/'.$row['profile_pic'];
            if (file_exists($profile_path)) {
                $profile_picture = 'uploads/profile_picture/'.$row['profile_pic'];
            }
        }

        $leaderboard[] = [
            'match_id' => $row['match_id'],
            'alumni_id' => $row['alumni_id'],
            'job_id' => $row['job_id'],
            'match_percentage' => $row['match_percentage'],
            'matched_at' => $row['matched_at'],
            'notified' => $row['notified'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['alumni_email'],
            'course' => $row['course'],
            'year_graduated' => $row['year_graduated'],
            'contact' => $row['contact'],
            'birthdate' => $row['birthdate'],
            'gender' => $row['gender'],
            'civil_status' => $row['civil_status'],
            'college' => $row['college'],
            'job_title' => $row['job_title'],
            'job_type' => $row['job_type'],
            'job_location' => $row['job_location'],
            'job_salary' => $row['job_salary'],
            'company_name' => $row['company_name'],
            'profile_pic' => $profile_picture,
            'skills' => $skills, // Now includes certificate and certificate_file
            'experiences' => $experiences,
            'educations' => $educations,
            'file_name' => $resume_file,
        ];
    }

    $stmt->close();

    // Debug: Check what we found
    error_log('Leaderboard data count: '.count($leaderboard));
    if (count($leaderboard) > 0) {
        error_log('First match skills: '.json_encode($leaderboard[0]['skills']));
    }

    echo json_encode([
        'success' => true,
        'data' => $leaderboard,
        'jobs' => $jobs,
        'count' => count($leaderboard),
    ], JSON_UNESCAPED_SLASHES);
} catch (Exception $e) {
    error_log('Error in get_leaderboard.php: '.$e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching leaderboard data: '.$e->getMessage(),
    ]);
}
