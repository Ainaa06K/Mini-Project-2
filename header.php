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
body { 
    background: linear-gradient(to right, #eef2f3, #e7eff8); 
}

.navbar-custom { 
    background-color:#2c3e50; 
}
</style>
</head>

<body>

<nav class="navbar navbar-dark navbar-custom">
<div class="container-fluid">

<span class="navbar-brand">Assignment Submission System</span>

<div>

<?php if($user_id): ?>

    <a class="btn btn-light btn-sm" href="dashboard.php">Dashboard</a>

    <?php if($role == 'admin'): ?>
        <a class="btn btn-warning btn-sm" href="create_assignment.php">Create Assignment</a>
        <a class="btn btn-info btn-sm" href="view_submission.php">View Submission</a>
    <?php endif; ?>

    <?php if($role == 'student'): ?>
        <a class="btn btn-success btn-sm" href="submit_assignment.php">Submit Assignment</a>
        <a class="btn btn-info btn-sm" href="view_submission.php">View Submission</a>
    <?php endif; ?>

    <a class="btn btn-danger btn-sm" href="logout.php">Logout</a>

<?php else: ?>

    <a class="btn btn-success btn-sm" href="login.php">Login</a>
    <a class="btn btn-primary btn-sm" href="register.php">Register</a>

<?php endif; ?>

</div>

</div>
</nav>

<div class="container mt-3">