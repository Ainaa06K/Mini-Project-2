<?php
include 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$name = $_SESSION['name'] ?? 'User';

/* =========================
   AJAX SEARCH MODE
========================= */
if(isset($_GET['ajax'])){

    $q = "%" . ($_GET['q'] ?? '') . "%";

    // ambil assignment ikut search
    $stmtA = $conn->prepare("SELECT * FROM assignments WHERE title LIKE ? ORDER BY id DESC");
    $stmtA->bind_param("s", $q);
    $stmtA->execute();
    $assignments = $stmtA->get_result();

    while($a = $assignments->fetch_assoc()){
        $aid = $a['id'];
?>

<div class="assignment-header">
    <div class="left-box">
        <div class="assignment-title" data-bs-toggle="collapse" data-bs-target="#a<?= $aid; ?>">
            <?= htmlspecialchars($a['title']); ?>
        </div>
        <div class="assignment-desc">
            <?= htmlspecialchars($a['description'] ?? 'No description'); ?>
        </div>
    </div>

    <?php if($role == 'admin'){ ?>
    <button class="btn-del" data-bs-toggle="modal" data-bs-target="#delA<?= $aid; ?>">
        Delete Assignment
    </button>
    <?php } ?>
</div>

<div id="a<?= $aid; ?>" class="collapse">
    <div class="p-3 border">

<?php
        // load submission
        if ($role == 'admin') {
            $stmt = $conn->prepare("
                SELECT submissions.id, submissions.file, users.name, submissions.user_id 
                FROM submissions 
                JOIN users ON submissions.user_id = users.id 
                WHERE submissions.assignment_id=?
            ");
            $stmt->bind_param("i", $aid);
        } else {
            $stmt = $conn->prepare("
                SELECT submissions.id, submissions.file, users.name, submissions.user_id 
                FROM submissions 
                JOIN users ON submissions.user_id = users.id 
                WHERE submissions.assignment_id=? AND submissions.user_id=?
            ");
            $stmt->bind_param("ii", $aid, $user_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $sid = $row['id'];
?>

<div class="file-item">
    <div>
        👤 <b><?= htmlspecialchars($row['name']); ?></b><br>
        📄 <?= htmlspecialchars($row['file']); ?>
    </div>

    <div>
        <a href="uploads/<?= urlencode($row['file']); ?>" target="_blank" class="btn btn-primary btn-sm">
            View
        </a>

        <?php if($role == 'admin' || $row['user_id'] == $user_id){ ?>
        <form method="POST" action="delete_submission.php" style="display:inline;">
            <input type="hidden" name="submission_id" value="<?= $sid; ?>">
            <button class="btn btn-danger btn-sm">Delete</button>
        </form>
        <?php } ?>
    </div>
</div>

<?php } } else { ?>
<p class="text-muted">No submission yet</p>
<?php } ?>

    </div>
</div>

<?php } // end assignment loop ?>

<?php
    exit();
}
?>

<?php include 'header.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{ background:#f4f6f9; font-family: Arial; }
.welcome{ background:#2c3e50; color:white; padding:20px; border-radius:10px; }
.role-box{ margin-top:8px; display:inline-block; padding:5px 12px; border-radius:20px; font-size:14px; background:#f39c12; color:white; }
.box{ background:white; padding:20px; border-radius:10px; margin-top:20px; }
.assignment-header{ display:flex; justify-content:space-between; padding:12px; border:1px solid #eee; border-radius:8px; margin-bottom:5px; background:#fff; }
.assignment-title{ font-weight:600; cursor:pointer; }
.assignment-desc{ font-size:13px; color:#666; }
.left-box{ display:flex; flex-direction:column; }
.file-item{ display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #eee; }
.btn-del{ background:#e74c3c; color:white; padding:5px 10px; border:none; border-radius:6px; }
#search{ margin-bottom:15px; padding:10px; width:100%; max-width:300px; }
</style>

<div class="container mt-3">

    <div class="welcome">
        <h3>Welcome <?= htmlspecialchars($name); ?></h3>
        <div class="role-box">Role: <?= ucfirst($role); ?></div>
    </div>

    <div class="box">
        <h4>Assignments</h4>

        <!-- SEARCH -->
        <input type="text" id="search" placeholder="Search assignment...">

        <!-- RESULT -->
        <div id="result">
            Loading...
        </div>

    </div>
</div>

<script>
function loadData(keyword = "") {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "dashboard.php?ajax=1&q=" + encodeURIComponent(keyword), true);

    xhr.onload = function(){
        document.getElementById("result").innerHTML = this.responseText;
    };

    xhr.send();
}

window.onload = function(){
    loadData();
};

let search = document.getElementById("search");

search.addEventListener("keyup", function(){
    let val = this.value;

    clearTimeout(this.timer);
    this.timer = setTimeout(() => {
        loadData(val);
    }, 300);
});
</script>

<?php include 'footer.php'; ?>