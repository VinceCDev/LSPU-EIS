<?php
session_start();
require_once '../conn/db_conn.php';

// Check if user is authenticated and is admin
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('HTTP/1.1 401 Unauthorized');
    exit();
}

$format = $_GET['format'] ?? 'excel';
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

// Fetch detailed employment data
$sql = "SELECT 
            a.alumni_id,
            a.first_name,
            a.last_name,
            a.middle_name,
            a.course,
            a.college,
            a.contact,
            u.email,
            a.civil_status,
            a.birthdate,
            a.city,
            a.province,
            a.gender,
            a.year_graduated,
            e.title as job_title,
            e.company,
            e.start_date,
            e.end_date,
            e.employment_status,
            e.employment_sector,
            e.location_of_work,
            e.description
        FROM alumni a 
        LEFT JOIN user u ON a.user_id = u.user_id
        LEFT JOIN alumni_experience e ON a.alumni_id = e.alumni_id
        WHERE e.current = 1 OR e.current IS NULL
        ORDER BY a.college, a.course, a.last_name, a.first_name";

$result = $db->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

if ($format === 'excel') {
    // Return JSON data for Excel export
    header('Content-Type: application/json');
    
    $excel_data = [];
    
    // Add detailed employment data
    foreach ($data as $row) {
        $full_name = trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
        $program = $row['course'];
        $gender = $row['gender'] ?? 'M';
        $graduation_date = $row['year_graduated'] ?? '';
        $hired_date = $row['start_date'] ?? '';
        $end_date = $row['end_date'] ?? '';
        
        // Determine if job is matched using Gemini API
        $ctr_matched = '';
        if (!empty($row['job_title'])) {
            $prompt = "Given the course: '{$row['course']}' and the job title: '{$row['job_title']}', classify the alignment as one of: Highly Aligned, Moderately Aligned, Slightly Aligned, Not Aligned. Only return the label.";
            $alignment = call_gemini_api($prompt);
            $ctr_matched = (strpos($alignment, 'Aligned') !== false && strpos($alignment, 'Not') === false) ? 'Yes' : 'No';
        }
        
        $current_status = $row['employment_status'] ?? '';
        $sector = $row['employment_sector'] ?? '';
        $location = $row['location_of_work'] ?? 'Local';
        $company = $row['company'] ?? '';
        $address = trim(($row['city'] ?? '') . ', ' . ($row['province'] ?? ''));
        
        $excel_data[] = [
            'Program' => $program,
            'Full Name' => $full_name,
            'Gender' => $gender,
            'Graduation Year' => $graduation_date,
            'Date Hired' => $hired_date,
            'Date Ended' => $end_date,
            'Job Title' => $row['job_title'] ?? '',
            'Company' => $company,
            'Job Related' => $ctr_matched,
            'Employment Status' => $current_status,
            'Sector' => $sector,
            'Location' => $row['location_of_work'] ?? '',
            'Contact' => $row['contact'] ?? '',
            'Email' => $row['email'] ?? '',
            'Address' => $address,
            'Job Description' => $row['description'] ?? ''
        ];
    }
    
    echo json_encode($excel_data);
    
} elseif ($format === 'pdf') {
    // Set headers for PDF download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="detailed_employment_' . date('Y-m-d') . '.pdf"');
    
    // Create PDF content
    $html = '<html><head><style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; font-size: 18px; font-weight: bold; margin: 20px 0; }
        .subheader { text-align: center; font-size: 14px; margin: 10px 0; }
        .section { margin: 30px 0; }
    </style></head><body>';
    
    $html .= '<div class="header">Data on Employment-' . date('Y') . '</div>';
    $html .= '<div class="subheader">Name of SUC: LAGUNA STATE POLYTECHNIC UNIVERSITY-SAN PABLO CITY CAMPUS</div>';
    
    // Summary Statistics
    $html .= '<div class="section">';
    $html .= '<h3>Employment Status Summary</h3>';
    $html .= '<table><tr><th>Program</th><th>Status</th><th>Count</th></tr>';
    
    $program_summary = [];
    foreach ($data as $row) {
        $course = $row['course'];
        $status = $row['employment_status'] ?? 'Unemployed';
        
        if (!isset($program_summary[$course])) {
            $program_summary[$course] = [];
        }
        if (!isset($program_summary[$course][$status])) {
            $program_summary[$course][$status] = 0;
        }
        $program_summary[$course][$status]++;
    }
    
    foreach ($program_summary as $course => $statuses) {
        foreach ($statuses as $status => $count) {
            $html .= "<tr><td>$course</td><td>$status</td><td>$count</td></tr>";
        }
    }
    $html .= '</table></div>';
    
    // Company Details
    $html .= '<div class="section">';
    $html .= '<h3>Company Details</h3>';
    $html .= '<table><tr><th>Program</th><th>Company & Position</th><th>Job Related</th><th>Contact</th><th>Email</th></tr>';
    
    foreach ($data as $row) {
        $program = $row['course'];
        $company_position = $row['company'] . ' / ' . $row['job_title'];
        $job_related = '';
        
        if (!empty($row['job_title'])) {
            $prompt = "Given the course: '{$row['course']}' and the job title: '{$row['job_title']}', classify the alignment as one of: Highly Aligned, Moderately Aligned, Slightly Aligned, Not Aligned. Only return the label.";
            $alignment = call_gemini_api($prompt);
            $job_related = (strpos($alignment, 'Aligned') !== false && strpos($alignment, 'Not') === false) ? 'Matched' : 'Mismatched';
        }
        
        $contact = $row['contact'] ?? '';
        $email = $row['email'] ?? '';
        
        $html .= "<tr><td>$program</td><td>$company_position</td><td>$job_related</td><td>$contact</td><td>$email</td></tr>";
    }
    $html .= '</table></div>';
    
    // Personal Details
    $html .= '<div class="section">';
    $html .= '<h3>Personal Details</h3>';
    $html .= '<table><tr><th>Name</th><th>Contact</th><th>Email</th><th>Civil Status</th><th>Birthday</th><th>Address</th></tr>';
    
    foreach ($data as $row) {
        $full_name = trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
        $contact = $row['contact'] ?? '';
        $email = $row['email'] ?? '';
        $civil_status = $row['civil_status'] ?? '';
        $birthday = $row['birthday'] ?? '';
        $address = trim(($row['city'] ?? '') . ', ' . ($row['province'] ?? ''));
        
        $html .= "<tr><td>$full_name</td><td>$contact</td><td>$email</td><td>$civil_status</td><td>$birthday</td><td>$address</td></tr>";
    }
    $html .= '</table></div>';
    
    $html .= '</body></html>';
    
    echo $html;
}
?> 