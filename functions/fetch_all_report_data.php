<?php
session_start();
require_once '../conn/db_conn.php';

// Check if user is authenticated and is admin
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$db = Database::getInstance()->getConnection();

// Gemini API function
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
        return 'Not Aligned';
    }
    $json = json_decode($response, true);
    if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
        $text = trim($json['candidates'][0]['content']['parts'][0]['text']);
        return $text;
    }
    return 'Not Aligned';
}

try {
    // 1. EMPLOYMENT SUMMARY DATA
    // Get detailed employment data to calculate accurate job related counts
    $sql = "SELECT 
                a.course,
                a.college,
                e.title as job_title,
                e.employment_status
            FROM alumni a 
            LEFT JOIN alumni_experience e ON a.alumni_id = e.alumni_id AND e.current = 1
            ORDER BY a.college, a.course";
    $result = $db->query($sql);
    $employment_data = [];
    while ($row = $result->fetch_assoc()) {
        $employment_data[] = $row;
    }

    // Process data to calculate accurate statistics grouped by college and course
    $employment_summary = [];
    $college_stats = [];

    foreach ($employment_data as $row) {
        $course = $row['course'];
        $college = $row['college'];
        
        if (!isset($college_stats[$college])) {
            $college_stats[$college] = [];
        }
        
        if (!isset($college_stats[$college][$course])) {
            $college_stats[$college][$course] = [
                'course' => $course,
                'college' => $college,
                'total_graduates' => 0,
                'employed_count' => 0,
                'related_job_count' => 0
            ];
        }
        
        $college_stats[$college][$course]['total_graduates']++;
        
        if ($row['employment_status'] === 'Employed' || $row['employment_status'] === 'Probational' || $row['employment_status'] === 'Regular') {
            $college_stats[$college][$course]['employed_count']++;
            
            // Use Gemini API to determine if job is related to course
            if (!empty($row['job_title'])) {
                $prompt = "Given the course: '{$row['course']}' and the job title: '{$row['job_title']}', classify the alignment as one of: Highly Aligned, Moderately Aligned, Slightly Aligned, Not Aligned. Only return the label.";
                $alignment = call_gemini_api($prompt);
                if (strpos($alignment, 'Aligned') !== false && strpos($alignment, 'Not') === false) {
                    $college_stats[$college][$course]['related_job_count']++;
                }
            }
        }
    }

    // Format data exactly like the image - with colleges as headers and courses below
    $college_keys = array_keys($college_stats);
    $last_college_index = count($college_keys) - 1;
    
    foreach ($college_stats as $college => $courses) {
        $index = array_search($college, $college_keys);
        
        // Add college header row
        $employment_summary[] = [
            'college' => $college,
            'course' => '',
            'total_graduates' => '',
            'employed_count' => '',
            'employment_rate' => '',
            'related_job_count' => '',
            'match_rate' => '',
            'is_header' => true
        ];
        
        // Add course rows for this college
        foreach ($courses as $course_data) {
            $employment_rate = $course_data['total_graduates'] > 0 ? 
                round(($course_data['employed_count'] / $course_data['total_graduates']) * 100, 2) : 0;
            
            $match_rate = $course_data['employed_count'] > 0 ? 
                round(($course_data['related_job_count'] / $course_data['employed_count']) * 100, 2) : 0;
            
            $employment_summary[] = [
                'college' => '',
                'course' => $course_data['course'],
                'total_graduates' => $course_data['total_graduates'],
                'employed_count' => $course_data['employed_count'],
                'employment_rate' => $employment_rate,
                'related_job_count' => $course_data['related_job_count'],
                'match_rate' => $match_rate,
                'is_header' => false
            ];
        }
        
        // Add empty row after each college (except the last one)
        if ($index < $last_college_index) {
            $employment_summary[] = [
                'college' => '',
                'course' => '',
                'total_graduates' => '',
                'employed_count' => '',
                'employment_rate' => '',
                'related_job_count' => '',
                'match_rate' => '',
                'is_header' => false
            ];
        }
    }

    // 2. EMPLOYMENT SECTOR SUMMARY (Like admin_dashboard)
    $sql = "SELECT 
                a.course,
                a.college,
                e.employment_sector,
                COUNT(*) as count
            FROM alumni a 
            LEFT JOIN alumni_experience e ON a.alumni_id = e.alumni_id AND e.current = 1
            WHERE (e.employment_status = 'Employed' OR e.employment_status = 'Probational' OR e.employment_status = 'Regular')
            GROUP BY a.course, a.college, e.employment_sector
            ORDER BY a.college, a.course, e.employment_sector";
    $result = $db->query($sql);
    $sector_data = [];
    while ($row = $result->fetch_assoc()) {
        $sector_data[] = $row;
    }

    // Process sector data by college and course
    $employment_sector_summary = [];
    $college_sector_stats = [];
    
    foreach ($sector_data as $row) {
        $college = $row['college'];
        $course = $row['course'];
        $sector = $row['employment_sector'] ?: 'Unknown';
        $count = $row['count'];
        
        if (!isset($college_sector_stats[$college])) {
            $college_sector_stats[$college] = [];
        }
        if (!isset($college_sector_stats[$college][$course])) {
            $college_sector_stats[$college][$course] = [];
        }
        $college_sector_stats[$college][$course][$sector] = $count;
    }
    
    // Format sector data by college
    foreach ($college_sector_stats as $college => $courses) {
        // Add college header
        $employment_sector_summary[] = [
            'College' => $college,
            'Course' => '',
            'Government' => '',
            'Private' => '',
            'Self-employed' => '',
            'Others' => ''
        ];
        
        foreach ($courses as $course => $sectors) {
            $employment_sector_summary[] = [
                'College' => '',
                'Course' => $course,
                'Government' => $sectors['Government'] ?? 0,
                'Private' => $sectors['Private'] ?? 0,
                'Self-employed' => $sectors['Self-employed'] ?? 0,
                'Others' => $sectors['Others'] ?? 0
            ];
        }
        
        // Add empty row after college
        $employment_sector_summary[] = [
            'College' => '',
            'Course' => '',
            'Government' => '',
            'Private' => '',
            'Self-employed' => '',
            'Others' => ''
        ];
    }

    // 3. LOCATION OF EMPLOYMENT SUMMARY
    $sql = "SELECT 
                a.course,
                a.college,
                e.location_of_work,
                COUNT(*) as count
            FROM alumni a 
            LEFT JOIN alumni_experience e ON a.alumni_id = e.alumni_id AND e.current = 1
            WHERE (e.employment_status = 'Employed' OR e.employment_status = 'Probational' OR e.employment_status = 'Regular')
            GROUP BY a.course, a.college, e.location_of_work
            ORDER BY a.college, a.course, e.location_of_work";
    $result = $db->query($sql);
    $location_data = [];
    while ($row = $result->fetch_assoc()) {
        $location_data[] = $row;
    }

    // Process location data by college and course
    $location_summary = [];
    $college_location_stats = [];
    
    foreach ($location_data as $row) {
        $college = $row['college'];
        $course = $row['course'];
        $location = $row['location_of_work'] ?: 'Unknown';
        $count = $row['count'];
        
        if (!isset($college_location_stats[$college])) {
            $college_location_stats[$college] = [];
        }
        if (!isset($college_location_stats[$college][$course])) {
            $college_location_stats[$college][$course] = [];
        }
        $college_location_stats[$college][$course][$location] = $count;
    }
    
    // Format location data by college
    foreach ($college_location_stats as $college => $courses) {
        // Add college header
        $location_summary[] = [
            'College' => $college,
            'Course' => '',
            'Local' => '',
            'Abroad' => '',
            'Others' => ''
        ];
        
        foreach ($courses as $course => $locations) {
            $location_summary[] = [
                'College' => '',
                'Course' => $course,
                'Local' => $locations['Local'] ?? 0,
                'Abroad' => $locations['Abroad'] ?? 0,
                'Others' => $locations['Others'] ?? 0
            ];
        }
        
        // Add empty row after college
        $location_summary[] = [
            'College' => '',
            'Course' => '',
            'Local' => '',
            'Abroad' => '',
            'Others' => ''
        ];
    }

    // 4. EMPLOYMENT STATUS SUMMARY
    $sql = "SELECT 
                a.course,
                a.college,
                e.employment_status,
                COUNT(*) as count
            FROM alumni a 
            LEFT JOIN alumni_experience e ON a.alumni_id = e.alumni_id AND e.current = 1
            WHERE (e.employment_status = 'Employed' OR e.employment_status = 'Probational' OR e.employment_status = 'Regular' OR e.employment_status = 'Contractual')
            GROUP BY a.course, a.college, e.employment_status
            ORDER BY a.college, a.course, e.employment_status";
    $result = $db->query($sql);
    $employment_status_data = [];
    while ($row = $result->fetch_assoc()) {
        $employment_status_data[] = $row;
    }

    // Process employment status data by college and course
    $employment_status_summary = [];
    $college_status_stats = [];
    
    foreach ($employment_status_data as $row) {
        $college = $row['college'];
        $course = $row['course'];
        $status = $row['employment_status'] ?: 'Unknown';
        $count = $row['count'];
        
        if (!isset($college_status_stats[$college])) {
            $college_status_stats[$college] = [];
        }
        if (!isset($college_status_stats[$college][$course])) {
            $college_status_stats[$college][$course] = [];
        }
        $college_status_stats[$college][$course][$status] = $count;
    }
    
    // Format employment status data by college
    foreach ($college_status_stats as $college => $courses) {
        // Add college header
        $employment_status_summary[] = [
            'College' => $college,
            'Course' => '',
            'Regular' => '',
            'Probational' => '',
            'Contractual' => '',
            'Others' => ''
        ];
        
        foreach ($courses as $course => $statuses) {
            $employment_status_summary[] = [
                'College' => '',
                'Course' => $course,
                'Regular' => $statuses['Regular'] ?? 0,
                'Probational' => $statuses['Probational'] ?? 0,
                'Contractual' => $statuses['Contractual'] ?? 0,
                'Others' => $statuses['Others'] ?? 0
            ];
        }
        
        // Add empty row after college
        $employment_status_summary[] = [
            'College' => '',
            'Course' => '',
            'Regular' => '',
            'Probational' => '',
            'Contractual' => '',
            'Others' => ''
        ];
    }

    // 6. INDUSTRY DETERMINATION USING GEMINI API
    $sql = "SELECT 
                a.course,
                a.college,
                a.gender,
                e.title as job_title,
                e.company
            FROM alumni a 
            LEFT JOIN alumni_experience e ON a.alumni_id = e.alumni_id AND e.current = 1
            WHERE e.title IS NOT NULL AND e.title != ''
            ORDER BY a.college, a.course, a.gender";
    $result = $db->query($sql);
    $industry_determination_raw = [];
    while ($row = $result->fetch_assoc()) {
        $industry_determination_raw[] = $row;
    }

    // Process industry determination with gender counts
    $industry_determination = [];
    $college_industry_stats = [];
    
    foreach ($industry_determination_raw as $row) {
        $college = $row['college'];
        $course = $row['course'];
        $gender = $row['gender'];
        
        // Use Gemini API to determine industry
        $determined_industry = '';
        if (!empty($row['job_title']) || !empty($row['company'])) {
            $prompt = "Given the job title: '{$row['job_title']}' and company: '{$row['company']}', determine the industry sector. Choose from: Technology/IT, Healthcare, Finance/Banking, Education, Manufacturing, Retail, Government, Non-profit, Hospitality/Tourism, Transportation, Construction, Media/Entertainment, Energy, Agriculture, Other. Only return the industry name.";
            $determined_industry = call_gemini_api($prompt);
        }
        
        if (!isset($college_industry_stats[$college])) {
            $college_industry_stats[$college] = [];
        }
        if (!isset($college_industry_stats[$college][$course])) {
            $college_industry_stats[$college][$course] = [
                'Male' => 0,
                'Female' => 0,
                'industries' => []
            ];
        }
        
        $college_industry_stats[$college][$course][$gender]++;
        if (!empty($determined_industry)) {
            $college_industry_stats[$college][$course]['industries'][] = $determined_industry;
        }
    }
    
    // Format industry data by college
    foreach ($college_industry_stats as $college => $courses) {
        // Add college header
        $industry_determination[] = [
            'College' => $college,
            'Course' => '',
            'Male' => '',
            'Female' => '',
            'Total' => '',
            'Industries' => ''
        ];
        
        foreach ($courses as $course => $data) {
            $total = $data['Male'] + $data['Female'];
            $industries = array_unique($data['industries']);
            $industry_list = implode(', ', $industries);
            
            $industry_determination[] = [
                'College' => '',
                'Course' => $course,
                'Male' => $data['Male'],
                'Female' => $data['Female'],
                'Total' => $total,
                'Industries' => $industry_list
            ];
        }
        
        // Add empty row after college
        $industry_determination[] = [
            'College' => '',
            'Course' => '',
            'Male' => '',
            'Female' => '',
            'Total' => '',
            'Industries' => ''
        ];
    }

    // 5. DETAILED EMPLOYMENT DATA (Organized by College and Course)
    $sql = "SELECT 
                a.alumni_id,
                a.user_id,
                a.first_name,
                a.middle_name,
                a.last_name,
                a.birthdate,
                a.contact,
                a.gender,
                a.civil_status,
                a.city,
                a.province,
                a.year_graduated,
                a.college,
                a.course,
                a.verification_document,
                a.created_at,
                a.profile_pic,
                e.experience_id,
                e.title,
                e.company,
                e.start_date,
                e.end_date,
                e.current,
                e.description,
                e.location_of_work,
                e.employment_status,
                e.employment_sector,
                e.created_at as exp_created_at,
                e.updated_at as exp_updated_at,
                u.email
            FROM alumni a 
            LEFT JOIN alumni_experience e ON a.alumni_id = e.alumni_id AND e.current = 1
            LEFT JOIN user u ON a.user_id = u.user_id
            ORDER BY a.college, a.course, a.last_name, a.first_name";
    $result = $db->query($sql);
    $detailed_employment_raw = [];
    while ($row = $result->fetch_assoc()) {
        $detailed_employment_raw[] = $row;
    }

    // Process detailed employment data organized by college and course
    $detailed_employment = [];
    
    // Group data by college and course
    $college_course_data = [];
    foreach ($detailed_employment_raw as $row) {
        $college = $row['college'];
        $course = $row['course'];
        
        if (!isset($college_course_data[$college])) {
            $college_course_data[$college] = [];
        }
        if (!isset($college_course_data[$college][$course])) {
            $college_course_data[$college][$course] = [];
        }
        
        // Determine if job is matched using Gemini API
        $job_related = '';
        if (!empty($row['title'])) {
            $prompt = "Given the course: '{$row['course']}' and the job title: '{$row['title']}', classify the alignment as one of: Highly Aligned, Moderately Aligned, Slightly Aligned, Not Aligned. Only return the label.";
            $alignment = call_gemini_api($prompt);
            $job_related = (strpos($alignment, 'Aligned') !== false && strpos($alignment, 'Not') === false) ? 'Yes' : 'No';
        }
        
        $fullName = [$row['first_name'], $row['middle_name'], $row['last_name']];
        $fullName = array_filter($fullName);
        $fullName = implode(' ', $fullName);
        
        $address = [$row['city'], $row['province']];
        $address = array_filter($address);
        $address = implode(', ', $address);
        
        $college_course_data[$college][$course][] = [
            'College' => $row['college'],
            'Course' => $row['course'],
            'Section' => 'Complete Details',
            'Alumni ID' => $row['alumni_id'],
            'User ID' => $row['user_id'],
            'Full Name' => $fullName,
            'Birthdate' => $row['birthdate'],
            'Contact' => $row['contact'],
            'Gender' => $row['gender'],
            'Civil Status' => $row['civil_status'],
            'Address' => $address,
            'Year Graduated' => $row['year_graduated'],
            'Verification Document' => $row['verification_document'],
            'Created At' => $row['created_at'],
            'Profile Pic' => $row['profile_pic'],
            'Email' => $row['email'],
            'Experience ID' => $row['experience_id'],
            'Job Title' => $row['title'],
            'Company' => $row['company'],
            'Start Date' => $row['start_date'],
            'End Date' => $row['end_date'],
            'Current' => $row['current'],
            'Job Description' => $row['description'],
            'Location of Work' => $row['location_of_work'],
            'Employment Status' => $row['employment_status'],
            'Employment Sector' => $row['employment_sector'],
            'Exp Created At' => $row['exp_created_at'],
            'Exp Updated At' => $row['exp_updated_at'],
            'Job Related' => $job_related
        ];
    }
    
    // Format detailed employment data by college and course with sections
    foreach ($college_course_data as $college => $courses) {
        // Add college header
        $detailed_employment[] = [
            'College' => $college,
            'Course' => '',
            'Section' => '',
            'Alumni ID' => '',
            'User ID' => '',
            'Full Name' => '',
            'Birthdate' => '',
            'Contact' => '',
            'Gender' => '',
            'Civil Status' => '',
            'Address' => '',
            'Year Graduated' => '',
            'Verification Document' => '',
            'Created At' => '',
            'Profile Pic' => '',
            'Email' => '',
            'Experience ID' => '',
            'Job Title' => '',
            'Company' => '',
            'Start Date' => '',
            'End Date' => '',
            'Current' => '',
            'Job Description' => '',
            'Location of Work' => '',
            'Employment Status' => '',
            'Employment Sector' => '',
            'Exp Created At' => '',
            'Exp Updated At' => '',
            'Job Related' => ''
        ];
        
        foreach ($courses as $course => $alumni_data) {
            // Add course header
            $detailed_employment[] = [
                'College' => '',
                'Course' => $course,
                'Section' => '',
                'Alumni ID' => '',
                'User ID' => '',
                'Full Name' => '',
                'Birthdate' => '',
                'Contact' => '',
                'Gender' => '',
                'Civil Status' => '',
                'Address' => '',
                'Year Graduated' => '',
                'Verification Document' => '',
                'Created At' => '',
                'Profile Pic' => '',
                'Email' => '',
                'Experience ID' => '',
                'Job Title' => '',
                'Company' => '',
                'Start Date' => '',
                'End Date' => '',
                'Current' => '',
                'Job Description' => '',
                'Location of Work' => '',
                'Employment Status' => '',
                'Employment Sector' => '',
                'Exp Created At' => '',
                'Exp Updated At' => '',
                'Job Related' => ''
            ];
            
            // Add alumni data for this course
            foreach ($alumni_data as $alumni) {
                $detailed_employment[] = $alumni;
            }
            
            // Add empty row after course
            $detailed_employment[] = [
                'College' => '',
                'Course' => '',
                'Section' => '',
                'Alumni ID' => '',
                'User ID' => '',
                'Full Name' => '',
                'Birthdate' => '',
                'Contact' => '',
                'Gender' => '',
                'Civil Status' => '',
                'Address' => '',
                'Year Graduated' => '',
                'Verification Document' => '',
                'Created At' => '',
                'Profile Pic' => '',
                'Email' => '',
                'Experience ID' => '',
                'Job Title' => '',
                'Company' => '',
                'Start Date' => '',
                'End Date' => '',
                'Current' => '',
                'Job Description' => '',
                'Location of Work' => '',
                'Employment Status' => '',
                'Employment Sector' => '',
                'Exp Created At' => '',
                'Exp Updated At' => '',
                'Job Related' => ''
            ];
        }
        
        // Add empty row after college
        $detailed_employment[] = [
            'College' => '',
            'Course' => '',
            'Section' => '',
            'Alumni ID' => '',
            'User ID' => '',
            'Full Name' => '',
            'Birthdate' => '',
            'Contact' => '',
            'Gender' => '',
            'Civil Status' => '',
            'Address' => '',
            'Year Graduated' => '',
            'Verification Document' => '',
            'Created At' => '',
            'Profile Pic' => '',
            'Email' => '',
            'Experience ID' => '',
            'Job Title' => '',
            'Company' => '',
            'Start Date' => '',
            'End Date' => '',
            'Current' => '',
            'Job Description' => '',
            'Location of Work' => '',
            'Employment Status' => '',
            'Employment Sector' => '',
            'Exp Created At' => '',
            'Exp Updated At' => '',
            'Job Related' => ''
        ];
    }

    // 7. INDUSTRY ANALYSIS DATA - IMPROVED VERSION
    // Define the standard industry categories
    $standard_industries = [
        'Agriculture, Hunting and Forestry',
        'Fishing',
        'Mining and Quarrying',
        'Manufacturing',
        'Electricity, Gas and Water Supply',
        'Construction',
        'Wholesale and Retail Trade, Repair of Motorcycles and Personal Households goods',
        'Hotels and Restaurants',
        'IT Industry',
        'Transport Storage and Communication',
        'Financial Intermediation',
        'Real State, Renting and Business Activities',
        'Public Administration and Defense; Compulsory Social Security',
        'Education',
        'Health and Social Work',
        'Other Community, Social and Personal Service Activities',
        'Private Households with Employed Persons',
        'Extra-territorial Organizations and Bodies'
    ];

    // Get all employed alumni with their job details
    $sql = "SELECT 
                a.alumni_id,
                a.course,
                a.gender,
                e.title as job_title,
                e.company,
                e.description as job_description,
                e.employment_sector
            FROM alumni a
            JOIN alumni_experience e ON a.alumni_id = e.alumni_id
            WHERE (e.employment_status = 'Employed' OR e.employment_status = 'Probational' OR e.employment_status = 'Regular') 
            AND e.current = 1
            ORDER BY a.course, a.gender";
    $result = $db->query($sql);
    
    $analysis_data = [];
    $courses = [];
    
    while ($row = $result->fetch_assoc()) {
        $course = $row['course'];
        $gender = $row['gender'];
        $job_title = $row['job_title'];
        $company = $row['company'];
        $job_description = $row['job_description'];
        
        // Track unique courses
        if (!in_array($course, $courses)) {
            $courses[] = $course;
        }
        
        // Use Gemini API to categorize the industry based on job title and company
        $prompt = "Given the job title: '{$job_title}', company: '{$company}', and job description: '{$job_description}', categorize this into one of these industries. Only return the exact industry name from the list:

Agriculture, Hunting and Forestry
Fishing
Mining and Quarrying
Manufacturing
Electricity, Gas and Water Supply
Construction
Wholesale and Retail Trade, Repair of Motorcycles and Personal Households goods
Hotels and Restaurants
IT Industry
Transport Storage and Communication
Financial Intermediation
Real State, Renting and Business Activities
Public Administration and Defense; Compulsory Social Security
Education
Health and Social Work
Other Community, Social and Personal Service Activities
Private Households with Employed Persons
Extra-territorial Organizations and Bodies

Only return the exact industry name from the list above.";
        
        $categorized_industry = call_gemini_api($prompt);
        
        // If Gemini returns something not in our list, default to "Other Community, Social and Personal Service Activities"
        if (!in_array($categorized_industry, $standard_industries)) {
            $categorized_industry = 'Other Community, Social and Personal Service Activities';
        }
        
        // Initialize data structure if not exists
        if (!isset($analysis_data[$course][$categorized_industry])) {
            $analysis_data[$course][$categorized_industry] = ['M' => 0, 'F' => 0];
        }
        
        // Count by gender
        if ($gender === 'M' || $gender === 'Male') {
            $analysis_data[$course][$categorized_industry]['M']++;
        } else if ($gender === 'F' || $gender === 'Female') {
            $analysis_data[$course][$categorized_industry]['F']++;
        }
    }

    // Create the final industry analysis data structure
    $industry_analysis = [];
    foreach ($standard_industries as $industry) {
        $row_data = ['Industry' => $industry];
        foreach ($courses as $course) {
            $male_count = $analysis_data[$course][$industry]['M'] ?? 0;
            $female_count = $analysis_data[$course][$industry]['F'] ?? 0;
            $total = $male_count + $female_count;
            
            $row_data[$course . ' - Male'] = $male_count;
            $row_data[$course . ' - Female'] = $female_count;
            $row_data[$course . ' - Total'] = $total;
        }
        $industry_analysis[] = $row_data;
    }

    // Return all data as JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'employment_summary' => $employment_summary,
        'employment_sector_summary' => $employment_sector_summary,
        'location_summary' => $location_summary,
        'employment_status_summary' => $employment_status_summary,
        'industry_determination' => $industry_determination,
        'detailed_employment' => $detailed_employment,
        'industry_analysis' => $industry_analysis,
        'debug_info' => [
            'total_detailed_records' => count($detailed_employment),
            'total_graduates_with_details' => count(array_filter($detailed_employment, function($row) {
                return $row['Section'] === 'Complete Details' && !empty($row['Full Name']);
            })),
            'colleges_with_data' => array_unique(array_filter(array_column($detailed_employment, 'College'))),
            'courses_with_data' => array_unique(array_filter(array_column($detailed_employment, 'Course')))
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 