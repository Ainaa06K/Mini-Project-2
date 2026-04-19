<?php
include 'header.php';
include 'config.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
?>

<style>
body{
    background:#f4f6f9;
    font-family: Arial;
}

.container-box{
    max-width:900px;
    margin:30px auto;
}

.title-box{
    background:#1f2a44;
    color:white;
    padding:15px 20px;
    border-radius:10px;
    margin-bottom:20px;
}

.card-sub{
    background:white;
    padding:15px;
    border-radius:10px;
    margin-bottom:15px;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
    transition:0.3s;
}

.card-sub:hover{
    transform:scale(1.01);
}

.badge{
    display:inline-block;
    padding:4px 10px;
    border-radius:20px;
    background:#e3e3e3;
    font-size:12px;
    margin-bottom:5px;
}

.btn-download{
    display:inline-block;
    margin-top:10px;
    padding:8px 12px;
    background:#4caf50;
    color:white;
    text-decoration:none;
    border-radius:6px;
}

.btn-download:hover{
    background:#388e3c;
    color:white;
}
</style>

<div class="container-box">

<?php if($role == 'admin'){ ?>

    <div class="title-box">
        <h3>All Submissions</h3>
    </div>

    <?php
    $sql = "SELECT users.name, assignments.title, submissions.file
            FROM submissions
            JOIN users ON submissions.user_id = users.id
            JOIN assignments ON submissions.assignment_id = assignments.id";

    $result = $conn->query($sql);

    while($row = $result->fetch_assoc()){
    ?>

        <div class="card-sub">
            <div class="badge">Student</div>
            <h4><?= $row['name']; ?></h4>

            <div class="badge">Assignment</div>
            <p><?= $row['title']; ?></p>

            <a class="btn-download" href="uploads/<?= $row['file']; ?>" download>
                Download
            </a>
        </div>

    <?php } ?>

<?php } else { ?>

    <div class="title-box">
        <h3>My Submissions</h3>
    </div>

    <?php
    $stmt = $conn->prepare("SELECT assignments.title, submissions.file
                            FROM submissions
                            JOIN assignments ON submissions.assignment_id = assignments.id
                            WHERE submissions.user_id = ?");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while($row = $result->fetch_assoc()){
    ?>

        <div class="card-sub">
            <div class="badge">Assignment</div>
            <p><?= $row['title']; ?></p>

            <a class="btn-download" href="uploads/<?= $row['file']; ?>" download>
                Download
            </a>
        </div>

    <?php } ?>

<?php } ?>

</div>