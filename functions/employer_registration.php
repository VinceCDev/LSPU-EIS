<?php
// Include the database connection
require_once '../conn/db_conn.php'; // Adjust the path based on your file structure

// Start session for CSRF token
session_start();

if (empty($_SESSION['csrf_token'])) {
    // Generate a new CSRF token if it does not exist
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a random token
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Capture form inputs
    $email = $_POST['email'];
    $password = $_POST['password'];
    $company_name = $_POST['company_name'];
    $nature_of_business = $_POST['nature_of_business'];
    $company_type = $_POST['company_type'];
    $company_location = $_POST['company_location'];
    $contact_email = $_POST['contact_email'];
    $contact_person = $_POST['contact_person'];

    // File upload handling
    $document_file = $_FILES['document_file']; // Get the file input

    // Validate the uploaded file (e.g., size, type)
    $allowed_extensions = ['pdf', 'doc', 'docx', 'jpg', 'png']; // Adjust the allowed file extensions
    $file_extension = strtolower(pathinfo($document_file['name'], PATHINFO_EXTENSION)); // Normalize file extension
    $file_name = time() . "_" . basename($document_file['name']); // Generate a unique file name
    $upload_dir = '../uploads/'; // Directory to store uploaded files
    $max_file_size = 10 * 1024 * 1024; // 10 MB file size limit

    // Check if the file is allowed
    if (!in_array($file_extension, $allowed_extensions)) {
        die("Invalid file type. Only PDF, DOC, DOCX, JPG, PNG files are allowed.");
    }

    // Check file size
    if ($document_file['size'] > $max_file_size) {
        die("File size exceeds the 10MB limit.");
    }

    // Check for file upload errors
    if ($document_file['error'] !== UPLOAD_ERR_OK) {
        die("File upload failed with error code: " . $document_file['error']);
    }

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($document_file['tmp_name'], $upload_dir . $file_name)) {
        die("Failed to upload the file.");
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Begin a transaction to ensure both inserts are successful
    $pdo->beginTransaction();

    try {
        // Prepare SQL query to insert the user into the users table
        $sql_user = "INSERT INTO users (email, password, role) VALUES (?, ?, ?)";
        $stmt_user = $pdo->prepare($sql_user);
        $role = 'employer'; // Static role for now
        $stmt_user->bindParam(1, $email, PDO::PARAM_STR);
        $stmt_user->bindParam(2, $hashed_password, PDO::PARAM_STR);
        $stmt_user->bindParam(3, $role, PDO::PARAM_STR);

        // Execute the user insert
        if ($stmt_user->execute()) {
            // Get the user_id of the newly inserted user
            $user_id = $pdo->lastInsertId();

            // Prepare SQL query to insert the company information into the company table
            $sql_company = "INSERT INTO company (user_id, company_name, nature_of_business, company_type, company_location, contact_email, contact_person, document_file) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_company = $pdo->prepare($sql_company);
            $stmt_company->bindParam(1, $user_id, PDO::PARAM_INT);
            $stmt_company->bindParam(2, $company_name, PDO::PARAM_STR);
            $stmt_company->bindParam(3, $nature_of_business, PDO::PARAM_STR);
            $stmt_company->bindParam(4, $company_type, PDO::PARAM_STR);
            $stmt_company->bindParam(5, $company_location, PDO::PARAM_STR);
            $stmt_company->bindParam(6, $contact_email, PDO::PARAM_STR);
            $stmt_company->bindParam(7, $contact_person, PDO::PARAM_STR);
            $stmt_company->bindParam(8, $file_name, PDO::PARAM_STR);

            // Execute the company insert
            if ($stmt_company->execute()) {
                // Commit the transaction if both inserts are successful
                $pdo->commit();
                echo "Registration successful!";

                // Redirect to the login page after success
                header("Location: ../employer_login.php");
                exit;  // Ensure no further code is executed after the redirect
            } else {
                // Rollback the transaction if company insert fails
                $pdo->rollBack();
                echo "Error inserting company information: " . $pdo->errorInfo()[2];
            }
        } else {
            // Rollback the transaction if user insert fails
            $pdo->rollBack();
            echo "Error inserting user information: " . $pdo->errorInfo()[2];
        }
    } catch (Exception $e) {
        // Rollback the transaction on any exception
        $pdo->rollBack();
        echo "Transaction failed: " . $e->getMessage();
    }
}
