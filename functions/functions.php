<?php
require_once '../conn/db_conn.php'; // Adjust path if needed

// Admin credentials
$admin_email = 'admin@example.com';
$admin_password_plain = 'admin123'; // Plain password
$admin_role = 'admin';

// Hash the password
$admin_password_hashed = password_hash($admin_password_plain, PASSWORD_DEFAULT);

// Check if admin already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "Admin already exists.";
} else {
    // Insert admin
    $insert = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
    $insert->bind_param("sss", $admin_email, $admin_password_hashed, $admin_role);

    if ($insert->execute()) {
        echo "Admin account created successfully!<br>";
        echo "Email: " . $admin_email . "<br>";
        echo "Password: " . $admin_password_plain;
    } else {
        echo "Error creating admin: " . $conn->error;
    }
}
