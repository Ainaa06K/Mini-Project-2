<?php
include 'db.php';
include 'header.php';

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$msg = "";

// Handle form submission when admin creates assignment
if (isset($_POST['create_task'])) {

    // Get and clean input data
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validate empty fields
    if ($title === "" || $description === "") {
        $msg = "<div class='alert error'>Please fill in all fields!</div>";
    }

    // Validate minimum title length
    elseif (strlen($title) < 5) {
        $msg = "<div class='alert error'>Title must be at least 5 characters!</div>";
    }

    // Validate minimum description length
    elseif (strlen($description) < 10) {
        $msg = "<div class='alert error'>Description must be at least 10 characters!</div>";
    }

    // Allow only safe characters in title
    elseif (!preg_match("/^[a-zA-Z0-9\s\(\)_]+$/", $title)) {
        $msg = "<div class='alert error'>Title only allow letters, numbers, space, () and _</div>";
    }

    else {

        // Insert assignment into database
        $stmt = $conn->prepare("INSERT INTO assignments (title, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $description);

        if ($stmt->execute()) {
            $msg = "<div class='alert success'>Assignment created successfully!</div>";
        } else {
            $msg = "<div class='alert error'>Database error!</div>";
        }

        $stmt->close();
    }
}
?>

<style>
body{
    font-family:'Segoe UI', Tahoma, sans-serif;
    background:#eef2f7;
}

.wrapper{
    max-width:600px;
    margin:50px auto;
    padding:20px;
}

/* main card container */
.card{
    background:#fff;
    border-radius:16px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    overflow:hidden;
}

/* header section */
.card-header{
    background:linear-gradient(135deg, #1e3c72, #2a5298);
    color:white;
    padding:18px 20px;
}

.card-header h4{
    margin:0;
    font-weight:600;
}

/* form content area */
.card-body{
    padding:20px;
}

/* form group spacing */
.form-group{
    margin-bottom:15px;
}

/* labels styling */
.form-group label{
    display:block;
    margin-bottom:5px;
    font-size:14px;
    font-weight:500;
}

/* input and textarea styling */
.form-group input,
.form-group textarea{
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid #ddd;
    outline:none;
    transition:0.2s;
}

/* focus effect for inputs */
.form-group input:focus,
.form-group textarea:focus{
    border-color:#2a5298;
    box-shadow:0 0 0 2px rgba(42,82,152,0.1);
}

/* validation error text */
small{
    font-size:12px;
    color:red;
}

/* action buttons container */
.actions{
    margin-top:15px;
    display:flex;
    gap:10px;
}

/* buttons styling */
.actions button,
.actions a{
    flex:1;
    padding:10px;
    border-radius:8px;
    text-align:center;
    text-decoration:none;
    font-size:14px;
    border:none;
    cursor:pointer;
    transition:0.2s;
}

/* primary action button */
.btn-main{
    background:#1e3c72;
    color:white;
}

.btn-main:hover{
    background:#16325c;
}

/* cancel button */
.btn-cancel{
    background:#ddd;
    color:#333;
}

.btn-cancel:hover{
    background:#bbb;
}

/* alert message box */
.alert{
    padding:10px;
    border-radius:8px;
    margin-bottom:10px;
    font-size:14px;
}

/* success message */
.success{
    background:#eafaf1;
    color:#2ecc71;
}

/* error message */
.error{
    background:#fdecea;
    color:#e74c3c;
}
</style>

<div class="wrapper">

    <div class="card">

        <!-- Card header title -->
        <div class="card-header">
            <h4>Create Assignment</h4>
        </div>

        <div class="card-body">

            <!-- Display system message -->
            <?php echo $msg; ?>

            <form id="assignmentForm" method="POST">

                <!-- Assignment title input -->
                <div class="form-group">
                    <label>Assignment Title</label>
                    <input type="text" name="title" id="title">
                    <small id="titleError"></small>
                </div>

                <!-- Assignment description input -->
                <div class="form-group">
                    <label>Description / Instructions</label>
                    <textarea name="description" id="description" rows="4"></textarea>
                    <small id="descError"></small>
                </div>

                <!-- Action buttons -->
                <div class="actions">
                    <button type="submit" name="create_task" class="btn-main">
                        Publish
                    </button>

                    <a href="dashboard.php" class="btn-cancel">
                        Cancel
                    </a>
                </div>

            </form>

        </div>

    </div>

</div>

<script>
// Handle client-side validation before submitting form
document.getElementById("assignmentForm").addEventListener("submit", function(e) {

    let title = document.getElementById("title");
    let description = document.getElementById("description");

    let titleError = document.getElementById("titleError");
    let descError = document.getElementById("descError");

    let valid = true;

    // Reset error messages
    titleError.innerText = "";
    descError.innerText = "";

    let titleValue = title.value.trim();

    // Validate title
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

    // Validate description
    if (descValue === "") {
        descError.innerText = "Description is required";
        valid = false;
    }
    else if (descValue.length < 10) {
        descError.innerText = "Description must be at least 10 characters";
        valid = false;
    }

    // Stop form submission if validation fails
    if (!valid) {
        e.preventDefault();
    }
});
</script>

<?php include 'footer.php'; ?>