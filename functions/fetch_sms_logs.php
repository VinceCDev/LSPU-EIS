<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

// Check if user is authenticated
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = Database::getInstance()->getConnection();
$user_role = $_SESSION['user_role'];
$user_email = $_SESSION['email'];

// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Get filter parameters
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Build query
$where_conditions = [];
$params = [];
$param_types = '';

// Role-based filtering
if ($user_role === 'admin') {
    // Admin can see all SMS logs
    $base_query = "SELECT sl.*, 
                          CASE 
                              WHEN sl.user_role = 'admin' THEN CONCAT(a.first_name, ' ', a.last_name)
                              WHEN sl.user_role = 'employer' THEN e.company_name
                              WHEN sl.user_role = 'alumni' THEN CONCAT(al.first_name, ' ', al.last_name)
                          END as sender_name
                   FROM sms_logs sl
                   LEFT JOIN administrator a ON sl.sender_email = a.email
                   LEFT JOIN employer e ON sl.sender_email = e.contact_email
                   LEFT JOIN alumni al ON sl.sender_email = al.email";
} else {
    // Other users can only see their own SMS logs
    $base_query = "SELECT sl.*, 
                          CASE 
                              WHEN sl.user_role = 'admin' THEN CONCAT(a.first_name, ' ', a.last_name)
                              WHEN sl.user_role = 'employer' THEN e.company_name
                              WHEN sl.user_role = 'alumni' THEN CONCAT(al.first_name, ' ', al.last_name)
                          END as sender_name
                   FROM sms_logs sl
                   LEFT JOIN administrator a ON sl.sender_email = a.email
                   LEFT JOIN employer e ON sl.sender_email = e.contact_email
                   LEFT JOIN alumni al ON sl.sender_email = al.email
                   WHERE sl.sender_email = ?";
    $where_conditions[] = "sl.sender_email = ?";
    $params[] = $user_email;
    $param_types .= 's';
}

// Add status filter
if (!empty($status)) {
    $where_conditions[] = "sl.status = ?";
    $params[] = $status;
    $param_types .= 's';
}

// Add search filter
if (!empty($search)) {
    $where_conditions[] = "(sl.receiver_phone LIKE ? OR sl.message LIKE ? OR sl.sender_email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'sss';
}

// Add date range filter
if (!empty($date_from)) {
    $where_conditions[] = "DATE(sl.sent_at) >= ?";
    $params[] = $date_from;
    $param_types .= 's';
}

if (!empty($date_to)) {
    $where_conditions[] = "DATE(sl.sent_at) <= ?";
    $params[] = $date_to;
    $param_types .= 's';
}

// Build WHERE clause
$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM sms_logs sl $where_clause";
$count_stmt = $db->prepare($count_query);
if (!empty($params)) {
    $count_stmt->bind_param($param_types, ...$params);
}
$count_stmt->execute();
$total_result = $count_stmt->get_result()->fetch_assoc();
$total = $total_result['total'];
$count_stmt->close();

// Get paginated results
$query = "$base_query $where_clause ORDER BY sl.sent_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$param_types .= 'ii';

$stmt = $db->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$sms_logs = [];
while ($row = $result->fetch_assoc()) {
    $sms_logs[] = [
        'id' => $row['id'],
        'sender_email' => $row['sender_email'],
        'sender_name' => $row['sender_name'] ?? 'Unknown',
        'receiver_phone' => $row['receiver_phone'],
        'message' => $row['message'],
        'status' => $row['status'],
        'sent_at' => $row['sent_at'],
        'user_role' => $row['user_role'],
        'error_message' => $row['error_message'],
        'api_response' => $row['api_response']
    ];
}
$stmt->close();

// Calculate pagination info
$total_pages = ceil($total / $limit);
$has_next = $page < $total_pages;
$has_prev = $page > 1;

echo json_encode([
    'success' => true,
    'data' => [
        'sms_logs' => $sms_logs,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_records' => $total,
            'limit' => $limit,
            'has_next' => $has_next,
            'has_prev' => $has_prev
        ]
    ]
]);
?> 