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

/* BASE BUTTON STYLE */
.navbar-custom .btn {
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
    position: relative;
}

/* HOVER EFFECT BASIC (angkat button) */
.navbar-custom .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.25);
}

/* ACTIVE */
.navbar-custom .btn:active {
    transform: translateY(0px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

/* ===================== */
/* BUTTON COLORS + HOVER */
/* ===================== */

/* DASHBOARD */
.btn-dashboard { background-color: #7b7dac; }
.btn-dashboard:hover { background-color: #5c5f99; }

/* SUBMIT */
.btn-submit { background-color: #1e639b; }
.btn-submit:hover { background-color: #14486f; }

/* VIEW */
.btn-view { background-color: #9c27b0; }
.btn-view:hover { background-color: #7b1fa2; }

/* LOGOUT */
.btn-logout { background-color: #f44336; }
.btn-logout:hover { background-color: #c62828; }

/* CREATE */
.btn-create { background-color: #8992ae; }
.btn-create:hover { background-color: #6f7895; }

/* LOGIN */
.btn-login { background-color: #4caf50; }
.btn-login:hover { background-color: #2e7d32; }

/* REGISTER */
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

        <a class="btn btn-dashboard btn-sm" href="dashboard.php">Dashboard</a>

        <?php if($role == 'admin'): ?>
            <a class="btn btn-create btn-sm" href="create_assignment.php">Create Assignment</a>
            <a class="btn btn-view btn-sm" href="view_submission.php">View Submission</a>
        <?php endif; ?>

        <?php if($role == 'student'): ?>
            <a class="btn btn-submit btn-sm" href="submit_assignment.php">Submit Assignment</a>
            <a class="btn btn-view btn-sm" href="view_submission.php">View Submission</a>
        <?php endif; ?>

        <a class="btn btn-logout btn-sm" href="logout.php">Logout</a>

    <?php else: ?>

        <a class="btn btn-login btn-sm" href="login.php">Login</a>
        <a class="btn btn-register btn-sm" href="register.php">Register</a>

    <?php endif; ?>

    </div>

</div>
</nav>

<div class="container mt-3">