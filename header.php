<!DOCTYPE html>
<html>
<head>
    <!-- Page title shown in browser tab -->
<title>Student Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: linear-gradient(to right, #eef2f3, #e7eff8); }
.main-card { max-width:600px; margin:auto; margin-top:50px; border-radius:15px; box-shadow:0 4px 15px rgba(0,0,0,0.1);}
.navbar-custom { background-color:#85a9c9; }
</style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom">
<div class="container-fluid">
<span class="navbar-brand">Student Portal</span>
<div>
 <!-- Check if user is logged in -->
<?php if(isset($_SESSION['user_id'])): ?>
    <!-- If logged in: show Dashboard and Logout buttons -->
<a class="btn btn-light btn-sm" href="dashboard.php">Dashboard</a>
<a class="btn btn-danger btn-sm" href="logout.php">Logout</a>
 <!-- If NOT logged in: show Login and Register buttons -->
<?php else: ?>
<a class="btn btn-success btn-sm" href="login.php">Login</a>
<a class="btn btn-primary btn-sm" href="register.php">Register</a>
<?php endif; ?>
</div>
</div>
</nav>

<div class="container"></div>