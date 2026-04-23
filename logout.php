<?php
// session data
session_start();

// destroy session data
session_destroy();

// redirect user 
header("Location: login.php");
?>
