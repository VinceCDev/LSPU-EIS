<?php
session_start();
if (isset($_SESSION['highlight_application_job_id'])) {
    unset($_SESSION['highlight_application_job_id']);
}
echo json_encode(['success' => true]);
?>