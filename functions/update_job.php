<?php
require_once '../conn/db_conn.php';

// Read the raw POST data
$inputData = json_decode(file_get_contents('php://input'), true);

// Check if the required data is provided
if (isset($inputData['id'], $inputData['title'], $inputData['department'], $inputData['type'], $inputData['location'], $inputData['description'], $inputData['requirements'], $inputData['status'], $inputData['employerQuestion'], $inputData['qualifications'])) {

    $jobId = $inputData['id'];
    $department = $inputData['department'];

    // Fetch the company_id based on the department (company_name)
    $stmt = $pdo->prepare("SELECT id FROM company WHERE company_name = :department");
    $stmt->bindParam(':department', $department, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $company = $stmt->fetch(PDO::FETCH_ASSOC);
        $company_id = $company['id'];

        // Prepare the UPDATE query
        $stmt = $pdo->prepare("UPDATE jobs 
            SET 
                title = :title,
                department = :department,
                type = :type,
                location = :location,
                description = :description,
                requirements = :requirements,
                salary = :salary,
                status = :status,
                employer_question = :employerQuestion,
                qualifications = :qualifications,
                company_id = :company_id
            WHERE id = :id");

        // Bind values
        $stmt->bindParam(':title', $inputData['title'], PDO::PARAM_STR);
        $stmt->bindParam(':department', $inputData['department'], PDO::PARAM_STR);
        $stmt->bindParam(':type', $inputData['type'], PDO::PARAM_STR);
        $stmt->bindParam(':location', $inputData['location'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $inputData['description'], PDO::PARAM_STR);
        $stmt->bindParam(':requirements', $inputData['requirements'], PDO::PARAM_STR);
        $stmt->bindParam(':salary', $inputData['salary'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $inputData['status'], PDO::PARAM_STR);
        $stmt->bindParam(':employerQuestion', $inputData['employerQuestion'], PDO::PARAM_STR);
        $stmt->bindParam(':qualifications', $inputData['qualifications'], PDO::PARAM_STR);
        $stmt->bindParam(':company_id', $company_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $jobId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->errorInfo()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No company found with that department name']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
}

// Close connection
$pdo = null;
