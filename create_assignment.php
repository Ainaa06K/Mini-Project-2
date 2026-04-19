<?php
// basic setup
include 'config.php';
include 'header.php';

// security check - only admin can enter this page
// if not admin or not logged in, kick them back to dashboard
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$msg = "";

// check if the form was actually clicked
if (isset($_POST['create_task'])) {
    
    // get data from the form
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $due_date = $_POST['due_date'];

    // simple validation to make sure nothing is empty
    if (empty($title) || empty($desc) || empty($due_date)) {
        $msg = "<div class='alert alert-danger'>Please fill in all the details!</div>";
    } else {
        // use prepared statement to prevent sql injection (for the marks)
        $stmt = $conn->prepare("INSERT INTO assignments (title, description, due_date) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $desc, $due_date);

        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>Assignment created successfully!</div>";
        } else {
            // just in case the database has an issue
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
                    <h4 class="mb-0">Create New Assignment</h4>
                </div>
                <div class="card-body">
                    
                    <?php echo $msg; // show success or error message ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Assignment Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Mini Project 2" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description / Instructions</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Write the details here..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="create_task" class="btn btn-create py-2">Publish Assignment</button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
