<?php
// Show all PHP errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection file
include 'config.php';

// Include header file (navbar, layout, etc)
include 'header.php';
?>

<div class="container mt-5">

<div class="row shadow rounded overflow-hidden">

<!-- LEFT SIDE (Welcome section) -->
<div class="col-md-6 bg-primary text-white d-flex justify-content-center align-items-center text-center p-5">
  <div>
    <h2>Welcome to</h2>
    <h1 class="fw-bold">Assignment Submission Management System</h1>
  </div>
</div>

<!-- RIGHT SIDE (Login form) -->
<div class="col-md-6 bg-white p-5">

  <!-- Login title -->
  <h3 class="text-center mb-4">Login</h3>

  <!-- Login form -->
  <form method="POST">
    <input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
    <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
    <button class="btn btn-success w-100" name="login">Login</button>
  </form>

<?php
// Check if login button is clicked
if(isset($_POST['login'])){

    // Get input from form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Get user data from database based on email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();

    // Check if user exists and password is correct
    if($user && password_verify($password, $user['password'])){

        // Store user info in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['username'];

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();

    } else {
        // Show error message if login fails
        echo "<div class='alert alert-danger mt-3'>Invalid email or password</div>";
    }
}
?>

  <!-- Register link -->
  <p class="mt-3 text-center">
    Don't have an account? <a href="register.php">Register</a>
  </p>

</div>
</div>
</div>

<?php
// Include footer file
include 'footer.php';
?>