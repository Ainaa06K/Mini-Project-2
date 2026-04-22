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
   DELETE SUBMISSION
========================= */
if(isset($_POST['delete_submission_id'])){

    $sid = $_POST['delete_submission_id'];

    if($role == 'admin'){
        $stmt = $conn->prepare("DELETE FROM submissions WHERE id=?");
        $stmt->bind_param("i", $sid);
    } else {
        $stmt = $conn->prepare("DELETE FROM submissions WHERE id=? AND user_id=?");
        $stmt->bind_param("ii", $sid, $user_id);
    }

    $stmt->execute();
}


/* =========================
   ADD THIS 👉 DELETE ASSIGNMENT
========================= */
if(isset($_POST['delete_assignment_id'])){

    $aid = $_POST['delete_assignment_id'];

    if($role == 'admin'){

        // delete submissions first
        $conn->query("DELETE FROM submissions WHERE assignment_id=$aid");

        // delete assignment
        $stmt = $conn->prepare("DELETE FROM assignments WHERE id=?");
        $stmt->bind_param("i", $aid);
        $stmt->execute();
    }
}


/* =========================
   AJAX SEARCH
========================= */
if(isset($_GET['ajax'])){

    $q = "%" . ($_GET['q'] ?? '') . "%";

    $stmt = $conn->prepare("SELECT * FROM assignments WHERE title LIKE ? ORDER BY id DESC");
    $stmt->bind_param("s", $q);
    $stmt->execute();
    $assignments = $stmt->get_result();

    while($a = $assignments->fetch_assoc()){
        $aid = $a['id'];
?>

<div class="assignment-header">

    <div class="left-box">
        <div class="assignment-title"
             data-bs-toggle="collapse"
             data-bs-target="#a<?= $aid ?>">
            <?= htmlspecialchars($a['title']); ?>
        </div>

        <div class="assignment-desc">
            <?= htmlspecialchars($a['description'] ?? 'No description'); ?>
        </div>
    </div>

    <!-- ADD DELETE ASSIGNMENT BUTTON -->
    <?php if($role == 'admin'){ ?>
    <button class="btn btn-danger btn-sm"
            onclick="openPopupAssign(<?= $aid ?>)">
        Delete Assignment
    </button>
    <?php } ?>

</div>

<div id="a<?= $aid ?>" class="collapse">
    <div class="p-3 border">

<?php
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

        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $sid = $row['id'];
?>

<div class="file-item">

    <div>
        👤 <b><?= htmlspecialchars($row['name']); ?></b><br>
        📄 <?= htmlspecialchars($row['file']); ?>
    </div>

    <div>
        <a href="uploads/<?= urlencode($row['file']); ?>"
           target="_blank"
           class="btn btn-primary btn-sm">
            View
        </a>

        <?php if($role == 'admin' || $row['user_id'] == $user_id){ ?>
        <button class="btn btn-danger btn-sm"
                onclick="openPopup(<?= $sid ?>)">
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

<style>
body{
    background:#f4f6f9;
    font-family:Arial;
}

.box{
    max-width:900px;
    margin:auto;
    padding:20px;
}

.welcome{
    background: linear-gradient(135deg, #2c3e50, #3498db);
    color:white;
    padding:20px;
    border-radius:10px;
}

.role-box{
    margin-top:8px;
    display:inline-block;
    padding:5px 12px;
    background:#f39c12;
    border-radius:20px;
    font-size:13px;
}

.assignment-header{
    display:flex;
    justify-content:space-between;
    padding:12px;
    border:1px solid #eee;
    border-radius:8px;
    margin-top:10px;
    background:#fff;
}

.assignment-title{
    font-weight:600;
    cursor:pointer;
}

.assignment-desc{
    font-size:13px;
    color:#666;
}

.file-item{
    display:flex;
    justify-content:space-between;
    padding:10px 0;
    border-bottom:1px solid #eee;
}

/* POPUP */
.popup{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.55);
    display:none;
    justify-content:center;
    align-items:center;
}

.popup-box{
    background:#fff;
    padding:25px;
    border-radius:14px;
    width:320px;
    text-align:center;
}

.popup-box button{
    margin:5px;
    padding:8px 15px;
    border:none;
    border-radius:8px;
}

/* ASSIGNMENT POPUP */
</style>

<div class="box">

    <div class="welcome">
        <h3>Welcome <?= htmlspecialchars($name); ?></h3>
        <div class="role-box">Role: <?= ucfirst($role); ?></div>
    </div>

    <input id="search"
           placeholder="Search assignment..."
           style="width:100%;padding:10px;margin-top:15px;border-radius:8px;border:1px solid #ddd;">

    <div id="result">Loading...</div>

</div>

<!-- DELETE SUBMISSION POPUP -->
<div class="popup" id="popup">
    <div class="popup-box">
        <h4>Delete Submission?</h4>

        <form method="POST">
            <input type="hidden" name="delete_submission_id" id="delId">

            <button type="submit">Yes</button>
            <button type="button" onclick="closePopup()">No</button>
        </form>
    </div>
</div>

<!-- DELETE ASSIGNMENT POPUP -->
<div class="popup" id="popupAssign">
    <div class="popup-box">
        <h4>Delete Assignment?</h4>

        <form method="POST">
            <input type="hidden" name="delete_assignment_id" id="delAssignId">

            <button type="submit">Yes</button>
            <button type="button" onclick="closePopupAssign()">No</button>
        </form>
    </div>
</div>

<script>

function load(q=""){
    fetch("dashboard.php?ajax=1&q="+encodeURIComponent(q))
    .then(r=>r.text())
    .then(data=>{
        document.getElementById("result").innerHTML=data;
    });
}

window.onload=()=>load();

document.getElementById("search").addEventListener("keyup",function(){
    load(this.value);
});

/* SUBMISSION POPUP */
function openPopup(id){
    document.getElementById("delId").value=id;
    document.getElementById("popup").style.display="flex";
}

function closePopup(){
    document.getElementById("popup").style.display="none";
}

/* ASSIGNMENT POPUP */
function openPopupAssign(id){
    document.getElementById("delAssignId").value=id;
    document.getElementById("popupAssign").style.display="flex";
}

function closePopupAssign(){
    document.getElementById("popupAssign").style.display="none";
}

</script>

<?php include 'footer.php'; ?>