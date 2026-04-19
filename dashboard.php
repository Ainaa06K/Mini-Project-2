<?php
include 'config.php';
include 'header.php';

$name = $_SESSION['name'] ?? 'User';

$query = "SELECT u.name, a.title, s.file 
          FROM submissions s
          JOIN users u ON s.user_id = u.id
          JOIN assignments a ON s.assignment_id = a.id";

$result = $conn->query($query);
?>

<style>
body {
    background: #f4f6f9;
    font-family: Arial, sans-serif;
}

.container {
    margin-top: 30px;
}

.card-box {
    background: #ffffff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

h2 {
    color: #2c3e50;
    margin-bottom: 5px;
}

h3 {
    color: #34495e;
    margin-top: 0;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #ffffff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

th {
    background: #2c3e50;
    color: #ffffff;
    text-align: left;
    padding: 12px;
}

td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

tr:hover {
    background: #f2f2f2;
}

a {
    color: #2c3e50;
    text-decoration: none;
    font-weight: bold;
}

a:hover {
    text-decoration: underline;
}
</style>

<div class="container">

    <div class="card-box">
        <h2>Welcome, <?= htmlspecialchars($name) ?></h2>
        <h3>Dashboard Submissions</h3>
    </div>

    <div class="card-box">
        <table>
            <tr>
                <th>Student</th>
                <th>Assignment</th>
                <th>File</th>
            </tr>

            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td>
                    <a href="uploads/<?= htmlspecialchars($row['file']) ?>" download>
                        Download
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</div>

<?php include 'footer.php'; ?>