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

// Fetch data from database
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
            e.employment_status,
            e.employment_sector,
            e.location_of_work,
            e.description
        FROM alumni a 
        LEFT JOIN user u ON a.user_id = u.user_id
        LEFT JOIN alumni_experience e ON a.alumni_id = e.alumni_id AND e.current = 1
        ORDER BY a.college, a.course, a.last_name, a.first_name";

$result = $db->query($sql);
$data = [];
$program_stats = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
    
    // Group by program for statistics
    $course = $row['course'];
    if (!isset($program_stats[$course])) {
        $program_stats[$course] = [
            'total_graduates' => 0,
            'employed_count' => 0,
            'related_job_count' => 0
        ];
    }
    
    $program_stats[$course]['total_graduates']++;
    
    if ($row['employment_status'] === 'Employed') {
        $program_stats[$course]['employed_count']++;
        
        // Use Gemini API to determine if job is related to course
        if (!empty($row['job_title'])) {
            $prompt = "Given the course: '{$row['course']}' and the job title: '{$row['job_title']}', classify the alignment as one of: Highly Aligned, Moderately Aligned, Slightly Aligned, Not Aligned. Only return the label.";
            $alignment = call_gemini_api($prompt);
            
            if (strpos($alignment, 'Aligned') !== false && strpos($alignment, 'Not') === false) {
                $program_stats[$course]['related_job_count']++;
            }
        }
    }
}

if ($format === 'excel') {
    // Return JSON data for Excel export
    header('Content-Type: application/json');
    
    $excel_data = [];
    
    // Add summary statistics
    foreach ($program_stats as $course => $stats) {
        $percentage = $stats['total_graduates'] > 0 ? round(($stats['employed_count'] / $stats['total_graduates']) * 100, 2) : 0;
        $match_percentage = $stats['employed_count'] > 0 ? round(($stats['related_job_count'] / $stats['employed_count']) * 100, 2) : 0;
        
        $excel_data[] = [
            'Program' => $course,
            'Total Graduates' => $stats['total_graduates'],
            'Employed' => $stats['employed_count'],
            'Employment Rate (%)' => $percentage,
            'Job Related' => $stats['related_job_count'],
            'Match Rate (%)' => $match_percentage
        ];
    }
    
    // Add detailed data
    foreach ($data as $row) {
        $full_name = trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
        $campus = 'SPCC';
        $program = $row['course'];
        $gender = $row['gender'] ?? 'M';
        $graduation_date = $row['year_graduated'] ?? '';
        $hired_date = $row['start_date'] ?? '';
        
        // Determine if job is matched using Gemini API
        $ctr_matched = '';
        if (!empty($row['job_title'])) {
            $prompt = "Given the course: '{$row['course']}' and the job title: '{$row['job_title']}', classify the alignment as one of: Highly Aligned, Moderately Aligned, Slightly Aligned, Not Aligned. Only return the label.";
            $alignment = call_gemini_api($prompt);
            $ctr_matched = (strpos($alignment, 'Aligned') !== false && strpos($alignment, 'Not') === false) ? 'Yes' : 'No';
        }
        
        $prior_status = 'Student';
        $current_status = $row['employment_status'] ?? '';
        $sector = $row['employment_sector'] ?? '';
        $location = $row['location_of_work'] ?? 'Local';
        $income = ''; // Would need to be added to database
        $company = $row['company'] ?? '';
        $address = trim(($row['city'] ?? '') . ', ' . ($row['province'] ?? ''));
        
        $excel_data[] = [
            'Campus' => $campus,
            'Program' => $program,
            'Full Name' => $full_name,
            'Gender' => $gender,
            'Graduation Year' => $graduation_date,
            'Date Hired' => $hired_date,
            'Job Related' => $ctr_matched,
            'Prior Status' => $prior_status,
            'Current Status' => $current_status,
            'Sector' => $sector,
            'Location' => $location,
            'Monthly Income' => $income,
            'Company' => $company,
            'Contact' => $row['contact'] ?? '',
            'Email' => $row['email'] ?? '',
            'Address' => $address
        ];
    }
    
    echo json_encode($excel_data);
    
} elseif ($format === 'pdf') {
    // Set headers for PDF download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="employment_summary_' . date('Y-m-d') . '.pdf"');
    
    // Create PDF content
    $html = '<html><head><style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; font-size: 18px; font-weight: bold; margin: 20px 0; }
        .subheader { text-align: center; font-size: 14px; margin: 10px 0; }
    </style></head><body>';
    
    $html .= '<div class="header">Data on Employment-' . date('Y') . '</div>';
    $html .= '<div class="subheader">Name of SUC: LAGUNA STATE POLYTECHNIC UNIVERSITY-SAN PABLO CITY CAMPUS</div>';
    
    // Employment Summary Table
    $html .= '<h3>Employment Summary by Program</h3>';
    $html .= '<table>
        <tr>
            <th>Program</th>
            <th>No. of Graduates</th>
            <th>No. of Employed</th>
            <th>Percentage</th>
            <th>Work Related/in-line to course</th>
            <th>% Matched</th>
        </tr>';
    
    foreach ($program_stats as $course => $stats) {
        $percentage = $stats['total_graduates'] > 0 ? round(($stats['employed_count'] / $stats['total_graduates']) * 100, 2) : 0;
        $match_percentage = $stats['employed_count'] > 0 ? round(($stats['related_job_count'] / $stats['employed_count']) * 100, 2) : 0;
        
        $html .= "<tr>
            <td>$course</td>
            <td>{$stats['total_graduates']}</td>
            <td>{$stats['employed_count']}</td>
            <td>{$percentage}%</td>
            <td>{$stats['related_job_count']}</td>
            <td>{$match_percentage}%</td>
        </tr>";
    }
    
    $html .= '</table>';
    
    // Detailed Employment Data
    $html .= '<h3>Detailed Employment Data</h3>';
    $html .= '<table>
        <tr>
            <th>Program</th>
            <th>Name</th>
            <th>Gender</th>
            <th>Graduation Date</th>
            <th>Job Title</th>
            <th>Company</th>
            <th>Employment Status</th>
            <th>Sector</th>
            <th>Job Related</th>
        </tr>';
    
    foreach ($data as $row) {
        $full_name = trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
        $job_related = '';
        
        if (!empty($row['job_title'])) {
            $prompt = "Given the course: '{$row['course']}' and the job title: '{$row['job_title']}', classify the alignment as one of: Highly Aligned, Moderately Aligned, Slightly Aligned, Not Aligned. Only return the label.";
            $alignment = call_gemini_api($prompt);
            $job_related = (strpos($alignment, 'Aligned') !== false && strpos($alignment, 'Not') === false) ? 'Matched' : 'Mismatched';
        }
        
        $html .= "<tr>
            <td>{$row['course']}</td>
            <td>$full_name</td>
            <td>{$row['gender']}</td>
            <td>{$row['date_of_graduation']}</td>
            <td>{$row['job_title']}</td>
            <td>{$row['company']}</td>
            <td>{$row['employment_status']}</td>
            <td>{$row['employment_sector']}</td>
            <td>$job_related</td>
        </tr>";
    }
    
    $html .= '</table></body></html>';
    
    // For PDF, you would need a library like TCPDF or mPDF
    // For now, we'll output HTML that can be converted to PDF
    echo $html;
}
?> 