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
    min-height: 60px; /* Bagi ketinggian tetap pada navbar */
}

/* Guna class .btn supaya kita tak kacau style asal Bootstrap sangat */
.navbar-custom .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 35px; /* Paksa semua butang sama tinggi */
    padding: 0 15px;
    border-radius: 5px; /* Samakan semua bucu butang */
    font-weight: 500;
    border: none;
    color: white !important;
}

/* Warna butang */
.btn-dashboard { background-color: #7b7dac; }
.btn-submit    { background-color: #1e639b; }
.btn-view      { background-color: #9c27b0; }
.btn-logout    { background-color: #f44336; }
.btn-create    { background-color: #8992ae; }
.btn-login     { background-color: #4caf50; }
.btn-register  { background-color: #00bcd4; }

/* Efek Hover */
.navbar-custom .btn:hover {
    filter: brightness(90%);
    opacity: 0.9;
}
</style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom py-2">
  <div class="container-fluid d-flex align-items-center justify-content-between">
    
    <a class="navbar-brand m-0" href="dashboard.php">Assignment Submission System</a>

    <div class="d-flex align-items-center gap-2">
      <?php if($user_id): ?>
        <a class="btn btn-sm btn-dashboard" href="dashboard.php">Dashboard</a>

        <?php if($role == 'admin'): ?>
          <a class="btn btn-sm btn-create" href="create_assignment.php">Create Assignment</a>
          <a class="btn btn-sm btn-view" href="view_submission.php">View Submission</a>
        <?php endif; ?>

        <?php if($role == 'student'): ?>
          <a class="btn btn-sm btn-submit" href="submit_assignment.php">Submit Assignment</a>
          <a class="btn btn-sm btn-view" href="view_submission.php">View Submission</a>
        <?php endif; ?>

        <a class="btn btn-sm btn-logout" href="logout.php">Logout</a>

      <?php else: ?>
        <a class="btn btn-sm btn-login" href="login.php">Login</a>
        <a class="btn btn-sm btn-register" href="register.php">Register</a>
      <?php endif; ?>
    </div>

  </div>
</nav>