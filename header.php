<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? null;
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Portal</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Segoe UI', Tahoma, sans-serif;
}

/* NAVBAR */
.navbar-custom{
    background: rgba(31, 42, 68, 0.9);
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    padding: 10px 20px;
}

/* BRAND */
.navbar-brand{
    font-weight:600;
    letter-spacing:0.5px;
    color:#fff !important;
}

/* BUTTON BASE */
.navbar-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:7px 14px;
    border-radius:20px;
    font-size:13px;
    font-weight:500;
    color:#fff !important;
    text-decoration:none;
    transition:0.2s ease;
    border:1px solid rgba(255,255,255,0.15);
    background:rgba(255,255,255,0.08);
}

/* hover effect (soft, bukan keras) */
.navbar-btn:hover{
    transform: translateY(-1px);
    background: rgba(255,255,255,0.18);
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
}

/* active */
.navbar-btn:active{
    transform: scale(0.98);
}

/* ROLE COLORS (lebih soft, bukan terang sangat) */
.btn-dashboard{ border-color:#4da3ff; }
.btn-create{ border-color:#7aa7ff; }
.btn-view{ border-color:#a78bfa; }
.btn-submit{ border-color:#60a5fa; }
.btn-logout{ border-color:#fb7185; }
.btn-login{ border-color:#34d399; }
.btn-register{ border-color:#22d3ee; }

/* spacing group */
.nav-group{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}
</style>

</head>

<body>

<nav class="navbar navbar-custom navbar-dark">
<div class="container-fluid d-flex justify-content-between align-items-center">

    <span class="navbar-brand">Assignment System</span>

    <div class="nav-group">

    <?php if($user_id): ?>

        <a class="navbar-btn btn-dashboard" href="dashboard.php">Dashboard</a>

        <?php if($role == 'admin'): ?>
            <a class="navbar-btn btn-create" href="create_assignment.php">Create</a>
            <a class="navbar-btn btn-view" href="view_submission.php">Submissions</a>
        <?php endif; ?>

        <?php if($role == 'student'): ?>
            <a class="navbar-btn btn-submit" href="submit_assignment.php">Submit</a>
            <a class="navbar-btn btn-view" href="view_submission.php">View</a>
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