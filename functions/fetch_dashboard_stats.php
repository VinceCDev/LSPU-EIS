<?php

// functions/fetch_dashboard_stats.php
header('Content-Type: application/json');
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();

// College abbreviation mapping for LSPU San Pablo
$collegeAbbreviations = [
    'College of Computer Studies' => 'CCS',
    'College of Business Administration and Accountancy' => 'CBAA',
    'College of Arts and Sciences' => 'CAS',
    'College of Teacher Education' => 'CTE',
    'College of Engineering' => 'COE',
    'College of Agriculture' => 'CA',
    'College of Criminal Justice Education' => 'CCJE',
    'College of Industrial Technology' => 'CIT',
    'College of Hospitality Management and Tourism' => 'CHMT'
];

// Course abbreviation mapping
$courseAbbreviations = [
    // College of Arts and Sciences
    'BS Biology' => 'BS Bio',
    'BS Psychology' => 'BS Psych',
    
    // College of Business Administration and Accountancy
    'BS Office Administration' => 'BS OA',
    'BS Business Administration Major in Financial Management' => 'BSBA-FM',
    'BS Business Administration Major in Marketing Management' => 'BSBA-MM',
    'BS Accountancy' => 'BSA',
    
    // College of Computer Studies
    'BS Information Technology' => 'BSIT',
    'BS Computer Science' => 'BSCS',
    
    // College of Criminal Justice Education
    'BS Criminology' => 'BSCrim',
    
    // College of Engineering
    'BS Electronics Engineering' => 'BS ECE',
    'BS Electrical Engineering' => 'BS EE',
    'BS Computer Engineering' => 'BS CoE',
    
    // College of Hospitality Management and Tourism
    'BS Hospitality Management' => 'BSHM',
    'BS Tourism Management' => 'BSTM',
    
    // College of Industrial Technology
    'BS Industrial Technology Major in Automotive Technology' => 'BSIT-AT',
    'BS Industrial Technology Major in Architectural Drafting' => 'BSIT-AD',
    'BS Industrial Technology Major in Electrical Technology' => 'BSIT-ET',
    'BS Industrial Technology Major in Electronics Technology' => 'BSIT-ELXT',
    'BS Industrial Technology Major in Food & Beverage Preparation and Service Management Technology' => 'BSIT-FBPSMT',
    'BS Industrial Technology Major in Heating, Ventilating, Air-Conditioning & Refrigeration Technology' => 'BSIT-HVACRT',
    
    // College of Teacher Education
    'BS Elementary Education' => 'BEED',
    'BS Physical Education' => 'BPE',
    'BS Secondary Education Major in English' => 'BSED-English',
    'BS Secondary Education Major in Filipino' => 'BSED-Filipino',
    'BS Secondary Education Major in Mathematics' => 'BSED-Math',
    'BS Secondary Education Major in Science' => 'BSED-Science',
    'BS Secondary Education Major in Social Studies' => 'BSED-Social Studies',
    'BS Technology and Livelihood Education Major in Home Economics' => 'BS TLE-HE',
    'BS Technical-Vocational Teacher Education Major in Electrical Technology' => 'BS TVTED-ET',
    'BS Technical-Vocational Teacher Education Major in Electronics Technology' => 'BS TVTED-ELXT',
    'BS Technical-Vocational Teacher Education Major in Food & Service Management' => 'BS TVTED-FSM',
    'BS Technical-Vocational Teacher Education Major in Garments, Fashion & Design' => 'BS TVTED-GFD'
];

// Function to abbreviate college name
function abbreviateCollege($collegeName, $abbreviations) {
    return $abbreviations[$collegeName] ?? $collegeName;
}

// Function to abbreviate course name
function abbreviateCourse($courseName, $abbreviations) {
    return $abbreviations[$courseName] ?? $courseName;
}

// Function to check if alumni is employed based on experience records
function isAlumniEmployed($alumni_id, $db) {
    // Check if alumni has any current experience that hasn't ended
    $current_date = date('Y-m-d');
    $stmt = $db->prepare("SELECT COUNT(*) as employed_count 
                         FROM alumni_experience 
                         WHERE alumni_id = ? 
                         AND (current = 1 OR (end_date IS NULL OR end_date >= ?))");
    $stmt->bind_param('is', $alumni_id, $current_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return ($row['employed_count'] > 0);
}

// 1. Graduates and Employed per College
$colleges = [];
$college_query = $db->query('SELECT college, COUNT(*) as graduates FROM alumni GROUP BY college');
while ($row = $college_query->fetch_assoc()) {
    $abbreviatedCollege = abbreviateCollege($row['college'], $collegeAbbreviations);
    $colleges[$abbreviatedCollege] = [
        'graduates' => (int) $row['graduates'],
        'employed' => 0,
    ];
}

// Get employed count per college (alumni with valid current employment)
$all_alumni = $db->query('SELECT alumni_id, college FROM alumni');
while ($alumni_row = $all_alumni->fetch_assoc()) {
    $abbreviatedCollege = abbreviateCollege($alumni_row['college'], $collegeAbbreviations);
    if (isAlumniEmployed($alumni_row['alumni_id'], $db)) {
        $colleges[$abbreviatedCollege]['employed']++;
    }
}

// 2. Employment Status per Program (Course)
$status_labels = ['Probational', 'Contractual', 'Regular', 'Self-employed', 'Unemployed'];
$programs = [];
$prog_query = $db->query('SELECT course FROM alumni GROUP BY course');
while ($row = $prog_query->fetch_assoc()) {
    $abbreviatedCourse = abbreviateCourse($row['course'], $courseAbbreviations);
    $programs[$abbreviatedCourse] = array_fill_keys($status_labels, 0);
}

// Count employment status for currently employed alumni
$current_date = date('Y-m-d');
$status_query = $db->query("SELECT a.course, e.employment_status, COUNT(DISTINCT a.alumni_id) as cnt 
                           FROM alumni a 
                           JOIN alumni_experience e ON a.alumni_id = e.alumni_id 
                           WHERE (e.current = 1 OR (e.end_date IS NULL OR e.end_date >= '$current_date'))
                           GROUP BY a.course, e.employment_status");
while ($row = $status_query->fetch_assoc()) {
    $abbreviatedCourse = abbreviateCourse($row['course'], $courseAbbreviations);
    $status = $row['employment_status'];
    if (isset($programs[$abbreviatedCourse]) && isset($programs[$abbreviatedCourse][$status])) {
        $programs[$abbreviatedCourse][$status] = (int) $row['cnt'];
    }
}

// Count unemployed alumni per course
$unemployed_query = $db->query('SELECT a.course, COUNT(*) as cnt 
                               FROM alumni a 
                               WHERE a.alumni_id NOT IN (
                                   SELECT DISTINCT alumni_id 
                                   FROM alumni_experience 
                                   WHERE current = 1 OR (end_date IS NULL OR end_date >= CURDATE())
                               )
                               GROUP BY a.course');
while ($row = $unemployed_query->fetch_assoc()) {
    $abbreviatedCourse = abbreviateCourse($row['course'], $courseAbbreviations);
    if (isset($programs[$abbreviatedCourse])) {
        $programs[$abbreviatedCourse]['Unemployed'] = (int) $row['cnt'];
    }
}

// 4. Work Location Distribution (currently employed)
$location_dist = ['Local' => 0, 'Abroad' => 0];
$loc_query = $db->query("SELECT location_of_work, COUNT(DISTINCT alumni_id) as cnt 
                        FROM alumni_experience 
                        WHERE current = 1 OR (end_date IS NULL OR end_date >= CURDATE())
                        GROUP BY location_of_work");
while ($row = $loc_query->fetch_assoc()) {
    $loc = $row['location_of_work'] ?: '';
    if (isset($location_dist[$loc])) {
        $location_dist[$loc] = (int) $row['cnt'];
    }
}

// 5. Employment Sector Distribution (currently employed)
$sector_dist = ['Government' => 0, 'Private' => 0];
$sector_query = $db->query("SELECT employment_sector, COUNT(DISTINCT alumni_id) as cnt 
                           FROM alumni_experience 
                           WHERE current = 1 OR (end_date IS NULL OR end_date >= CURDATE())
                           GROUP BY employment_sector");
while ($row = $sector_query->fetch_assoc()) {
    $sector = $row['employment_sector'] ?: '';
    if (isset($sector_dist[$sector])) {
        $sector_dist[$sector] = (int) $row['cnt'];
    }
}

// 6. Course breakdown per location
$courses_per_location = [];
$loc_course_query = $db->query('SELECT location_of_work, a.course, COUNT(DISTINCT a.alumni_id) as graduates FROM alumni_experience e JOIN alumni a ON a.alumni_id = e.alumni_id WHERE e.current = 1 GROUP BY location_of_work, a.course');
while ($row = $loc_course_query->fetch_assoc()) {
    $loc = $row['location_of_work'] ?: '';
    $course = $row['course'];
    if (!isset($courses_per_location[$loc])) {
        $courses_per_location[$loc] = [];
    }
    $courses_per_location[$loc][$course] = [
        'graduates' => (int) $row['graduates'],
        'employed' => 0,
    ];
}
$loc_emp_query = $db->query('SELECT location_of_work, a.course, COUNT(DISTINCT a.alumni_id) as employed FROM alumni_experience e JOIN alumni a ON a.alumni_id = e.alumni_id WHERE e.current = 1 GROUP BY location_of_work, a.course');
while ($row = $loc_emp_query->fetch_assoc()) {
    $loc = $row['location_of_work'] ?: '';
    $course = $row['course'];
    if (isset($courses_per_location[$loc][$course])) {
        $courses_per_location[$loc][$course]['employed'] = (int) $row['employed'];
    }
}

// 7. Course breakdown per sector
$courses_per_sector = [];
$sec_course_query = $db->query('SELECT employment_sector, a.course, COUNT(DISTINCT a.alumni_id) as graduates FROM alumni_experience e JOIN alumni a ON a.alumni_id = e.alumni_id WHERE e.current = 1 GROUP BY employment_sector, a.course');
while ($row = $sec_course_query->fetch_assoc()) {
    $sec = $row['employment_sector'] ?: '';
    $course = $row['course'];
    if (!isset($courses_per_sector[$sec])) {
        $courses_per_sector[$sec] = [];
    }
    $courses_per_sector[$sec][$course] = [
        'graduates' => (int) $row['graduates'],
        'employed' => 0,
    ];
}
$sec_emp_query = $db->query('SELECT employment_sector, a.course, COUNT(DISTINCT a.alumni_id) as employed FROM alumni_experience e JOIN alumni a ON a.alumni_id = e.alumni_id WHERE e.current = 1 GROUP BY employment_sector, a.course');
while ($row = $sec_emp_query->fetch_assoc()) {
    $sec = $row['employment_sector'] ?: '';
    $course = $row['course'];
    if (isset($courses_per_sector[$sec][$course])) {
        $courses_per_sector[$sec][$course]['employed'] = (int) $row['employed'];
    }
}

// Use Gemini API for course-work alignment
function call_gemini_api($prompt)
{
    $api_key = 'AIzaSyC5LRDr-q8eDfLMFe_QOA8GmLCzF-Qo6dU';
    $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt],
                ],
            ],
        ],
    ];
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-goog-api-key: '.$api_key,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);
    if (!$response) {
        error_log('Gemini API call failed: No response');

        return '';
    }
    $json = json_decode($response, true);
    if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
        $text = trim($json['candidates'][0]['content']['parts'][0]['text']);

        return $text;
    }
    error_log('Gemini API unexpected response: '.$response);

    return '';
}

$alignment_labels = ['Highly Aligned', 'Moderately Aligned', 'Slightly Aligned', 'Not Aligned'];
$course_work_alignment = [];
$align_query = $db->query('SELECT a.course, e.title FROM alumni a JOIN alumni_experience e ON a.alumni_id = e.alumni_id WHERE e.current = 1');
while ($row = $align_query->fetch_assoc()) {
    $course = $row['course'];
    $title = $row['title'];
    if (!isset($course_work_alignment[$course])) {
        $course_work_alignment[$course] = [
            'counts' => array_fill_keys($alignment_labels, 0),
            'percentages' => array_fill_keys($alignment_labels, 0),
        ];
    }
    $prompt = "Given the course: '$course' and the job title: '$title', classify the alignment as one of: Highly Aligned, Moderately Aligned, Slightly Aligned, Not Aligned. Only return the label.";
    $label = call_gemini_api($prompt);
    $label = preg_replace('/[^A-Za-z ]/', '', $label);
    if (in_array($label, $alignment_labels)) {
        ++$course_work_alignment[$course]['counts'][$label];
    } else {
        ++$course_work_alignment[$course]['counts']['Not Aligned'];
    }
}
// Calculate percentages
foreach ($course_work_alignment as $course => &$data) {
    $total = array_sum($data['counts']);
    if ($total > 0) {
        foreach ($alignment_labels as $label) {
            $data['percentages'][$label] = round(($data['counts'][$label] / $total) * 100, 2);
        }
    }
}
unset($data);

// Add total companies and alumni for dashboard cards
$total_companies = 0;
$total_alumni = 0;
$res = $db->query('SELECT COUNT(*) as cnt FROM employer');
if ($res && $row = $res->fetch_assoc()) {
    $total_companies = (int) $row['cnt'];
}
$res = $db->query('SELECT COUNT(*) as cnt FROM alumni');
if ($res && $row = $res->fetch_assoc()) {
    $total_alumni = (int) $row['cnt'];
}

// Companies added yesterday
$companies_yesterday = 0;
$res = $db->query('SELECT COUNT(*) as cnt FROM employer WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY');
if ($res && $row = $res->fetch_assoc()) {
    $companies_yesterday = (int) $row['cnt'];
}
// Alumni added yesterday
$alumni_yesterday = 0;
$res = $db->query('SELECT COUNT(*) as cnt FROM alumni WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY');
if ($res && $row = $res->fetch_assoc()) {
    $alumni_yesterday = (int) $row['cnt'];
}

// Jobs and Applications for dashboard cards
$total_jobs = 0;
$jobs_yesterday = 0;
$res = $db->query('SELECT COUNT(*) as cnt FROM jobs');
if ($res && $row = $res->fetch_assoc()) {
    $total_jobs = (int) $row['cnt'];
}
$res = $db->query('SELECT COUNT(*) as cnt FROM jobs WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY');
if ($res && $row = $res->fetch_assoc()) {
    $jobs_yesterday = (int) $row['cnt'];
}
$total_applications = 0;
$applications_yesterday = 0;
$res = $db->query('SELECT COUNT(*) as cnt FROM applications');
if ($res && $row = $res->fetch_assoc()) {
    $total_applications = (int) $row['cnt'];
}
$res = $db->query('SELECT COUNT(*) as cnt FROM applications WHERE DATE(applied_at) = CURDATE() - INTERVAL 1 DAY');
if ($res && $row = $res->fetch_assoc()) {
    $applications_yesterday = (int) $row['cnt'];
}

// Alumni map data for pins
$alumni_map = [];
$alumni_query = $db->query("SELECT a.alumni_id, a.first_name, a.middle_name, a.last_name, a.profile_pic, a.course, a.year_graduated, a.city, a.province, a.college FROM alumni a WHERE a.city IS NOT NULL AND a.city != '' AND a.province IS NOT NULL AND a.province != ''");
while ($row = $alumni_query->fetch_assoc()) {
    $location_key = $row['city'].', '.$row['province'];
    if (!isset($alumni_map[$location_key])) {
        $alumni_map[$location_key] = [];
    }
    $alumni_id = $row['alumni_id'];
    
    // Get current work experience (not ended)
    $current_date = date('Y-m-d');
    $exp = $db->query("SELECT title, company, start_date, end_date, description, employment_status, location_of_work 
                      FROM alumni_experience 
                      WHERE alumni_id = $alumni_id 
                      AND (current = 1 OR (end_date IS NULL OR end_date >= '$current_date'))
                      ORDER BY start_date DESC 
                      LIMIT 1");
    
    $work = '';
    $work_details = null;
    $employment_status = 'Unemployed';
    
    if ($exp && ($exp_row = $exp->fetch_assoc())) {
        $work = $exp_row['title'].' at '.$exp_row['company'];
        $work_details = [
            'title' => $exp_row['title'],
            'company' => $exp_row['company'],
            'start_date' => $exp_row['start_date'],
            'end_date' => $exp_row['end_date'],
            'description' => $exp_row['description'],
            'employment_status' => $exp_row['employment_status'],
            'location_of_work' => $exp_row['location_of_work']
        ];
        $employment_status = 'Employed';
    }
    
    $profile_pic = $row['profile_pic'] ? 'uploads/profile_picture/'.$row['profile_pic'] : null;
    
    $alumni_map[$location_key][] = [
        'name' => trim($row['first_name'].' '.$row['middle_name'].' '.$row['last_name']),
        'profile_pic' => $profile_pic,
        'course' => $row['course'], // Use full course name
        'college' => $row['college'], // Use full college name
        'year_graduated' => $row['year_graduated'],
        'work' => $work,
        'work_details' => $work_details,
        'status' => $employment_status,
    ];
}

// Output
$response = [
    'graduates_per_college' => $colleges,
    'employment_status_per_program' => $programs,
    'work_location_distribution' => $location_dist,
    'employment_sector_distribution' => $sector_dist,
    'courses_per_location' => $courses_per_location,
    'courses_per_sector' => $courses_per_sector,
    'course_work_alignment' => $course_work_alignment,
    'total_alumni' => $total_alumni,
    'total_companies' => $total_companies,
    'companies_yesterday' => $companies_yesterday,
    'alumni_yesterday' => $alumni_yesterday,
    'total_jobs' => $total_jobs,
    'jobs_yesterday' => $jobs_yesterday,
    'total_applications' => $total_applications,
    'applications_yesterday' => $applications_yesterday,
    'alumni_map' => $alumni_map,
];
echo json_encode($response);
