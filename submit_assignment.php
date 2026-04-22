<?php
include 'db.php';
include 'header.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$msg = "";

/* GET ASSIGNMENTS FOR DROPDOWN */
$assignments = $conn->query("SELECT * FROM assignments");

if(isset($_POST['submit'])){

    $assignment_id = $_POST['assignment_id'];

    $file = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];
    $size = $_FILES['file']['size'];
    $error = $_FILES['file']['error'];

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $allowed = ['pdf','docx','txt','pptx'];

    if(empty($assignment_id)){
        $msg = "<div class='alert alert-danger'>Please select assignment</div>";
    }
    elseif($error != 0){
        $msg = "<div class='alert alert-danger'>Upload file</div>";
    }
    elseif(!in_array($ext, $allowed)){
        $msg = "<div class='alert alert-danger'>Invalid file type</div>";
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
        // Check for duplicate submission
        $check = $conn->prepare("SELECT id FROM submissions WHERE user_id=? AND assignment_id=?");
        $check->bind_param("ii", $_SESSION['user_id'], $assignment_id);
        $check->execute();
        $check->store_result();

if($check->num_rows > 0){
    $msg = "<div class='alert alert-danger'>You already submitted this assignment</div>";
}

        /* INSERT WITH assignment_id */
        $stmt = $conn->prepare("INSERT INTO submissions (user_id, assignment_id, file) VALUES (?,?,?)");

        $stmt->bind_param(
            "iis",
            $_SESSION['user_id'],
            $assignment_id,
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

<!-- DROPDOWN -->
<select class="form-control mb-2" name="assignment_id" required>
    <option value="">-- Select Assignment --</option>

    <?php while($row = $assignments->fetch_assoc()){ ?>
        <option value="<?= $row['id']; ?>">
            <?= $row['title']; ?>
        </option>
    <?php } ?>

</select>

<input class="form-control mb-2" type="file" name="file" required>

<button class="btn btn-success w-100" name="submit">Submit</button>

</form>

</div>
</div>

<script>
document.getElementById("formSubmit").addEventListener("submit", function(e){

    let file = document.querySelector("[name='file']").value.toLowerCase();

    if(file !== "" && !(file.endsWith(".pdf") || file.endsWith(".docx") || file.endsWith(".txt") || file.endsWith(".pptx"))){
        alert("Only PDF, DOCX, TXT, PPTX allowed");
        e.preventDefault();
    }
});
</script>

<?php include 'footer.php'; ?>