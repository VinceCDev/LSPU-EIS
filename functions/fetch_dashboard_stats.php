<?php
// functions/fetch_dashboard_stats.php
header('Content-Type: application/json');
require_once '../conn/db_conn.php';
$db = Database::getInstance()->getConnection();

// 1. Graduates and Employed per College
$colleges = [];
$college_query = $db->query('SELECT college, COUNT(*) as graduates FROM alumni GROUP BY college');
while ($row = $college_query->fetch_assoc()) {
    $colleges[$row['college']] = [
        'graduates' => (int)$row['graduates'],
        'employed' => 0
    ];
}
// Get employed count per college (alumni with at least one current experience)
$emp_query = $db->query('SELECT a.college, COUNT(DISTINCT a.alumni_id) as employed FROM alumni a JOIN alumni_experience e ON a.alumni_id = e.alumni_id WHERE e.current = 1 GROUP BY a.college');
while ($row = $emp_query->fetch_assoc()) {
    if (isset($colleges[$row['college']])) {
        $colleges[$row['college']]['employed'] = (int)$row['employed'];
    }
}

// 2. Employment Status per Program (Course)
$status_labels = ['Probational', 'Contractual', 'Regular', 'Self-employed', 'Unemployed'];
$programs = [];
$prog_query = $db->query('SELECT course FROM alumni GROUP BY course');
while ($row = $prog_query->fetch_assoc()) {
    $programs[$row['course']] = array_fill_keys($status_labels, 0);
}
$status_query = $db->query('SELECT a.course, e.employment_status, COUNT(*) as cnt FROM alumni a JOIN alumni_experience e ON a.alumni_id = e.alumni_id WHERE e.current = 1 GROUP BY a.course, e.employment_status');
while ($row = $status_query->fetch_assoc()) {
    $course = $row['course'];
    $status = $row['employment_status'];
    if (isset($programs[$course]) && isset($programs[$course][$status])) {
        $programs[$course][$status] = (int)$row['cnt'];
    }
}

// 4. Work Location Distribution (current=1)
$location_dist = ['Local' => 0, 'Abroad' => 0];
$loc_query = $db->query("SELECT location_of_work, COUNT(*) as cnt FROM alumni_experience WHERE current = 1 GROUP BY location_of_work");
while ($row = $loc_query->fetch_assoc()) {
    $loc = $row['location_of_work'] ?: '';
    if (isset($location_dist[$loc])) {
        $location_dist[$loc] = (int)$row['cnt'];
    }
}

// 5. Employment Sector Distribution (current=1)
$sector_dist = ['Government' => 0, 'Private' => 0];
$sector_query = $db->query("SELECT employment_sector, COUNT(*) as cnt FROM alumni_experience WHERE current = 1 GROUP BY employment_sector");
while ($row = $sector_query->fetch_assoc()) {
    $sector = $row['employment_sector'] ?: '';
    if (isset($sector_dist[$sector])) {
        $sector_dist[$sector] = (int)$row['cnt'];
    }
}

// 6. Course breakdown per location
$courses_per_location = [];
$loc_course_query = $db->query("SELECT location_of_work, a.course, COUNT(DISTINCT a.alumni_id) as graduates FROM alumni_experience e JOIN alumni a ON a.alumni_id = e.alumni_id WHERE e.current = 1 GROUP BY location_of_work, a.course");
while ($row = $loc_course_query->fetch_assoc()) {
    $loc = $row['location_of_work'] ?: '';
    $course = $row['course'];
    if (!isset($courses_per_location[$loc])) $courses_per_location[$loc] = [];
    $courses_per_location[$loc][$course] = [
        'graduates' => (int)$row['graduates'],
        'employed' => 0
    ];
}
$loc_emp_query = $db->query("SELECT location_of_work, a.course, COUNT(DISTINCT a.alumni_id) as employed FROM alumni_experience e JOIN alumni a ON a.alumni_id = e.alumni_id WHERE e.current = 1 GROUP BY location_of_work, a.course");
while ($row = $loc_emp_query->fetch_assoc()) {
    $loc = $row['location_of_work'] ?: '';
    $course = $row['course'];
    if (isset($courses_per_location[$loc][$course])) {
        $courses_per_location[$loc][$course]['employed'] = (int)$row['employed'];
    }
}

// 7. Course breakdown per sector
$courses_per_sector = [];
$sec_course_query = $db->query("SELECT employment_sector, a.course, COUNT(DISTINCT a.alumni_id) as graduates FROM alumni_experience e JOIN alumni a ON a.alumni_id = e.alumni_id WHERE e.current = 1 GROUP BY employment_sector, a.course");
while ($row = $sec_course_query->fetch_assoc()) {
    $sec = $row['employment_sector'] ?: '';
    $course = $row['course'];
    if (!isset($courses_per_sector[$sec])) $courses_per_sector[$sec] = [];
    $courses_per_sector[$sec][$course] = [
        'graduates' => (int)$row['graduates'],
        'employed' => 0
    ];
}
$sec_emp_query = $db->query("SELECT employment_sector, a.course, COUNT(DISTINCT a.alumni_id) as employed FROM alumni_experience e JOIN alumni a ON a.alumni_id = e.alumni_id WHERE e.current = 1 GROUP BY employment_sector, a.course");
while ($row = $sec_emp_query->fetch_assoc()) {
    $sec = $row['employment_sector'] ?: '';
    $course = $row['course'];
    if (isset($courses_per_sector[$sec][$course])) {
        $courses_per_sector[$sec][$course]['employed'] = (int)$row['employed'];
    }
}

// Use Gemini API for course-work alignment
function call_gemini_api($prompt) {
    $api_key = 'AIzaSyC5LRDr-q8eDfLMFe_QOA8GmLCzF-Qo6dU';
    $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-goog-api-key: ' . $api_key
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
    error_log('Gemini API unexpected response: ' . $response);
    return '';
}

$alignment_labels = ['Highly Aligned', 'Moderately Aligned', 'Slightly Aligned', 'Not Aligned'];
$course_work_alignment = [];
$align_query = $db->query("SELECT a.course, e.title FROM alumni a JOIN alumni_experience e ON a.alumni_id = e.alumni_id WHERE e.current = 1");
while ($row = $align_query->fetch_assoc()) {
    $course = $row['course'];
    $title = $row['title'];
    if (!isset($course_work_alignment[$course])) {
        $course_work_alignment[$course] = [
            'counts' => array_fill_keys($alignment_labels, 0),
            'percentages' => array_fill_keys($alignment_labels, 0)
        ];
    }
    $prompt = "Given the course: '$course' and the job title: '$title', classify the alignment as one of: Highly Aligned, Moderately Aligned, Slightly Aligned, Not Aligned. Only return the label.";
    $label = call_gemini_api($prompt);
    $label = preg_replace('/[^A-Za-z ]/', '', $label);
    if (in_array($label, $alignment_labels)) {
        $course_work_alignment[$course]['counts'][$label]++;
    } else {
        $course_work_alignment[$course]['counts']['Not Aligned']++;
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
    $total_companies = (int)$row['cnt'];
}
$res = $db->query('SELECT COUNT(*) as cnt FROM alumni');
if ($res && $row = $res->fetch_assoc()) {
    $total_alumni = (int)$row['cnt'];
}

// Companies added yesterday
$companies_yesterday = 0;
$res = $db->query("SELECT COUNT(*) as cnt FROM employer WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY");
if ($res && $row = $res->fetch_assoc()) {
    $companies_yesterday = (int)$row['cnt'];
}
// Alumni added yesterday
$alumni_yesterday = 0;
$res = $db->query("SELECT COUNT(*) as cnt FROM alumni WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY");
if ($res && $row = $res->fetch_assoc()) {
    $alumni_yesterday = (int)$row['cnt'];
}

// Jobs and Applications for dashboard cards
$total_jobs = 0;
$jobs_yesterday = 0;
$res = $db->query("SELECT COUNT(*) as cnt FROM jobs");
if ($res && $row = $res->fetch_assoc()) {
    $total_jobs = (int)$row['cnt'];
}
$res = $db->query("SELECT COUNT(*) as cnt FROM jobs WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY");
if ($res && $row = $res->fetch_assoc()) {
    $jobs_yesterday = (int)$row['cnt'];
}
$total_applications = 0;
$applications_yesterday = 0;
$res = $db->query("SELECT COUNT(*) as cnt FROM applications");
if ($res && $row = $res->fetch_assoc()) {
    $total_applications = (int)$row['cnt'];
}
$res = $db->query("SELECT COUNT(*) as cnt FROM applications WHERE DATE(applied_at) = CURDATE() - INTERVAL 1 DAY");
if ($res && $row = $res->fetch_assoc()) {
    $applications_yesterday = (int)$row['cnt'];
}

// Alumni map data for pins
$alumni_map = [];
$alumni_query = $db->query("SELECT a.alumni_id, a.first_name, a.middle_name, a.last_name, a.profile_pic, a.course, a.year_graduated, a.city, a.province FROM alumni a WHERE a.city IS NOT NULL AND a.city != '' AND a.province IS NOT NULL AND a.province != ''");
while ($row = $alumni_query->fetch_assoc()) {
    $location_key = $row['city'] . ', ' . $row['province'];
    if (!isset($alumni_map[$location_key])) $alumni_map[$location_key] = [];
    $alumni_id = $row['alumni_id'];
    // Get current work experience
    $exp = $db->query("SELECT title, company, start_date, end_date, description FROM alumni_experience WHERE alumni_id = $alumni_id AND current = 1 LIMIT 1");
    $work = '';
    $work_details = null;
    if ($exp && ($exp_row = $exp->fetch_assoc())) {
        $work = $exp_row['title'] . ' at ' . $exp_row['company'];
        $work_details = [
            'title' => $exp_row['title'],
            'company' => $exp_row['company'],
            'start_date' => $exp_row['start_date'],
            'end_date' => $exp_row['end_date'],
            'description' => $exp_row['description']
        ];
    }
    $employed = $exp && $exp->num_rows > 0 ? 'Employed' : 'Unemployed';
    $profile_pic = $row['profile_pic'] ? 'uploads/profile_picture/' . $row['profile_pic'] : null;
    $alumni_map[$location_key][] = [
        'name' => trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']),
        'profile_pic' => $profile_pic,
        'course' => $row['course'],
        'year_graduated' => $row['year_graduated'],
        'work' => $work,
        'work_details' => $work_details,
        'status' => $employed
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
    'alumni_map' => $alumni_map
];
echo json_encode($response); 