<?php
session_start();
$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? null;
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Portal</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* NAVBAR */
.navbar-custom {
    background-color: #1f2a44;
    min-height: 60px;
}

/* BASE NAVBAR BUTTON (ISOLATED) */
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
    transition: all 0.2s ease;
    text-decoration: none;
}

/* HOVER EFFECT */
.navbar-custom .navbar-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.25);
}

/* ACTIVE PRESS */
.navbar-custom .navbar-btn:active {
    transform: translateY(0px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

/* COLORS */
.btn-dashboard { background-color: #7b7dac; }
.btn-dashboard:hover { background-color: #5c5f99; }

.btn-submit { background-color: #1e639b; }
.btn-submit:hover { background-color: #14486f; }

.btn-view { background-color: #9c27b0; }
.btn-view:hover { background-color: #7b1fa2; }

.btn-logout { background-color: #f44336; }
.btn-logout:hover { background-color: #c62828; }

.btn-create { background-color: #8992ae; }
.btn-create:hover { background-color: #6f7895; }

.btn-login { background-color: #4caf50; }
.btn-login:hover { background-color: #2e7d32; }

.btn-register { background-color: #00bcd4; }
.btn-register:hover { background-color: #008c9e; }
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