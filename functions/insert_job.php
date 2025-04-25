<?php
require_once '../conn/db_conn.php';

// Read the raw POST data
$inputData = json_decode(file_get_contents('php://input'), true);

// Check if the required data is provided
if (isset($inputData['title'], $inputData['department'], $inputData['type'], $inputData['location'], $inputData['description'], $inputData['requirements'], $inputData['status'], $inputData['employerQuestion'], $inputData['qualifications'])) {
    // First, fetch the company_id based on the department (company_name)
    $department = $inputData['department'];

    // Prepare and execute the query to fetch company_id
    $stmt = $pdo->prepare("SELECT id FROM company WHERE company_name = :department");
    $stmt->bindParam(':department', $department, PDO::PARAM_STR);
    $stmt->execute();

    // Check if a matching company was found
    if ($stmt->rowCount() > 0) {
        $company = $stmt->fetch(PDO::FETCH_ASSOC);
        $company_id = $company['id']; // Get the company_id

        // Now, insert the job data with the correct company_id
        $stmt = $pdo->prepare("INSERT INTO jobs (title, department, type, location, description, requirements, salary, status, employer_question, qualifications, company_id) VALUES (:title, :department, :type, :location, :description, :requirements, :salary, :status, :employerQuestion, :qualifications, :company_id)");

        // Bind parameters using named placeholders
        $stmt->bindParam(':title', $inputData['title'], PDO::PARAM_STR);
        $stmt->bindParam(':department', $inputData['department'], PDO::PARAM_STR);
        $stmt->bindParam(':type', $inputData['type'], PDO::PARAM_STR);
        $stmt->bindParam(':location', $inputData['location'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $inputData['description'], PDO::PARAM_STR);
        $stmt->bindParam(':requirements', $inputData['requirements'], PDO::PARAM_STR);
        $stmt->bindParam(':salary', $inputData['salary'], PDO::PARAM_STR);  // Salary can be NULL or string
        $stmt->bindParam(':status', $inputData['status'], PDO::PARAM_STR);
        $stmt->bindParam(':employerQuestion', $inputData['employerQuestion'], PDO::PARAM_STR);
        $stmt->bindParam(':qualifications', $inputData['qualifications'], PDO::PARAM_STR);
        $stmt->bindParam(':company_id', $company_id, PDO::PARAM_INT); // Use the fetched company_id

        // Execute the query and check for success
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->errorInfo()]);
        }
    } else {
        // If no company was found matching the department
        echo json_encode(['success' => false, 'error' => 'No company found with that department name']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
}

// Close the connection
$pdo = null;
