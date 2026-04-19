<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
include 'header.php';

$msg = "";

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(empty($email) || empty($password)){
        $msg = "<div class='alert alert-danger'>All fields required</div>";
    } else {

        // PREPARED STATEMENT
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        $user = $result->fetch_assoc();

        $stmt->close();

        if($user && password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // ROLE REDIRECT
            if($user['role'] == 'admin'){
                header("Location: admin_dashboard.php");
            } else {
                header("Location: student_dashboard.php");
            }
            exit();

        } else {
            $msg = "<div class='alert alert-danger'>Invalid email or password</div>";
        }
    }
}
?>

<div class="container mt-5">
<div class="row shadow rounded overflow-hidden">

<div class="col-md-6 bg-primary text-white d-flex justify-content-center align-items-center">
<h2>Assignment System</h2>
</div>

<div class="col-md-6 p-5">
<h3>Login</h3>
<?= $msg ?>

<form method="POST" id="loginForm">
<input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
<input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
<button class="btn btn-success w-100" name="login">Login</button>
</form>

<p class="mt-3">No account? <a href="register.php">Register</a></p>

</div>
</div>
</div>

<script>
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