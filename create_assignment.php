<?php
include 'db.php';
include 'header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$msg = "";

if (isset($_POST['create_task'])) {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === "" || $description === "") {
        $msg = "<div class='alert alert-danger'>Please fill in all fields!</div>";
    }
    elseif (strlen($title) < 5) {
        $msg = "<div class='alert alert-danger'>Title must be at least 5 characters!</div>";
    }
    elseif (strlen($description) < 10) {
        $msg = "<div class='alert alert-danger'>Description must be at least 10 characters!</div>";
    }
    elseif (!preg_match("/^[a-zA-Z0-9\s\(\)_]+$/", $title)) {
        $msg = "<div class='alert alert-danger'>Title only allow letters, numbers, space, () and _</div>";
    }
    else {

        $stmt = $conn->prepare("INSERT INTO assignments (title, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $description);

        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>Assignment created successfully!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Database error!</div>";
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

                    <form id="assignmentForm" method="POST">

                        <div class="mb-3">
                            <label class="form-label">Assignment Title</label>
                            <input type="text" name="title" id="title" class="form-control">
                            <small id="titleError" class="text-danger"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description / Instructions</label>
                            <textarea name="description" id="description" class="form-control" rows="4"></textarea>
                            <small id="descError" class="text-danger"></small>
                        </div>

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

<script>
document.getElementById("assignmentForm").addEventListener("submit", function(e) {

    let title = document.getElementById("title");
    let description = document.getElementById("description");

    let titleError = document.getElementById("titleError");
    let descError = document.getElementById("descError");

    let valid = true;

    titleError.innerText = "";
    descError.innerText = "";

    let titleValue = title.value.trim();

    if (titleValue === "") {
        titleError.innerText = "Title is required";
        valid = false;
    }
    else if (titleValue.length < 5) {
        titleError.innerText = "Title must be at least 5 characters";
        valid = false;
    }
    else if (!/^[a-zA-Z0-9\s\(\)_]+$/.test(titleValue)) {
        titleError.innerText = "Only letters, numbers, space, () and _ allowed";
        valid = false;
    }

    let descValue = description.value.trim();

    if (descValue === "") {
        descError.innerText = "Description is required";
        valid = false;
    }
    else if (descValue.length < 10) {
        descError.innerText = "Description must be at least 10 characters";
        valid = false;
    }

    if (!valid) {
        e.preventDefault();
    }

});
</script>

<?php include 'footer.php'; ?>