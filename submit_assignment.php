<?php
session_start();
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
    $allowed = ['pdf','docx','txt'];

    if(empty($title) || empty($description)){
        $msg = "<div class='alert alert-danger'>All fields required</div>";
    }
    elseif($error != 0){
        $msg = "<div class='alert alert-danger'>Upload file</div>";
    }
    elseif(!in_array($ext, $allowed)){
        $msg = "<div class='alert alert-danger'>Only PDF, DOCX, TXT allowed</div>";
    }
    elseif($size > 2000000){
        $msg = "<div class='alert alert-danger'>Max 2MB</div>";
    }
    else{

        if(!is_dir("uploads")){
            mkdir("uploads");
        }

        $newFile = time()."_".$file;

        move_uploaded_file($tmp, "uploads/".$newFile);

        // INSERT SUBMISSION
        $stmt = $conn->prepare("INSERT INTO submissions (user_id,title,description,file) VALUES (?,?,?,?)");

        $stmt->bind_param(
            "isss",
            $_SESSION['user_id'],
            $title,
            $description,
            $newFile
        );

        if($stmt->execute()){
            $msg = "<div class='alert alert-success'>Submitted successfully</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Error submitting</div>";
        }

        $stmt->close();
    }
}
?>

<div class="container mt-4">
<div class="card p-4 mx-auto" style="max-width:600px;">

<h3>Submit Assignment</h3>

<?= $msg ?>

<form method="POST" enctype="multipart/form-data" id="formSubmit">

<input class="form-control mb-2" name="title" placeholder="Title" required>

<textarea class="form-control mb-2" name="description" placeholder="Description" required></textarea>

<input class="form-control mb-2" type="file" name="file" required>

<button class="btn btn-success w-100" name="submit">Submit</button>

</form>

</div>
</div>

<script>
document.getElementById("formSubmit").addEventListener("submit", function(e){

    let file = document.querySelector("[name='file']").value.toLowerCase();

    if(file !== "" && !(file.endsWith(".pdf") || file.endsWith(".docx") || file.endsWith(".txt"))){
        alert("Only PDF, DOCX, TXT allowed");
        e.preventDefault();
    }
});
</script>

<?php include 'footer.php'; ?>