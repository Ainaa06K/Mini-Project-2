<?php
include 'db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if user not authenticated
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// Get session data for access control
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$name = $_SESSION['name'] ?? 'User';

// Handle submission deletion
if(isset($_POST['delete_submission_id'])){

    $sid = $_POST['delete_submission_id'];

    // Get file info and owner validation
    $stmt = $conn->prepare("SELECT file, user_id FROM submissions WHERE id=?");
    $stmt->bind_param("i", $sid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if($res){

        // Only admin or owner can delete submission
        if($role != 'admin' && $res['user_id'] != $user_id){
            exit("Unauthorized");
        }

        $file = $res['file'];

        // Delete record from database
        $stmt = $conn->prepare("DELETE FROM submissions WHERE id=?");
        $stmt->bind_param("i", $sid);
        $stmt->execute();

        // Remove file from server
        $path = "uploads/" . $file;
        if(!empty($file) && file_exists($path)){
            unlink($path);
        }
    }
}

// Handle assignment deletion (admin only)
if(isset($_POST['delete_assignment_id'])){

    $aid = $_POST['delete_assignment_id'];

    if($role == 'admin'){

        // Get all related submission files first
        $stmt = $conn->prepare("SELECT file FROM submissions WHERE assignment_id=?");
        $stmt->bind_param("i", $aid);
        $stmt->execute();
        $result = $stmt->get_result();

        // Delete files from storage
        while($row = $result->fetch_assoc()){
            $path = "uploads/" . $row['file'];
            if(!empty($row['file']) && file_exists($path)){
                unlink($path);
            }
        }

        // Delete submissions from database
        $stmt = $conn->prepare("DELETE FROM submissions WHERE assignment_id=?");
        $stmt->bind_param("i", $aid);
        $stmt->execute();

        // Delete assignment record
        $stmt = $conn->prepare("DELETE FROM assignments WHERE id=?");
        $stmt->bind_param("i", $aid);
        $stmt->execute();
    }
}

// AJAX search handler for assignments
if(isset($_GET['ajax'])){

    $q = "%" . ($_GET['q'] ?? '') . "%";

    // Search assignments by title
    $stmt = $conn->prepare("SELECT * FROM assignments WHERE title LIKE ? ORDER BY id DESC");
    $stmt->bind_param("s", $q);
    $stmt->execute();
    $assignments = $stmt->get_result();

    while($a = $assignments->fetch_assoc()){
        $aid = $a['id'];
?>

<div class="assignment-header">
    <div class="left-box">

        <!-- Assignment title (click to expand) -->
        <div class="assignment-title" data-bs-toggle="collapse" data-bs-target="#a<?= $aid ?>">
            <?= htmlspecialchars($a['title']); ?>
        </div>

        <!-- Assignment description -->
        <div class="assignment-desc">
            <?= htmlspecialchars($a['description'] ?? 'No description'); ?>
        </div>
    </div>

    <?php if($role == 'admin'){ ?>
    <!-- Admin delete assignment button -->
    <button class="btn btn-danger btn-sm" onclick="openPopupAssign(<?= $aid ?>)">
        Delete Assignment
    </button>
    <?php } ?>

</div>

<div id="a<?= $aid ?>" class="collapse">
    <div class="p-3 border">

<?php
        // Load submissions based on role
        if($role == 'admin'){
            $stmt2 = $conn->prepare("
                SELECT submissions.id, submissions.file, users.name, submissions.user_id
                FROM submissions
                JOIN users ON submissions.user_id = users.id
                WHERE assignment_id=?
            ");
            $stmt2->bind_param("i", $aid);
        } else {
            $stmt2 = $conn->prepare("
                SELECT submissions.id, submissions.file, users.name, submissions.user_id
                FROM submissions
                JOIN users ON submissions.user_id = users.id
                WHERE assignment_id=? AND user_id=?
            ");
            $stmt2->bind_param("ii", $aid, $user_id);
        }

        $stmt2->execute();
        $res = $stmt2->get_result();

        // Display submission list
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $sid = $row['id'];
?>

<div class="file-item">

    <div>
        <!-- Student name -->
        👤 <b><?= htmlspecialchars($row['name']); ?></b><br>
        <!-- File name -->
        📄 <?= htmlspecialchars($row['file']); ?>
    </div>

    <div>
        <!-- View file -->
        <a href="uploads/<?= urlencode($row['file']); ?>" target="_blank" class="btn btn-primary btn-sm">
            View
        </a>

        <?php if($role == 'admin' || $row['user_id'] == $user_id){ ?>
        <!-- Delete submission -->
        <button class="btn btn-danger btn-sm" onclick="openPopup(<?= $sid ?>)">
            Delete
        </button>
        <?php } ?>
    </div>

</div>

<?php } } else { ?>
    <p class="text-muted">No submission yet</p>
<?php } ?>

    </div>
</div>

<?php } exit(); } ?>

<?php include 'header.php'; ?>