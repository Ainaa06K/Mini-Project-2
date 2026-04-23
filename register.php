<?php
include 'db.php';
include 'header.php';

$message = "";
$type = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and trim user input
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = "student"; // Default role for new users

    // Check empty fields
    if (empty($name) || empty($email) || empty($password)) {
        $message = "All fields are required.";
        $type = "error";

    // Validate email format
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $type = "error";

    // Validate password length
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $type = "error";

    } else {

        // Check if email already exists
        $checkQuery = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $checkQuery->bind_param("s", $email);
        $checkQuery->execute();
        $checkQuery->store_result();

        if ($checkQuery->num_rows > 0) {
            $message = "Email already registered.";
            $type = "warning";

        } else {

            // Hash password before saving to database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into database
            $insertStmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

            // Execute insert query
            if ($insertStmt->execute()) {
                $message = "Registration successful! You can login now.";
                $type = "success";
            } else {
                $message = "System error.";
                $type = "error";
            }

            $insertStmt->close();
        }

        $checkQuery->close();
    }
}
?>

<style>
body{
    margin:0;
    font-family:'Segoe UI', Tahoma, sans-serif;
    background: radial-gradient(circle at top, #eef2f7, #dbe7f5);
}

/* center container */
.wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

/* main form card */
.card{
    width:360px;
    background:rgba(255,255,255,0.9);
    backdrop-filter: blur(12px);
    border-radius:18px;
    box-shadow:0 20px 50px rgba(0,0,0,0.12);
    padding:28px;
}

/* hover effect for card */
.card:hover{
    transform:translateY(-2px);
}

/* page title */
.card h3{
    text-align:center;
    margin-bottom:18px;
    color:#1e3c72;
    font-weight:600;
}

/* input wrapper */
.input-group{
    margin-bottom:12px;
}

/* input field style */
.input-group input{
    width:100%;
    padding:11px;
    border-radius:10px;
    border:1px solid #e0e0e0;
    outline:none;
    font-size:13px;
}

/* focus effect on input */
.input-group input:focus{
    border-color:#2a5298;
    box-shadow:0 0 0 3px rgba(42,82,152,0.12);
}

/* submit button */
.btn{
    width:100%;
    padding:11px;
    border:none;
    border-radius:10px;
    background: linear-gradient(135deg,#1e3c72,#2a5298);
    color:white;
    font-size:14px;
    cursor:pointer;
    transition:0.2s;
}

/* button hover effect */
.btn:hover{
    transform:translateY(-1px);
    box-shadow:0 10px 20px rgba(0,0,0,0.15);
}

/* alert message box */
.alert{
    padding:10px;
    border-radius:10px;
    margin-bottom:12px;
    font-size:13px;
    text-align:center;
}

/* error message */
.error{ background:#fdecea; color:#e74c3c; }

/* success message */
.success{ background:#eafaf1; color:#2ecc71; }

/* warning message */
.warning{ background:#fff4e5; color:#f39c12; }

/* bottom text section */
.bottom{
    margin-top:14px;
    text-align:center;
    font-size:12px;
    color:#666;
}

/* link style */
.bottom a{
    color:#2a5298;
    text-decoration:none;
    font-weight:500;
}

/* link hover effect */
.bottom a:hover{
    text-decoration:underline;
}
</style>

<div class="wrapper">

    <div class="card">

        <!-- page heading -->
        <h3>Create Account</h3>

        <!-- show system message -->
        <?php if ($message): ?>
            <div class="alert <?= $type ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- registration form -->
        <form method="POST" id="registrationForm">

            <!-- name input -->
            <div class="input-group">
                <input type="text" name="name" placeholder="Full Name">
            </div>

            <!-- email input -->
            <div class="input-group">
                <input type="email" name="email" placeholder="Email Address">
            </div>

            <!-- password input -->
            <div class="input-group">
                <input type="password" name="password" placeholder="Password (min 6)">
            </div>

            <!-- submit button -->
            <button class="btn" type="submit">Sign Up</button>

        </form>

        <!-- login redirect link -->
        <div class="bottom">
            Already have an account? <a href="login.php">Login</a>
        </div>

    </div>

</div>

<script>
// Client-side validation before submitting form
document.getElementById("registrationForm").addEventListener("submit", function(event) {

    let name  = document.querySelector("[name='name']").value.trim();
    let email = document.querySelector("[name='email']").value.trim();
    let pass  = document.querySelector("[name='password']").value.trim();

    // Check empty fields
    if(!name || !email || !pass){
        alert("Please fill all fields");
        event.preventDefault();
        return;
    }

    // Check password length
    if(pass.length < 6){
        alert("Password must be at least 6 characters");
        event.preventDefault();
    }
});
</script>

<?php include 'footer.php'; ?>