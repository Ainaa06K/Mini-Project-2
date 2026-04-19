<?php
// Start session
session_start();

// If already logged in → go dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// If not logged in → go login
header("Location: login.php");
exit();
?>