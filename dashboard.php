<?php
include 'config.php';
include 'header.php';

$name = $_SESSION['name'] ?? 'User';
$user_id = $_SESSION['user_id'] ?? 0;

/* GET LATEST 4 SUBMISSIONS */
$sql = "SELECT * FROM submissions ORDER BY id DESC LIMIT 4";
$result = $conn->query($sql);
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

.action-btn {
    display: inline-block;
    padding: 12px 18px;
    border-radius: 10px;
    text-align: center;
    text-decoration: none;
    color: white;
    font-weight: bold;
    margin-top: 15px;
}

.more-btn {
    background: #8e44ad;
}

.more-btn:hover {
    background: #6d2c91;
    color: white;
}

/* table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th, td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}
</style>

<div class="container mt-4">

    <!-- WELCOME -->
    <div class="welcome-box">
        <h2>Welcome, <?= htmlspecialchars($name) ?></h2>
        <p>Latest submissions overview</p>
    </div>

    <!-- LATEST SUBMISSIONS -->
    <div class="card-box">
        <h3>Recent Submissions (Latest 4)</h3>

        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Date</th>
            </tr>

            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['title'] ?? '-') ?></td>
                <td><?= $row['created_at'] ?? '-' ?></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <!-- VIEW ALL BUTTON -->
        <a href="view_submission.php" class="action-btn more-btn">
            View All Submission
        </a>
    </div>

</div>

<?php include 'footer.php'; ?>