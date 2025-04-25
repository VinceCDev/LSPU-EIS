<?php
$host = 'localhost';
$dbname = 'alumni_tracing_and_placement_db';
$username = 'root';
$password = '';

// Create MySQLi connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
