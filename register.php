<?php
include 'config.php';
include 'header.php';

$msg = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    // ikut database = name
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if(empty($name) || empty($email) || empty($password)){
        $msg = "<div class='alert alert-danger'>All fields required</div>";
    } else {

        // check name
        $checkUser = $conn->prepare("SELECT id FROM users WHERE name=?");
        $checkUser->bind_param("s", $name);
        $checkUser->execute();
        $checkUser->store_result();

        if($checkUser->num_rows > 0){
            $msg = "<div class='alert alert-danger'>Name already exists</div>";
        } else {

            // check email
            $checkEmail = $conn->prepare("SELECT id FROM users WHERE email=?");
            $checkEmail->bind_param("s", $email);
            $checkEmail->execute();
            $checkEmail->store_result();

            if($checkEmail->num_rows > 0){
                $msg = "<div class='alert alert-danger'>Email already exists</div>";
            } else {

                $hashed = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO users(name,email,password) VALUES(?,?,?)");
                $stmt->bind_param("sss", $name, $email, $hashed);

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

<div class="card main-card p-4">

<h3 class="text-center">Register</h3>

<?= $msg ?>

<form method="POST">
    <input class="form-control mb-2" name="name" placeholder="Name" required>
    <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
    <input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
    <button class="btn btn-primary w-100">Register</button>
</form>

</div>

<?php include 'footer.php'; ?>