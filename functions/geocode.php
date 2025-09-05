<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if query parameter exists
if (!isset($_GET['q']) || empty($_GET['q'])) {
    echo json_encode(['error' => 'No query provided']);
    exit();
}

$query = urlencode($_GET['q']);
$url = "https://nominatim.openstreetmap.org/search?format=json&q={$query}&countrycodes=ph&limit=5";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'LSPU-EIS/1.0 (https://lspueis.com)');
curl_setopt($ch, CURLOPT_REFERER, 'https://lspueis.com');
curl_setopt($ch, CURLOPT_FAILONERROR, true);

// Execute request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo json_encode(['error' => 'API request failed: ' . curl_error($ch)]);
    curl_close($ch);
    exit();
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Check HTTP status code
if ($httpCode !== 200) {
    echo json_encode(['error' => 'API returned HTTP code: ' . $httpCode]);
    exit();
}

// Return the response
echo $response;
?>