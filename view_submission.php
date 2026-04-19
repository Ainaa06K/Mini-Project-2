<?php
include 'header.php';
include 'db.php';

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];

// all data admin pov
if ($role == 'admin') {
    $sql = "SELECT users.name, assignments.title, submissions.file_path
            FROM submissions
            JOIN users ON submissions.user_id = users.id
            JOIN assignments ON submissions.assignment_id = assignments.id";
    
    $result = $conn->query($sql);

    echo "<h2>All Submissions</h2>";

    while ($row = $result->fetch_assoc()) {
        echo "<p>
                Student: {$row['name']} <br>
                Assignment: {$row['title']} <br>
                <a href='{$row['file_path']}' download>Download</a>
              </p><hr>";
    }

// students data
} else {
    $stmt = $conn->prepare("SELECT assignments.title, submissions.file_path
                            FROM submissions
                            JOIN assignments ON submissions.assignment_id = assignments.id
                            WHERE submissions.user_id = ?");
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>My Submissions</h2>";

    while ($row = $result->fetch_assoc()) {
        echo "<p>
                Assignment: {$row['title']} <br>
                <a href='{$row['file_path']}' download>Download</a>
              </p><hr>";
    }
}
?>
