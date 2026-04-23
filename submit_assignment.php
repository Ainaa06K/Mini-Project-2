<?php
include 'db.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if($_SESSION['role'] !== 'student'){
    header("Location: dashboard.php");
    exit();
}

$msg = "";

$assignments = $conn->query("SELECT * FROM assignments");

if(isset($_POST['submit'])){

    $assignment_id = $_POST['assignment_id'] ?? '';

    $file = $_FILES['file']['name'] ?? '';
    $tmp = $_FILES['file']['tmp_name'] ?? '';
    $size = $_FILES['file']['size'] ?? 0;
    $error = $_FILES['file']['error'] ?? 1;

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $allowed = ['pdf','docx','txt','pptx'];

    if(empty($assignment_id)){
        $msg = "<div class='alert error'>Please select assignment</div>";
    }
    elseif($error != 0){
        $msg = "<div class='alert error'>Please upload file</div>";
    }
    elseif(!in_array($ext, $allowed)){
        $msg = "<div class='alert error'>Invalid file type</div>";
    }
    elseif($size > 2000000){
        $msg = "<div class='alert error'>Max file size 2MB</div>";
    }
    else{

        $check = $conn->prepare("SELECT id FROM submissions WHERE user_id=? AND assignment_id=?");
        $check->bind_param("ii", $_SESSION['user_id'], $assignment_id);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0){
            $msg = "<div class='alert error'>Already submitted</div>";
        } else {

            if(!is_dir("uploads")){
                mkdir("uploads", 0777, true);
            }

            $newFile = time() . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $file);

            if(move_uploaded_file($tmp, "uploads/" . $newFile)){

                $stmt = $conn->prepare("
                    INSERT INTO submissions (user_id, assignment_id, file)
                    VALUES (?,?,?)
                ");

                $stmt->bind_param("iis", $_SESSION['user_id'], $assignment_id, $newFile);

                if($stmt->execute()){
                    $msg = "<div class='alert success'>Submitted successfully</div>";
                } else {
                    $msg = "<div class='alert error'>Database error</div>";
                }

                $stmt->close();

            } else {
                $msg = "<div class='alert error'>Upload failed</div>";
            }
        }

        $check->close();
    }
}
include 'header.php';
?>

<style>
body{
    margin:0;
    font-family:'Segoe UI', Tahoma, sans-serif;
    background: linear-gradient(135deg, #eef2f7, #dbe7f5);
}

/* center layout */
.wrapper{
    min-height:90vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

/* card */
.card{
    width:420px;
    background:rgba(255,255,255,0.95);
    backdrop-filter: blur(10px);
    border-radius:16px;
    box-shadow:0 15px 35px rgba(0,0,0,0.1);
    overflow:hidden;
}

/* header */
.header{
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    color:white;
    padding:18px;
    text-align:center;
}

.header h4{
    margin:0;
    font-weight:600;
}

/* body */
.body{
    padding:22px;
}

/* input */
select, input[type=file]{
    width:100%;
    padding:10px;
    border-radius:10px;
    border:1px solid #ddd;
    margin-top:5px;
    font-size:13px;
}

/* button */
.btn{
    width:100%;
    margin-top:15px;
    padding:10px;
    border:none;
    border-radius:10px;
    background:#1e3c72;
    color:white;
    cursor:pointer;
    transition:0.2s;
}

.btn:hover{
    background:#16325c;
}

/* alert */
.alert{
    padding:8px;
    border-radius:8px;
    margin-bottom:10px;
    font-size:13px;
    text-align:center;
}

.error{ background:#fdecea; color:#e74c3c; }
.success{ background:#eafaf1; color:#2ecc71; }

/* note */
.note{
    font-size:12px;
    color:#666;
    margin-top:5px;
}
</style>

<div class="wrapper">

    <div class="card">

        <div class="header">
            <h4>Submit Assignment</h4>
        </div>

        <div class="body">

            <?= $msg ?>

            <form method="POST" enctype="multipart/form-data">

                <label>Choose Assignment</label>
                <select name="assignment_id" required>
                    <option value="">Select Assignment</option>
                    <?php while($a = $assignments->fetch_assoc()){ ?>
                        <option value="<?= $a['id']; ?>">
                            <?= htmlspecialchars($a['title']); ?>
                        </option>
                    <?php } ?>
                </select>

                <br><br>

                <label>Upload File</label>
                <input type="file" name="file" required>

                <div class="note">
                    Allowed: PDF, DOCX, TXT, PPTX (Max 2MB)
                </div>

                <button class="btn" name="submit">
                    Submit Assignment
                </button>

            </form>

        </div>

    </div>

</div>

<?php include 'footer.php'; ?>