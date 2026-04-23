<?php
// Start session
session_start();

// if logged in,go to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// not logged in,go to login page
header("Location: login.php");
exit();
?>
