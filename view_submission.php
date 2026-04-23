<?php
include 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['ajax'])) {

    $q = $_GET['q'] ?? '';
    $search = "%$q%";

    if ($role == 'admin') {

        $stmt = $conn->prepare("
            SELECT users.name, assignments.title, submissions.file
            FROM submissions
            JOIN users ON submissions.user_id = users.id
            JOIN assignments ON submissions.assignment_id = assignments.id
            WHERE users.name LIKE ?
        ");
        $stmt->bind_param("s", $search);

    } else {

        $stmt = $conn->prepare("
            SELECT assignments.title, submissions.file
            FROM submissions
            JOIN assignments ON submissions.assignment_id = assignments.id
            WHERE submissions.user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {

            echo "<div class='card'>";

            if ($role == 'admin') {
                echo "<div class='badge'>Student</div>";
                echo "<h4>" . htmlspecialchars($row['name']) . "</h4>";
            }

            echo "<p><b>" . htmlspecialchars($row['title']) . "</b></p>";

            $fileName = basename($row['file']);
            echo "<div class='file-name'>📄 " . htmlspecialchars($fileName) . "</div>";

            echo "<div class='buttons'>";
            echo "<a class='view' href='uploads/" . urlencode($row['file']) . "' target='_blank'>View</a>";
            echo "<a class='download' href='uploads/" . urlencode($row['file']) . "' download>Download</a>";
            echo "</div>";

            echo "</div>";
        }

    } else {
        echo "<p>No submission found.</p>";
    }

    $stmt->close();
    exit();
}

include 'header.php';
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: #eef2f7;
}

.container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 20px;
}

.header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.header h2 {
    margin:0;
    font-weight:600;
    color:#1e3c72;
}

.search-box {
    width:250px;
}

.search-box input {
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:none;
    outline:none;
    background:#fff;
    box-shadow:0 3px 10px rgba(0,0,0,0.05);
}

#result {
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap:15px;
}

.card {
    background:#fff;
    border-radius:14px;
    padding:18px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
    transition:0.2s;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}

.card:hover {
    transform:translateY(-4px);
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

.badge {
    font-size:11px;
    background:#e8f0ff;
    color:#2a5298;
    padding:4px 10px;
    border-radius:20px;
    display:inline-block;
    margin-bottom:8px;
}

.card h4 {
    margin:0;
    font-size:15px;
    color:#333;
}

.card p {
    font-size:14px;
    color:#555;
    margin:6px 0;
}

.file-name {
    margin-top:8px;
    padding:8px;
    background:#f4f7fb;
    border-radius:8px;
    font-size:13px;
    color:#444;
}

.buttons {
    margin-top:12px;
    display:flex;
    gap:8px;
}

.buttons a {
    flex:1;
    text-align:center;
    padding:8px;
    border-radius:8px;
    font-size:13px;
    text-decoration:none;
    transition:0.2s;
}

.view {
    background:#1e3c72;
    color:white;
}

.view:hover {
    background:#16325c;
}

.download {
    background:#2a5298;
    color:white;
}

.download:hover {
    background:#1f417a;
}
</style>

<div class="container">

    <div class="header">
        <h2>📚 Submission List</h2>

        <?php if ($role == 'admin'): ?>
        <div class="search-box">
            <input type="text" id="search" placeholder="Search student name...">
        </div>
        <?php endif; ?>
    </div>

    <div id="result">
        <p>Loading...</p>
    </div>

</div>

<script>
function loadData(keyword = "") {

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "view_submission.php?ajax=1&q=" + encodeURIComponent(keyword), true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("result").innerHTML = xhr.responseText;
        }
    };

    xhr.send();
}

window.onload = function () {
    loadData();
};

let searchInput = document.getElementById("search");

if (searchInput) {
    let timeout;

    searchInput.addEventListener("keyup", function () {
        clearTimeout(timeout);

        timeout = setTimeout(() => {
            loadData(this.value);
        }, 300);
    });
}
</script>

<?php include 'footer.php'; ?>