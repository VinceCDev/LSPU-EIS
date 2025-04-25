<?php
// Include the database connection
require_once '../conn/db_conn.php'; // Adjust the path based on your file structure

function registerEmployer($formData)
{
    global $conn;  // Assuming $conn is your database connection

    // Retrieve form data
    $email = $formData['email'];
    $password = $formData['password'];
    $company_name = $formData['company_name'];
    $company_location = $formData['company_location'];
    $contact_email = $formData['contact_email'];
    $contact_number = $formData['contact_number'];
    $industry_type = $formData['industry_type'];
    $nature_of_business = $formData['nature_of_business'];
    $tin = $formData['tin'];
    $date_established = $formData['date_established'];
    $company_type = $formData['company_type'];
    $accreditation_status = $formData['accreditation_status'];
    $document_file = $_FILES['document_file']['name'];  // Handle file upload for documents
    $logo_file = $_FILES['company_logo']['name'];  // Handle file upload for logo
    $document_tmp = $_FILES['document_file']['tmp_name'];
    $logo_tmp = $_FILES['company_logo']['tmp_name'];

    // Password hash
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Step 1: Insert into `users` table using prepared statement
        $insertUserQuery = "INSERT INTO users (email, password, role) VALUES (?, ?, 'employer')";
        $stmt = mysqli_prepare($conn, $insertUserQuery);
        mysqli_stmt_bind_param($stmt, "ss", $email, $hashed_password);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error inserting into users table: " . mysqli_error($conn));
        }

        // Get the last inserted user id
        $user_id = mysqli_insert_id($conn);

        // Step 2: Handle logo upload (if exists)
        if ($logo_file) {
            $upload_dir = '../uploads/logos/';
            $logo_path = $upload_dir . basename($logo_file);
            if (!move_uploaded_file($logo_tmp, $logo_path)) {
                throw new Exception("Error uploading logo.");
            }
        } else {
            $logo_path = NULL;  // No logo uploaded
        }

        // Step 3: Handle document upload (if exists)
        if ($document_file) {
            $document_dir = '../uploads/documents/';
            $document_path = $document_dir . basename($document_file);
            if (!move_uploaded_file($document_tmp, $document_path)) {
                throw new Exception("Error uploading document.");
            }
        } else {
            $document_path = NULL;  // No document uploaded
        }

        // Step 4: Insert into `company_profile` table using prepared statement
        $insertCompanyQuery = "INSERT INTO company_profile 
        (user_id, company_name, company_logo, company_location, contact_email, contact_number, 
        industry_type, nature_of_business, tin, date_established, company_type, accreditation_status, document_file) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $insertCompanyQuery);
        mysqli_stmt_bind_param(
            $stmt,
            "issssssssssss",
            $user_id,
            $company_name,
            $logo_path,
            $company_location,
            $contact_email,
            $contact_number,
            $industry_type,
            $nature_of_business,
            $tin,
            $date_established,
            $company_type,
            $accreditation_status,
            $document_path
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error inserting into company_profile table: " . mysqli_error($conn));
        }

        // Commit transaction
        mysqli_commit($conn);

        return true; // Successfully registered
    } catch (Exception $e) {
        // Rollback transaction if there was an error
        mysqli_rollback($conn);  // This is the correct function
        return $e->getMessage(); // Return the error message
    }
}
