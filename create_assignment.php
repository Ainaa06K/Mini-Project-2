<?php
include 'config.php';
include 'header.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$msg = "";

if (isset($_POST['create_task'])) {

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (empty($title) || empty($description)) {
        $msg = "<div class='alert alert-danger'>Please fill in all fields!</div>";
    } else {

        // INSERT TITLE + DESCRIPTION ONLY
        $stmt = $conn->prepare("INSERT INTO assignments (title, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $description);

        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>Assignment created successfully!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Something went wrong. Try again.</div>";
        }

        $stmt->close();
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card shadow-sm">

                <div class="card-header btn-create text-white">
                    <h4 class="mb-0">Create Assignment</h4>
                </div>

                <div class="card-body">

                    <?php echo $msg; ?>

                    <form method="POST">

                        <!-- TITLE -->
                        <div class="mb-3">
                            <label class="form-label">Assignment Title</label>
                            <input type="text" name="title" class="form-control"
                                   placeholder="e.g. Mini Project 2" required>
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-3">
                            <label class="form-label">Description / Instructions</label>
                            <textarea name="description" class="form-control" rows="4"
                                      placeholder="Write assignment details..." required></textarea>
                        </div>

                        <!-- BUTTONS -->
                        <div class="d-grid gap-2">
                            <button type="submit" name="create_task" class="btn btn-create py-2">
                                Publish Assignment
                            </button>

                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>