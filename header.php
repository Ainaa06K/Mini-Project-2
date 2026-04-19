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
        .navbar-custom {
            background-color: #1f2a44;
            padding: 10px 0; /* Tambah ruang atas bawah */
        }

        /* Grouping buttons and forcing alignment */
        .nav-buttons {
            display: flex;
            align-items: center; /* Paksa butang duduk tengah secara menegak */
            gap: 10px; /* Jarak antara butang */
        }

        /* Custom Button Styles */
        .btn-custom {
            border: none;
            color: white !important;
            font-size: 14px;
            padding: 6px 15px;
            border-radius: 6px;
            transition: 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            height: 36px; /* Paksa semua butang sama tinggi */
        }

        .btn-dashboard { background-color: #7b7dac; }
        .btn-dashboard:hover { background-color: #3a3f68; }

        .btn-create { background-color: #8992ae; }
        .btn-create:hover { background-color: #27436f; }

        .btn-submit { background-color: #1e639b; }
        .btn-submit:hover { background-color: #1976d2; }

        .btn-view { background-color: #9c27b0; }
        .btn-view:hover { background-color: #7b1fa2; }

        .btn-logout { background-color: #f44336; }
        .btn-logout:hover { background-color: #d32f2f; }

        .btn-login { background-color: #4caf50; }
        .btn-register { background-color: #00bcd4; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom">
    <div class="container-fluid d-flex align-items-center justify-content-between">
        
        <a class="navbar-brand m-0" href="dashboard.php">Assignment Submission System</a>

        <div class="nav-buttons">
            <?php if($user_id): ?>
                <a class="btn-custom btn-dashboard" href="dashboard.php">Dashboard</a>

                <?php if($role == 'admin'): ?>
                    <a class="btn-custom btn-create" href="create_assignment.php">Create Assignment</a>
                    <a class="btn-custom btn-view" href="view_submission.php">View Submission</a>
                <?php endif; ?>

                <?php if($role == 'student'): ?>
                    <a class="btn-custom btn-submit" href="submit_assignment.php">Submit Assignment</a>
                    <a class="btn-custom btn-view" href="view_submission.php">View Submission</a>
                <?php endif; ?>

                <a class="btn-custom btn-logout" href="logout.php">Logout</a>
            <?php else: ?>
                <a class="btn-custom btn-login" href="login.php">Login</a>
                <a class="btn-custom btn-register" href="register.php">Register</a>
            <?php endif; ?>
        </div>

    </div>
</nav>