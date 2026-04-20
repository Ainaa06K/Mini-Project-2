<?php
/* ============================================================
   1. INITIALIZATION & SESSION
   ============================================================ */
include 'config.php'; 

// Ensure session is active before checking login status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

/* ============================================================
   2. SECURITY CHECK
   ============================================================ */
if (!$user_id) {
    header("Location: login.php");
    exit();
}

/* ============================================================
   3. AJAX SEARCH LOGIC (The "Stop" Gate)
   ============================================================ */
if (isset($_GET['ajax'])) {

    $q = $_GET['q'] ?? '';
    $search = "%$q%";

    /* ADMIN VIEW - Search by student name */
    if ($role == 'admin') {
        $stmt = $conn->prepare("
            SELECT users.name, assignments.title, submissions.file
            FROM submissions
            JOIN users ON submissions.user_id = users.id
            JOIN assignments ON submissions.assignment_id = assignments.id
            WHERE users.name LIKE ?
        ");
        $stmt->bind_param("s", $search);
    } 
    /* STUDENT VIEW - Only see own submissions */
    else {
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
            echo "<div class='card-sub'>";
            
            if ($role == 'admin') {
                echo "<div class='badge'>Student</div>";
                echo "<h4>" . htmlspecialchars($row['name']) . "</h4>";
            }

            echo "<div class='badge'>Assignment</div>";
            echo "<p>" . htmlspecialchars($row['title']) . "</p>";

            $fileName = basename($row['file']);
            echo "<div style='margin-top:5px; font-size:13px; color:#555;'>
                    📄 File: <b>" . htmlspecialchars($fileName) . "</b>
                  </div>";

            echo "<a class='btn-download' href='uploads/{$row['file']}' download>Download</a>";
            echo "</div>";
        }
    } else {
        echo "<p style='padding:20px;'>No results found.</p>";
    }

    // CRITICAL: Stop the script here so header.php doesn't load into the AJAX result!
    exit();
}

/* ============================================================
   4. NORMAL PAGE LOAD (Include the visual header here)
   ============================================================ */
include 'header.php'; 
?>

<style>
    body { background:#f4f6f9; font-family: Arial, sans-serif; margin: 0; }
    .container-box { max-width:900px; margin:30px auto; padding: 0 15px; }
    .title-box { background:#1f2a44; color:white; padding:15px; border-radius:10px; margin-bottom:20px; }
    .card-sub { background:white; padding:15px; margin-bottom:15px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08); transition: 0.2s; }
    .card-sub:hover { transform:scale(1.01); }
    .badge { display:inline-block; padding:4px 10px; border-radius:20px; background:#ddd; font-size:12px; margin-bottom:5px; color: #333; }
    .btn-download { display:inline-block; margin-top:10px; padding:8px 12px; background:#4caf50; color:white; text-decoration:none; border-radius:6px; font-size: 14px; }
    .btn-download:hover { background: #45a049; }
    #search { padding:10px; width:100%; max-width:300px; margin-bottom:20px; border: 1px solid #ccc; border-radius: 5px; }
</style>

<div class="container-box">
    <div class="title-box">
        <h3><?= ($role == 'admin') ? 'All Submissions' : 'My Submissions' ?></h3>
    </div>

    <?php if ($role == 'admin'): ?>
        <input type="text" id="search" placeholder="Search student name...">
    <?php endif; ?>

    <div id="result">
        <p>Loading submissions...</p>
    </div>
</div>

<script>
function loadData(keyword = "") {
    var xhr = new XMLHttpRequest();
    // Ensure the filename here matches this file's name
    xhr.open("GET", "view_submission.php?ajax=1&q=" + encodeURIComponent(keyword), true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("result").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

// Load data immediately on page load
window.onload = function () {
    loadData();
};

// Listen for typing in search box
let searchInput = document.getElementById("search");
if (searchInput) {
    let timeout;
    searchInput.addEventListener("keyup", function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            loadData(this.value);
        }, 300); // 300ms delay to prevent server spam
    });
}
</script>