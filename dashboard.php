<?php
include 'db.php';

// Smart session start to avoid "already active" notice
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include 'header.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$name = $_SESSION['name'] ?? 'User';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
body{ background:#f4f6f9; font-family: Arial; }
.welcome{ background:#2c3e50; color:white; padding:20px; border-radius:10px; }
.role-box{ margin-top:8px; display:inline-block; padding:5px 12px; border-radius:20px; font-size:14px; background:#f39c12; color:white; }
.box{ background:white; padding:20px; border-radius:10px; margin-top:20px; }
.assignment-header{ display:flex; justify-content:space-between; align-items:flex-start; padding:12px; border:1px solid #eee; border-radius:8px; margin-bottom:5px; background:#fff; }
.assignment-title{ font-weight:600; cursor:pointer; }
.assignment-desc{ font-size:13px; color:#666; margin-top:3px; }
.left-box{ display:flex; flex-direction:column; }
.file-item{ display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #eee; align-items: center; }
.btn-del{ background:#e74c3c; color:white; padding:5px 10px; border-radius:6px; border:none; font-size:13px; }
</style>

<div class="container mt-3">

    <div class="welcome">
        <h3>Welcome <?= htmlspecialchars($name); ?></h3>
        <div class="role-box">Role: <?= ucfirst($role); ?></div>
    </div>

    <div class="box">
        <h4>Assignments</h4>
        <div class="accordion" id="assignmentAccordion">

        <?php
        $assignments = $conn->query("SELECT * FROM assignments ORDER BY id DESC");
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
            <button type="button" class="btn-del" data-bs-toggle="modal" data-bs-target="#delA<?= $aid; ?>">
                Delete Assignment
            </button>
            <?php } ?>
        </div>

        <div id="a<?= $aid; ?>" class="collapse" data-bs-parent="#assignmentAccordion">
            <div class="p-3 border">
            <?php
            if ($role == 'admin') {
                $stmt = $conn->prepare("SELECT submissions.id, submissions.file, users.name FROM submissions JOIN users ON submissions.user_id = users.id WHERE submissions.assignment_id=?");
                $stmt->bind_param("i", $aid);
            } else {
                $stmt = $conn->prepare("SELECT submissions.id, submissions.file, users.name FROM submissions JOIN users ON submissions.user_id = users.id WHERE submissions.assignment_id=? AND submissions.user_id=?");
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
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delS<?= $sid; ?>">
                        Delete
                    </button>
                </div>

                <div class="modal fade" id="delS<?= $sid; ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header"><h5>Delete Submission</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                      <div class="modal-body">Are you sure you want to delete this submission?</div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form method="POST" action="delete_submission.php">
                            <input type="hidden" name="submission_id" value="<?= $sid; ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
            <?php } // End Submission While ?>

            <?php } else { ?>
                <p class="text-muted">No submission yet</p>
            <?php } ?>
            </div>
        </div>

        <div class="modal fade" id="delA<?= $aid; ?>">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header"><h5>Delete Assignment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
              <div class="modal-body">Delete this assignment? All student files will be removed.</div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="delete_assignment.php">
                    <input type="hidden" name="id" value="<?= $aid; ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <?php } // End Assignment While ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>