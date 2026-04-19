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
}

/* DASHBOARD - Grey Blue */
.btn-dashboard {
    background-color: #7b7dac;
    color: white;
    border: none;
}
.btn-dashboard:hover {
    background-color: #3a3f68;
    color: white;
}

/* CREATE ASSIGNMENT - Orange */
.btn-create {
    background-color: #8992ae;
    color: white;
    border: none;
}
.btn-create:hover {
    background-color: #27436f;
    color: white;
}

/* SUBMIT ASSIGNMENT - Blue */
.btn-submit {
    background-color: #1e639b;
    color: white;
    border: none;
}
.btn-submit:hover {
    background-color: #5d9ddc;
    color: white;
}

/* VIEW SUBMISSION - Purple */
.btn-view {
    background-color: #9c27b0;
    color: white;
    border: none;
}
.btn-view:hover {
    background-color: #7b1fa2;
    color: white;
}

/* LOGIN - Green */
.btn-login {
    background-color: #4caf50;
    color: white;
    border: none;
}
.btn-login:hover {
    background-color: #388e3c;
    color: white;
}

/* REGISTER - Teal */
.btn-register {
    background-color: #00bcd4;
    color: white;
    border: none;
}
.btn-register:hover {
    background-color: #0097a7;
    color: white;
}

/* LOGOUT - Red */
.btn-logout {
    background-color: #f44336;
    color: white;
    border: none;
}
.btn-logout:hover {
    background-color: #d32f2f;
    color: white;
}

/* smooth */
.btn {
    transition: 0.3s;
}
</style>
</head>

<body>

<nav class="navbar navbar-dark navbar-custom">
<div class="container-fluid">

<span class="navbar-brand">Assignment Submission System</span>

<div>

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