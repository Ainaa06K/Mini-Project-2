<?php
// session_start();
include 'config.php';
include 'header.php';

$msg = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // AUTO ROLE (WAJIB)
    $role = "student";

    // VALIDATION
    if(empty($name) || empty($email) || empty($password)){
        $msg = "<div class='alert alert-danger'>All fields required</div>";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $msg = "<div class='alert alert-danger'>Invalid email format</div>";
    }
    else {

        // CHECK NAME
        $checkName = $conn->prepare("SELECT id FROM users WHERE name=?");
        $checkName->bind_param("s", $name);
        $checkName->execute();
        $checkName->store_result();

        if($checkName->num_rows > 0){
            $msg = "<div class='alert alert-danger'>Name already exists</div>";
        } else {

            // CHECK EMAIL
            $checkEmail = $conn->prepare("SELECT id FROM users WHERE email=?");
            $checkEmail->bind_param("s", $email);
            $checkEmail->execute();
            $checkEmail->store_result();

            if($checkEmail->num_rows > 0){
                $msg = "<div class='alert alert-danger'>Email already exists</div>";
            } else {

                // HASH PASSWORD
                $hashed = password_hash($password, PASSWORD_DEFAULT);

                // INSERT USER
                $stmt = $conn->prepare("
                    INSERT INTO users(name,email,password,role) 
                    VALUES(?,?,?,?)
                ");

                $stmt->bind_param("ssss", $name, $email, $hashed, $role);

                if($stmt->execute()){
                    $msg = "<div class='alert alert-success'>Registered successfully</div>";
                } else {
                    $msg = "<div class='alert alert-danger'>Error registering user</div>";
                }
            }
        }
    }
}
?>

<div class="container mt-5">

<div class="card p-4 mx-auto" style="max-width:500px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">

<h3 class="text-center mb-3">Register</h3>

<?= $msg ?>

<form method="POST" id="registerForm">

<input class="form-control mb-3" name="name" placeholder="Name" required>

<input class="form-control mb-3" type="email" name="email" placeholder="Email" required>

<input class="form-control mb-3" type="password" name="password" placeholder="Password" required>

<button class="btn btn-primary w-100">Register</button>

</form>

<p class="text-center mt-3">
Already have account? <a href="login.php">Login</a>
</p>

</div>

</div>

<script>
// CLIENT-SIDE VALIDATION
document.getElementById("registerForm").addEventListener("submit", function(e){

    let name = document.querySelector("[name='name']").value.trim();
    let email = document.querySelector("[name='email']").value.trim();
    let pass = document.querySelector("[name='password']").value.trim();

    if(name === "" || email === "" || pass === ""){
        alert("All fields required");
        e.preventDefault();
    }

    if(pass.length < 6){
        alert("Password must be at least 6 characters");
        e.preventDefault();
    }
});
</script>

<?php include 'footer.php'; ?>