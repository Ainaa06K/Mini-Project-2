<?php
include 'header.php';
include 'db.php';

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];

// ajax
if (isset($_GET['ajax'])) {

    // admin search
    if ($role == 'admin') {
        $q = $_GET['q'] ?? '';
        $search = "%" . $q . "%";

        $stmt = $conn->prepare("
            SELECT users.name, assignments.title, submissions.file_path
            FROM submissions
            JOIN users ON submissions.user_id = users.id
            JOIN assignments ON submissions.assignment_id = assignments.id
            WHERE users.name LIKE ?
        ");
        $stmt->bind_param("s", $search);
    }

    // student view
    else {
        $stmt = $conn->prepare("
            SELECT assignments.title, submissions.file_path
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

            echo "<div style='border:1px solid #ccc; padding:10px; margin:5px;'>";

            if ($role == 'admin') {
                echo "<strong>Student:</strong> {$row['name']}<br>";
            }

            echo "<strong>Assignment:</strong> {$row['title']}<br>";
            echo "<a href='{$row['file_path']}' download>Download</a>";

            echo "</div>";
        }
    } else {
        echo "No results found";
    }

    exit(); 
}
?>

<!-- normal page ui  -->

<h2>View Submissions</h2>

<?php if ($role == 'admin'): ?>
    <input type="text" id="search" placeholder="Search student name..." style="padding:8px; width:300px;">
<?php endif; ?>

<div id="result"></div>

<script>
function loadData(keyword = "") {
    fetch("view_submission.php?ajax=1&q=" + keyword)
    .then(res => res.text())
    .then(data => {
        document.getElementById("result").innerHTML = data;
    });
}

// Load all data at start
window.onload = function() {
    loadData();
};

// Live search (admin only)
let timeout;
let searchInput = document.getElementById("search");

if (searchInput) {
    searchInput.addEventListener("keyup", function() {
        clearTimeout(timeout);
        let keyword = this.value;

        timeout = setTimeout(() => {
            loadData(keyword);
        }, 300);
    });
}
</script>
