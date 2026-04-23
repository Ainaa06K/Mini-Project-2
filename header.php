<?php
// Start the session at the very top so we can check if the user is an Admin or Student
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Grab the role and user ID from the session to decide which buttons to show
$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? null;
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Portal</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* Main Navbar styling - using a dark professional blue */
.navbar-custom {
    background-color: #1f2a44;
    min-height: 60px;
}

/* Base style for all nav buttons to keep them consistent */
.navbar-custom .navbar-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 35px;
    padding: 0 15px;
    border-radius: 6px;
    font-weight: 500;
    border: none;
    color: white !important;
    transition: all 0.2s ease; /* Smooth transition for the hover effects */
    text-decoration: none;
}

/* Give buttons a little "pop" when hovering */
.navbar-custom .navbar-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.25);
}

/* Feedback when the button is clicked */
.navbar-custom .navbar-btn:active {
    transform: translateY(0px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

/* Different colors for different actions so the UI is easy to navigate */
.btn-dashboard { background-color: #7b7dac; }
.btn-submit { background-color: #1e639b; }
.btn-view { background-color: #9c27b0; }
.btn-logout { background-color: #f44336; }
.btn-create { background-color: #8992ae; }
.btn-login { background-color: #4caf50; }
.btn-register { background-color: #00bcd4; }
</style>
</head>

<body>

<nav class="navbar navbar-dark navbar-custom">
<div class="container-fluid d-flex justify-content-between align-items-center">

    <span class="navbar-brand mb-0">Assignment Submission System</span>

    <div class="d-flex align-items-center gap-2">

    <?php if($user_id): ?>
        <a class="navbar-btn btn-dashboard" href="dashboard.php">Dashboard</a>

        <?php if($role == 'admin'): ?>
            <a class="navbar-btn btn-create" href="create_assignment.php">Create Assignment</a>
            <a class="navbar-btn btn-view" href="view_submission.php">View Submission</a>
        <?php endif; ?>

        <?php if($role == 'student'): ?>
            <a class="navbar-btn btn-submit" href="submit_assignment.php">Submit Assignment</a>
            <a class="navbar-btn btn-view" href="view_submission.php">View Submission</a>
        <?php endif; ?>

        <a class="navbar-btn btn-logout" href="logout.php">Logout</a>

    <?php else: ?>
        <a class="navbar-btn btn-login" href="login.php">Login</a>
        <a class="navbar-btn btn-register" href="register.php">Register</a>

    <?php endif; ?>

    </div>

</div>
</nav>

<div class="container mt-3">
