<?php
// START SESSION (WAJIB PALING ATAS)
// session_start();

// Show errors (development sahaja)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect database
include 'config.php';

// Include header
include 'header.php';

// Message variable
$msg = "";
?>

<div class="container mt-5">

<div class="row shadow rounded overflow-hidden">

<!-- LEFT SIDE -->
<div class="col-md-6 bg-primary text-white d-flex justify-content-center align-items-center text-center p-5">
  <div>
    <h2>Welcome to</h2>
    <h1 class="fw-bold">Assignment Submission Management System</h1>
  </div>
</div>

<!-- RIGHT SIDE -->
<div class="col-md-6 bg-white p-5">

<h3 class="text-center mb-4">Login</h3>

<?= $msg ?>

<form method="POST" id="loginForm">
    <input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
    <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
    <button class="btn btn-success w-100" name="login">Login</button>
</form>

<?php
if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Server-side validation
    if(empty($email) || empty($password)){
        $msg = "<div class='alert alert-danger mt-3'>All fields required</div>";
    } else {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();

        if($user && password_verify($password, $user['password'])){

            //  SAVE SESSION (IMPORTANT)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit();

        } else {
            $msg = "<div class='alert alert-danger mt-3'>Invalid email or password</div>";
        }
    }
}
?>

<p class="mt-3 text-center">
    Don't have an account? <a href="register.php">Register</a>
</p>

</div>
</div>
</div>

<script>
// CLIENT-SIDE VALIDATION
document.getElementById("loginForm").addEventListener("submit", function(e){

    let email = document.querySelector("[name='email']").value.trim();
    let pass = document.querySelector("[name='password']").value.trim();

    if(email === "" || pass === ""){
        alert("Please fill all fields");
        e.preventDefault();
    }
});
</script>

<?php include 'footer.php'; ?>