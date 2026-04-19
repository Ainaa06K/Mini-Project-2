<?php
// session_start();
include 'config.php';
include 'header.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$msg = "";

if(isset($_POST['submit'])){

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    $file = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];
    $size = $_FILES['file']['size'];
    $error = $_FILES['file']['error'];

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if(empty($title) || empty($description)){
        $msg = "<div class='alert alert-danger'>All fields required</div>";
    }
    elseif($error != 0){
        $msg = "<div class='alert alert-danger'>Please upload a file</div>";
    }
    elseif($ext != "pdf"){
        $msg = "<div class='alert alert-danger'>Only PDF allowed</div>";
    }
    elseif($size > 2000000){
        $msg = "<div class='alert alert-danger'>File too large (max 2MB)</div>";
    }
    else {

        $newFile = time() . "_" . $file;

        move_uploaded_file($tmp, "uploads/" . $newFile);

        $stmt = $conn->prepare("
            INSERT INTO submissions (user_id, title, description, file)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isss",
            $_SESSION['user_id'],
            $title,
            $description,
            $newFile
        );

        if($stmt->execute()){
            $msg = "<div class='alert alert-success'>Assignment submitted successfully</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Failed to submit</div>";
        }
    }
}
?>

<div class="container mt-4">

<div class="card p-4 mx-auto" style="max-width:600px; border-radius:12px;">

<h3>Submit Assignment</h3>

<?= $msg ?>

<form method="POST" enctype="multipart/form-data" id="formSubmit">

<input class="form-control mb-2" name="title" placeholder="Assignment Title" required>

<textarea class="form-control mb-2" name="description" placeholder="Description" required></textarea>

<input class="form-control mb-2" type="file" name="file" required>

<button class="btn btn-success w-100" name="submit">Submit Assignment</button>

</form>

</div>

</div>

<script>
document.getElementById("formSubmit").addEventListener("submit", function(e){

    let file = document.querySelector("[name='file']").value.toLowerCase();

    if(file !== "" && !file.endsWith(".pdf")){
        alert("Only PDF allowed");
        e.preventDefault();
    }
});
</script>

<?php include 'footer.php'; ?>