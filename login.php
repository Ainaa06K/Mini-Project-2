<?php
// Turn on error reporting during development so I can catch bugs early
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
include 'header.php';

$msg = "";

// Check if the user actually clicked the login button
if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic check: don't let them submit empty strings
    if(empty($email) || empty($password)){
        $msg = "<div class='alert alert-danger'>All fields required</div>";
    } else {

        // Use a prepared statement to prevent SQL Injection - security best practice
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc(); // Get the user data as an array

        $stmt->close();

        // Verify the password using PHP's built-in password_verify (for hashed passwords)
        if($user && password_verify($password, $user['password'])){

            // Store user details in the session so they stay logged in across pages
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Direct them to the right dashboard based on their role
            if($user['role'] == 'admin'){
                header("Location: dashboard.php"); 
            } else {
                header("Location: dashboard.php");
            }
            exit();

        } else {
            // Keep it vague for security: don't tell them if it was the email or the password that was wrong
            $msg = "<div class='alert alert-danger'>Invalid email or password</div>";
        }
    }
}
?>

<div class="container mt-5">
<div class="row shadow rounded overflow-hidden">

<div class="col-md-6 d-flex justify-content-center align-items-center text-white"
     style="background: linear-gradient(135deg, #2c3e50, #3498db);">
    
    <div class="text-center">
        <h2 style="font-weight:700; letter-spacing:1px;">
            Assignment Submission System
        </h2>
        <p style="font-size:13px; opacity:0.8;">
            For students to submit assignments and for teachers to manage them.
        </p>
    </div>

</div>

<div class="col-md-6 p-5">
<h3>Login</h3>
<?= $msg ?> <form method="POST" id="loginForm">
<input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
<input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
<button class="btn btn-success w-100" name="login">Login</button>
</form>

<p class="mt-3">No account? <a href="register.php">Register</a></p>

</div>
</div>
</div>

<script>
// Simple frontend check just to save a server trip if fields are empty
document.getElementById("loginForm").addEventListener("submit", function(e){
    let email = document.querySelector("[name='email']").value.trim();
    let pass = document.querySelector("[name='password']").value.trim();

    if(email === "" || pass === ""){
        alert("Please fill all fields");
        e.preventDefault(); // Stop the form from submitting
    }
});
</script>

<?php include 'footer.php'; ?>
