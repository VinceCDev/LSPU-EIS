<?php
require_once '../conn/db_conn.php';

// Read the raw POST data
$inputData = json_decode(file_get_contents('php://input'), true);

// Check if the required data is provided
if (isset(
    $inputData['title'],
    $inputData['company'],
    $inputData['type'],
    $inputData['location'],
    $inputData['description'],
    $inputData['requirements'],
    $inputData['qualifications'],
    $inputData['employerQuestion'],
    $inputData['salary'],
    $inputData['status']
)) {
    // First, fetch the company_id based on the company (company_name)
    $company = $inputData['company'];

    // Prepare and execute the query to fetch company_id
    $query = "SELECT id FROM company_profile WHERE company_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $company);  // Bind company parameter
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a matching company was found
    if ($result->num_rows > 0) {
        $company = $result->fetch_assoc();
        $company_id = $company['id']; // Get the company_id

        // Now, insert the job data with the correct company_id
        $insertQuery = "INSERT INTO jobs (company_id, title, company, type, location, description, requirements, qualifications, employer_question, salary, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);

        // Bind parameters using the appropriate types
        $insertStmt->bind_param(
            'issssssssss',
            $company_id, // company_id (int)
            $inputData['title'], // title (string)
            $inputData['company'], // company (string) -- Reuse the same company name
            $inputData['type'], // type (string)
            $inputData['location'], // location (string)
            $inputData['description'], // description (string)
            $inputData['requirements'], // requirements (string)
            $inputData['qualifications'], // qualifications (string)
            $inputData['employerQuestion'], // employer_question (string)
            $inputData['salary'], // salary (string or numeric, adjust if needed)
            $inputData['status'] // status (string)
        );

        // Execute the query and check for success
        if ($insertStmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $insertStmt->error]);
        }

        // Close the insert statement
        $insertStmt->close();
    } else {
        // If no company was found matching the company name
        echo json_encode(['success' => false, 'error' => 'No company found with that company name']);
    }

    // Close the first statement
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
}

// Close the connection
$conn->close();
