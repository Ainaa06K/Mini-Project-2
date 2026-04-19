<?php
// Start session to access session data
session_start();

// Destroy all session data (log out user)
session_destroy();

// Redirect user back to login page
header("Location: login.php");
?>