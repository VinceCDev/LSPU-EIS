<?php
session_start();
header('Content-Type: application/json');
require_once '../conn/db_conn.php';

// Check if user is authenticated and is admin
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = Database::getInstance()->getConnection();

// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Get filter parameters
$type = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';

// Build query
$where_conditions = [];
$params = [];
$param_types = '';

// Add type filter
if (!empty($type)) {
    $where_conditions[] = "type = ?";
    $params[] = $type;
    $param_types .= 's';
}

// Add status filter
if (!empty($status)) {
    $where_conditions[] = "status = ?";
    $params[] = $status;
    $param_types .= 's';
}

// Build WHERE clause
$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM reminder_logs $where_clause";
$count_stmt = $db->prepare($count_query);
if (!empty($params)) {
    $count_stmt->bind_param($param_types, ...$params);
}
$count_stmt->execute();
$total_result = $count_stmt->get_result()->fetch_assoc();
$total = $total_result['total'];
$count_stmt->close();

// Get paginated results
$query = "SELECT * FROM reminder_logs $where_clause ORDER BY sent_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$param_types .= 'ii';

$stmt = $db->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$reminder_logs = [];
while ($row = $result->fetch_assoc()) {
    $reminder_logs[] = [
        'id' => $row['id'],
        'type' => $row['type'],
        'recipient' => $row['recipient'],
        'subject' => $row['subject'],
        'message' => $row['message'],
        'status' => $row['status'],
        'error_message' => $row['error_message'],
        'sent_at' => $row['sent_at'],
        'created_at' => $row['created_at']
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
        'reminder_logs' => $reminder_logs,
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