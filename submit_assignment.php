<?php
include 'db.php';
session_start();

/* =======================
   SECURITY CHECK
========================= */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if($_SESSION['role'] !== 'student'){
    header("Location: dashboard.php");
    exit();
}

$msg = "";

/* GET ASSIGNMENTS*/
$assignments = $conn->query("SELECT * FROM assignments");


//    SUBMIT PROCESS

if(isset($_POST['submit'])){

    $assignment_id = $_POST['assignment_id'] ?? '';

    $file = $_FILES['file']['name'] ?? '';
    $tmp = $_FILES['file']['tmp_name'] ?? '';
    $size = $_FILES['file']['size'] ?? 0;
    $error = $_FILES['file']['error'] ?? 1;

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $allowed = ['pdf','docx','txt','pptx'];

    if(empty($assignment_id)){
        $msg = "<div class='alert alert-danger'>Please select assignment</div>";
    }
    elseif($error != 0){
        $msg = "<div class='alert alert-danger'>Please upload file</div>";
    }
    elseif(!in_array($ext, $allowed)){
        $msg = "<div class='alert alert-danger'>Invalid file type</div>";
    }
    elseif($size > 2000000){
        $msg = "<div class='alert alert-danger'>Max file size 2MB</div>";
    }
    else{

        /* DUPLICATE CHECK */
        $check = $conn->prepare("SELECT id FROM submissions WHERE user_id=? AND assignment_id=?");
        $check->bind_param("ii", $_SESSION['user_id'], $assignment_id);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0){
            $msg = "<div class='alert alert-danger'>You already submitted this assignment</div>";
        } else {

            /* CREATE UPLOAD FOLDER */
            if(!is_dir("uploads")){
                mkdir("uploads", 0777, true);
            }

            $newFile = time() . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $file);

            if(move_uploaded_file($tmp, "uploads/" . $newFile)){

                $stmt = $conn->prepare("
                    INSERT INTO submissions (user_id, assignment_id, file)
                    VALUES (?,?,?)
                ");

                $stmt->bind_param(
                    "iis",
                    $_SESSION['user_id'],
                    $assignment_id,
                    $newFile
                );

                if($stmt->execute()){
                    $msg = "<div class='alert alert-success'>Submitted successfully</div>";
                } else {
                    $msg = "<div class='alert alert-danger'>Database error</div>";
                }

                $stmt->close();

            } else {
                $msg = "<div class='alert alert-danger'>Upload failed</div>";
            }
        }

        $check->close();
    }
}

/* =========================
   HEADER
========================= */
include 'header.php';
?>

<!-- ================= STYLE ================= -->
<style>
body{
    background:#f4f6f9;
    font-family: Arial;
}

.card{
    border-radius: 15px;
}

.form-select, .form-control{
    border-radius: 10px;
    padding: 12px;
}

.btn{
    border-radius: 10px;
}

.upload-box{
    max-width: 650px;
    margin: auto;
}

.header-title{
    background: linear-gradient(135deg, #2c3e50, #3498db);
    color: white;
    padding: 18px;
    border-radius: 15px 15px 0 0;
    text-align: center;
}

.card-body{
    padding: 30px;
}
</style>

<!-- ================= UI ================= -->
<div class="container mt-5 upload-box">

    <div class="card shadow-lg border-0">

        <!-- HEADER -->
        <div class="header-title">
            <h4> Submit Assignment</h4>
        </div>

        <div class="card-body">

            <?= $msg; ?>

            <form method="POST" enctype="multipart/form-data">

                <!-- ASSIGNMENT -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Choose Assignment</label>
                    <select name="assignment_id" class="form-select shadow-sm" required>
                        <option value="">-- Select Assignment --</option>

                        <?php while($a = $assignments->fetch_assoc()){ ?>
                            <option value="<?= $a['id']; ?>">
                                <?= htmlspecialchars($a['title']); ?>
                            </option>
                        <?php } ?>

                    </select>
                </div>

                <!-- FILE -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Upload File</label>
                    <input type="file" name="file" class="form-control shadow-sm" required>
                    <small class="text-muted">
                        Allowed: PDF, DOCX, TXT, PPTX (Max 2MB)
                    </small>
                </div>

                <!-- BUTTON -->
                <button type="submit" name="submit"
                        class="btn btn-primary w-100 shadow-sm"
                        style="background: linear-gradient(135deg,#3498db,#2c3e50); border:none;">
                     Submit Assignment
                </button>

            </form>

        </div>
    </div>

</div>

<?php include 'footer.php'; ?>
