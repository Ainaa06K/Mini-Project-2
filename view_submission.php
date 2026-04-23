<?php
/**
 * Dashboard for viewing submissions. 
 * Handles both Admin (see everyone) and Student (see own) views.
 */

include 'db.php'; 

// Make sure we know who is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kick them out if they aren't logged in
$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit();
}

/* ---------------------------------------------------------
   AJAX STUFF
   This part only runs when the JavaScript asks for data.
   --------------------------------------------------------- */
if (isset($_GET['ajax'])) {

    $q = $_GET['q'] ?? '';
    $search = "%$q%"; // Wrap with % for the SQL LIKE query

    // If admin, they can search by name. Otherwise, student just gets their own list.
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

    // Loop through results and build the cards
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            
            echo "<div class='card-sub'>";

            if ($role == 'admin') {
                echo "<div class='badge'>Student</div>";
                echo "<h4>" . htmlspecialchars($row['name']) . "</h4>";
            }

            echo "<div class='badge'>Assignment</div>";
            echo "<p>" . htmlspecialchars($row['title']) . "</p>";

            // Clean up the file name so it looks nice
            $fileName = basename($row['file']);
            echo "<div style='margin-top:5px; font-size:13px; color:#555;'>
                    📄 File: <b>" . htmlspecialchars($fileName) . "</b>
                  </div>";

            echo "<div style='margin-top:10px;'>";
            // View opens in new tab, Download triggers the browser download
            echo "<a class='btn-view' href='uploads/" . urlencode($row['file']) . "' target='_blank'>View</a>";
            echo "<a class='btn-download' href='uploads/" . urlencode($row['file']) . "' download>Download</a>";
            echo "</div>";

            echo "</div>";
        }
    } else {
        echo "<p style='padding:20px;'>Nothing found!</p>";
    }

    $stmt->close();
    exit(); // Don't load the rest of the HTML if this is an AJAX call
}

include 'header.php'; 
?>

<style>
/* CSS for the submission cards and search box */
/* ... (Keep your styles here) ... */
</style>

<div class="container-box">

    <div class="title-box">
        <h3><?= ($role == 'admin') ? 'All Submissions' : 'My Submissions' ?></h3>
    </div>

    <?php if ($role == 'admin'): ?>
        <input type="text" id="search" placeholder="Search student name...">
    <?php endif; ?>

    <div id="result">
        <p>Loading...</p>
    </div>

</div>

<script>
// Main function to fetch data without refreshing
function loadData(keyword = "") {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "view_submission.php?ajax=1&q=" + encodeURIComponent(keyword), true);

    xhr.onreadystatechange = function () {
        // 4 means "done" and 200 means "success"
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("result").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

// Load the list immediately when page opens
window.onload = function () {
    loadData();
};

// Handle the search bar typing
let searchInput = document.getElementById("search");
if (searchInput) {
    let timeout;
    searchInput.addEventListener("keyup", function () {
        // Wait 300ms after user stops typing so we don't spam the database
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            loadData(this.value);
        }, 300);
    });
}
</script>

<?php include 'footer.php'; ?>
