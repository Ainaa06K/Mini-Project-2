<?php
include 'config.php';
include 'header.php';


if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$name = $_SESSION['name'] ?? 'User';
?>

<style>
body{
    background:#f4f6f9;
    font-family: Arial;
}

.welcome{
    background:#2c3e50;
    color:white;
    padding:20px;
    border-radius:10px;
}

.role-box{
    margin-top:8px;
    display:inline-block;
    padding:5px 12px;
    border-radius:20px;
    font-size:14px;
    background:#f39c12;
    color:white;
}

.box{
    background:white;
    padding:20px;
    border-radius:10px;
    margin-top:20px;
}

/* FILE STYLE */
.file-item{
    padding:10px 0;
    border-bottom:1px solid #eee;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.file-left{
    font-size:14px;
}

.file-name{
    color:#333;
    font-weight:500;
}

/* BUTTONS */
.download-btn{
    background:#1e639b;
    color:white;
    padding:5px 10px;
    border-radius:6px;
    text-decoration:none;
    font-size:13px;
}

.delete-btn{
    background:#e74c3c;
    color:white;
    padding:5px 10px;
    border-radius:6px;
    border:none;
    font-size:13px;
}
</style>

<div class="container mt-3">

    <!-- WELCOME -->
    <div class="welcome">
        <h3>Welcome <?php echo htmlspecialchars($name); ?></h3>

        <div class="role-box">
            Role: <?php echo ucfirst($role); ?>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="box">

        <h4>
            <?php echo ($role == 'admin') ? 'All Submissions' : 'My Submissions'; ?>
        </h4>

        <div class="accordion" id="submissionAccordion">

        <?php
        $assignments = $conn->query("SELECT * FROM assignments ORDER BY id DESC");

        while($a = $assignments->fetch_assoc()){

            $assignment_id = $a['id'];
        ?>

            <div class="accordion-item">

                <h2 class="accordion-header">
                    <button class="accordion-button collapsed"
                        data-bs-toggle="collapse"
                        data-bs-target="#a<?= $assignment_id; ?>">

                        <?= htmlspecialchars($a['title']); ?>

                    </button>
                </h2>

                <div id="a<?= $assignment_id; ?>" class="accordion-collapse collapse"
                    data-bs-parent="#submissionAccordion">

                    <div class="accordion-body">

                        <?php

                        if($role == 'admin'){
                            $sql = "SELECT users.name, submissions.file
                                    FROM submissions
                                    JOIN users ON submissions.user_id = users.id
                                    WHERE submissions.assignment_id = $assignment_id";
                        } else {
                            $sql = "SELECT file
                                    FROM submissions
                                    WHERE assignment_id = $assignment_id
                                    AND user_id = $user_id";
                        }

                        $result = $conn->query($sql);

                        if($result && $result->num_rows > 0){

                            while($row = $result->fetch_assoc()){

                                $file = $row['file'];
                        ?>

                        <div class="file-item">

                            <div class="file-left">

                                <?php if($role == 'admin'){ ?>
                                    👤 <b><?= $row['name']; ?></b><br>
                                <?php } ?>

                                📄 <span class="file-name"><?= htmlspecialchars($file); ?></span>

                            </div>

                            <div class="d-flex gap-2">

                                <a class="download-btn" href="uploads/<?= $file; ?>">
                                    Download
                                </a>

                                <button class="delete-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#del<?= md5($file); ?>">
                                    Delete
                                </button>

                            </div>

                        </div>

                        <!-- DELETE MODAL -->
                        <div class="modal fade" id="del<?= md5($file); ?>" tabindex="-1">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                              <div class="modal-header">
                                <h5 class="modal-title">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>

                              <div class="modal-body">
                                Are you sure want to delete this file?
                                <br><b><?= htmlspecialchars($file); ?></b>
                              </div>

                              <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">
                                    Cancel
                                </button>

                                <a href="delete.php?file=<?= $file; ?>"
                                   class="btn btn-danger">
                                   Yes Delete
                                </a>
                              </div>

                            </div>
                          </div>
                        </div>

                        <?php
                            }

                        } else {
                            echo "No submission yet";
                        }
                        ?>

                    </div>

                </div>

            </div>

        <?php } ?>

        </div>

    </div>

</div>

<?php include 'footer.php'; ?>