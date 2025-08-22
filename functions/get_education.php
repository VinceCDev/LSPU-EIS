<?php
session_start();
include '../conn/db_conn.php'; // Assuming you have a db connection file

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set header to tell the client that the response is JSON
header('Content-Type: application/json');

// Check if the user is logged in by checking session email
if (isset($_SESSION['email'])) {
    // Get the email from the session
    $email = $_SESSION['email'];

    // Step 1: Get user_id from users table based on the email
    $userQuery = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("s", $email); // Bind the email parameter
    $stmt->execute();
    $userResult = $stmt->get_result();

    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
        $user_id = $user['id']; // Get the user_id

        // Step 2: Get alumni_id from alumni_profile table using user_id
        $profileQuery = "SELECT alumni_id FROM alumni_profile WHERE user_id = ?";
        $stmt = $conn->prepare($profileQuery);
        $stmt->bind_param("i", $user_id); // Bind the user_id parameter
        $stmt->execute();
        $profileResult = $stmt->get_result();

        if ($profileResult->num_rows > 0) {
            $profile = $profileResult->fetch_assoc();
            $alumni_id = $profile['alumni_id']; // Get the alumni_id

            // Step 3: Get education data from alumni_education table
            $educationQuery = "SELECT * FROM alumni_education WHERE alumni_id = ?";
            $stmt = $conn->prepare($educationQuery);
            $stmt->bind_param("i", $alumni_id); // Bind alumni_id parameter
            $stmt->execute();
            $educationResult = $stmt->get_result();

            if ($educationResult->num_rows > 0) {
                $educationData = [];
                while ($row = $educationResult->fetch_assoc()) {
                    $educationData[] = $row; // Collect all education data
                }
                echo json_encode([
                    "status" => "success",
                    "education" => $educationData
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "No education data found."
                ]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "No matching alumni profile found"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No matching user found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
}
