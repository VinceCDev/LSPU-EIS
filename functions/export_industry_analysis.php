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

// Fetch industry analysis data
$sql = "SELECT 
            a.course,
            a.gender,
            e.employment_sector,
            e.description as job_description,
            e.title as job_title
        FROM alumni a 
        LEFT JOIN alumni_experience e ON a.alumni_id = e.alumni_id
        WHERE e.current = 1 AND e.employment_status = 'Employed'
        ORDER BY a.course, a.gender, e.employment_sector";

$result = $db->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Define industry categories (based on the image)
$industries = [
    'Agriculture, Hunting and Forestry',
    'Fishing',
    'Mining and Quarrying',
    'Manufacturing',
    'Electricity, Gas and Water Supply',
    'Construction',
    'Wholesale and Retail Trade, Repair of Motorcycles',
    'Hotels and Restaurants',
    'IT Industry',
    'Transport Storage and Communication',
    'Financial Intermediation',
    'Real Estate, Renting and Business Activities',
    'Public Administration and Defense: Compulsory'
];

// Group data by course, industry, and gender
$analysis_data = [];
foreach ($data as $row) {
    $course = $row['course'];
    $gender = $row['gender'] ?? 'M';
    $sector = $row['employment_sector'] ?? 'IT Industry'; // Default to IT Industry
    
    // Map employment sector to industry categories
    $industry = 'IT Industry'; // Default
    if (strpos(strtolower($sector), 'government') !== false) {
        $industry = 'Public Administration and Defense: Compulsory';
    } elseif (strpos(strtolower($sector), 'manufacturing') !== false) {
        $industry = 'Manufacturing';
    } elseif (strpos(strtolower($sector), 'finance') !== false || strpos(strtolower($sector), 'bank') !== false) {
        $industry = 'Financial Intermediation';
    } elseif (strpos(strtolower($sector), 'retail') !== false || strpos(strtolower($sector), 'trade') !== false) {
        $industry = 'Wholesale and Retail Trade, Repair of Motorcycles';
    } elseif (strpos(strtolower($sector), 'hotel') !== false || strpos(strtolower($sector), 'restaurant') !== false) {
        $industry = 'Hotels and Restaurants';
    } elseif (strpos(strtolower($sector), 'transport') !== false) {
        $industry = 'Transport Storage and Communication';
    } elseif (strpos(strtolower($sector), 'real estate') !== false) {
        $industry = 'Real Estate, Renting and Business Activities';
    }
    
    if (!isset($analysis_data[$course])) {
        $analysis_data[$course] = [];
    }
    if (!isset($analysis_data[$course][$industry])) {
        $analysis_data[$course][$industry] = ['M' => 0, 'F' => 0];
    }
    $analysis_data[$course][$industry][$gender]++;
}

if ($format === 'excel') {
    // Return JSON data for Excel export
    header('Content-Type: application/json');
    
    $excel_data = [];
    
    // Get unique courses
    $courses = array_keys($analysis_data);
    
    // Add industry analysis data
    foreach ($industries as $industry) {
        $row_data = ['Industry' => $industry];
        foreach ($courses as $course) {
            $male_count = $analysis_data[$course][$industry]['M'] ?? 0;
            $female_count = $analysis_data[$course][$industry]['F'] ?? 0;
            $total = $male_count + $female_count;
            
            $row_data[$course . ' - Male'] = $male_count;
            $row_data[$course . ' - Female'] = $female_count;
            $row_data[$course . ' - Total'] = $total;
        }
        $excel_data[] = $row_data;
    }
    
    echo json_encode($excel_data);
    
} elseif ($format === 'pdf') {
    // Set headers for PDF download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="industry_analysis_' . date('Y-m-d') . '.pdf"');
    
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
    
    $html .= '<div class="header">Nature of Work/Industry Analysis</div>';
    $html .= '<div class="subheader">LAGUNA STATE POLYTECHNIC UNIVERSITY-SAN PABLO CITY CAMPUS</div>';
    
    // Industry Analysis Table
    $html .= '<div class="section">';
    $html .= '<table><tr><th>Nature of Work/Industry</th>';
    
    // Get unique courses
    $courses = array_keys($analysis_data);
    foreach ($courses as $course) {
        $html .= "<th colspan='3'>$course</th>";
    }
    $html .= '</tr><tr><th></th>';
    
    foreach ($courses as $course) {
        $html .= '<th>MALE</th><th>FEMALE</th><th>TOTAL</th>';
    }
    $html .= '</tr>';
    
    // Data rows for each industry
    foreach ($industries as $industry) {
        $html .= "<tr><td>$industry</td>";
        foreach ($courses as $course) {
            $male_count = $analysis_data[$course][$industry]['M'] ?? 0;
            $female_count = $analysis_data[$course][$industry]['F'] ?? 0;
            $total = $male_count + $female_count;
            $html .= "<td>$male_count</td><td>$female_count</td><td>$total</td>";
        }
        $html .= '</tr>';
    }
    $html .= '</table></div>';
    
    // Summary Statistics
    $html .= '<div class="section">';
    $html .= '<h3>Summary by Program</h3>';
    $html .= '<table><tr><th>Program</th><th>Total Employed</th><th>Male</th><th>Female</th></tr>';
    
    foreach ($courses as $course) {
        $total_male = 0;
        $total_female = 0;
        foreach ($industries as $industry) {
            $total_male += $analysis_data[$course][$industry]['M'] ?? 0;
            $total_female += $analysis_data[$course][$industry]['F'] ?? 0;
        }
        $total = $total_male + $total_female;
        $html .= "<tr><td>$course</td><td>$total</td><td>$total_male</td><td>$total_female</td></tr>";
    }
    $html .= '</table></div>';
    
    $html .= '</body></html>';
    
    echo $html;
}
?> 