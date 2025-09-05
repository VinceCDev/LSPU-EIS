<?php
session_start();
header('Content-Type: application/json');

try {
    // Check if user is authenticated
    if (!isset($_SESSION['email']) || !isset($_SESSION['user_role'])) {
        throw new Exception('Not authenticated.');
    }

    require_once '../conn/db_conn.php';
    $db = Database::getInstance()->getConnection();

    // If user is admin and alumni_id is provided, use that
    // Otherwise, use the logged-in user's alumni_id
    if ($_SESSION['user_role'] === 'admin' && isset($_GET['alumni_id'])) {
        $alumni_id = intval($_GET['alumni_id']);
    } else {
        // For alumni users, get their own ID
        $email = $_SESSION['email'];
        $stmt = $db->prepare('SELECT alumni_id FROM alumni a JOIN user u ON a.user_id = u.user_id WHERE u.email = ? LIMIT 1');
        if (!$stmt) {
            throw new Exception('Database preparation failed.');
        }
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($alumni_id);
        $stmt->fetch();
        $stmt->close();
    }

    if (!$alumni_id) {
        throw new Exception('Alumni not found.');
    }

    // Get the resume from alumni_resumes table based on alumni_id
    $resume_file = null;
    $stmt = $db->prepare("SELECT resume_file FROM alumni_resumes WHERE alumni_id = ?");
    if (!$stmt) {
        throw new Exception('Database preparation failed.');
    }
    $stmt->bind_param("i", $alumni_id);
    $stmt->execute();
    $stmt->bind_result($resume_file);
    $stmt->fetch();
    $stmt->close();

    if ($resume_file) {
        echo json_encode(['success' => true, 'resume' => [
            'file_name' => $resume_file,
            'url' => 'uploads/resume/' . $resume_file
        ]]);
    } else {
        echo json_encode(['success' => true, 'resume' => null]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;
?>