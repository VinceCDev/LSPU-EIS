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
    // Get detailed employment data to calculate accurate job related counts
    $sql = "SELECT 
                a.course,
                a.college,
                e.title as job_title,
                e.employment_status
            FROM alumni a 
            LEFT JOIN alumni_experience e ON a.alumni_id = e.alumni_id AND e.current = 1
            ORDER BY a.course, a.college";
    $result = $db->query($sql);
    $employment_data = [];
    while ($row = $result->fetch_assoc()) {
        $employment_data[] = $row;
    }

    // Process data to calculate accurate statistics
    $program_stats = [];
    $course_stats = [];

    foreach ($employment_data as $row) {
        $course = $row['course'];
        $college = $row['college'];
        $key = $course . '|' . $college;
        
        if (!isset($course_stats[$key])) {
            $course_stats[$key] = [
                'course' => $course,
                'college' => $college,
                'total_graduates' => 0,
                'employed_count' => 0,
                'related_job_count' => 0
            ];
        }
        
        $course_stats[$key]['total_graduates']++;
        
        if ($row['employment_status'] === 'Employed' || $row['employment_status'] === 'Probational' || $row['employment_status'] === 'Regular') {
            $course_stats[$key]['employed_count']++;
            
            // Use Gemini API to determine if job is related to course
            if (!empty($row['job_title'])) {
                $prompt = "Given the course: '{$row['course']}' and the job title: '{$row['job_title']}', classify the alignment as one of: Highly Aligned, Moderately Aligned, Slightly Aligned, Not Aligned. Only return the label.";
                $alignment = call_gemini_api($prompt);
                if (strpos($alignment, 'Aligned') !== false && strpos($alignment, 'Not') === false) {
                    $course_stats[$key]['related_job_count']++;
                }
            }
        }
    }

    // Convert to array format
    foreach ($course_stats as $stats) {
        $program_stats[] = $stats;
    }

    // Employment by sector
    $sql = "SELECT 
                e.employment_sector,
                COUNT(*) as count
            FROM alumni a
            JOIN alumni_experience e ON a.alumni_id = e.alumni_id
            WHERE (e.employment_status = 'Employed' OR e.employment_status = 'Probational' OR e.employment_status = 'Regular') AND e.current = 1
            GROUP BY e.employment_sector";
    $result = $db->query($sql);
    $sector_stats = [];
    while ($row = $result->fetch_assoc()) {
        $sector_stats[] = $row;
    }

    // Employment by location
    $sql = "SELECT 
                e.location_of_work,
                COUNT(*) as count
            FROM alumni a
            JOIN alumni_experience e ON a.alumni_id = e.alumni_id
            WHERE (e.employment_status = 'Employed' OR e.employment_status = 'Probational' OR e.employment_status = 'Regular') AND e.current = 1
            GROUP BY e.location_of_work";
    $result = $db->query($sql);
    $location_stats = [];
    while ($row = $result->fetch_assoc()) {
        $location_stats[] = $row;
    }

    // Employment status
    $sql = "SELECT 
                CASE 
                    WHEN e.employment_status IN ('Employed', 'Probational', 'Regular') THEN 'Employed'
                    WHEN e.employment_status IS NULL THEN 'Unemployed'
                    ELSE e.employment_status
                END as employment_status,
                COUNT(*) as count
            FROM alumni a
            LEFT JOIN alumni_experience e ON a.alumni_id = e.alumni_id AND e.current = 1
            GROUP BY CASE 
                WHEN e.employment_status IN ('Employed', 'Probational', 'Regular') THEN 'Employed'
                WHEN e.employment_status IS NULL THEN 'Unemployed'
                ELSE e.employment_status
            END";
    $result = $db->query($sql);
    $status_stats = [];
    while ($row = $result->fetch_assoc()) {
        $status_stats[] = $row;
    }

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'program_stats' => $program_stats,
        'sector_stats' => $sector_stats,
        'location_stats' => $location_stats,
        'status_stats' => $status_stats
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 