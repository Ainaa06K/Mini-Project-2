<?php
include 'config.php';
include 'header.php';

$name = $_SESSION['name'] ?? 'User';
$role = $_SESSION['role'] ?? 'student';
?>

<style>
body {
    background: #f4f6f9;
    font-family: Arial;
}

.welcome-box {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 20px;
}

.card-box {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.btn-box {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.action-btn {
    flex: 1;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    text-decoration: none;
    color: white;
    font-weight: bold;
}

.submit-btn {
    background: #27ae60;
}

.view-btn {
    background: #2980b9;
}

.admin-btn {
    background: #f39c12;
}
</style>

<div class="container mt-4">

    <!-- WELCOME -->
    <div class="welcome-box">
        <h2>Welcome, <?= htmlspecialchars($name) ?></h2>
        <p>Role: <?= htmlspecialchars($role) ?></p>
    </div>

    <!-- ADMIN DASHBOARD -->
    <?php if($role == 'admin'): ?>

        <div class="card-box">
            <h3>Admin Panel</h3>
            <p>Manage system below</p>

            <div class="btn-box">
                <a href="create_assignment.php" class="action-btn admin-btn">
                    Create Assignment
                </a>

                <a href="view_submission.php" class="action-btn view-btn">
                    View Submission
                </a>
            </div>
        </div>

    <!-- STUDENT DASHBOARD -->
    <?php else: ?>

        <div class="card-box">
            <h3>Student Panel</h3>
            <p>Choose your action</p>

            <div class="btn-box">
                <a href="submit_assignment.php" class="action-btn submit-btn">
                    Submit Work
                </a>

                <a href="view_submission.php" class="action-btn view-btn">
                    View Submission
                </a>
            </div>
        </div>

    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>