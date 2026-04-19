<?php
// Connect to database
$conn = new mysqli("localhost", "root", "", "assignment_system");

// Check database connection
// If connection fails, stop the system and show error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session (used for login system and user data)
session_start();
?>