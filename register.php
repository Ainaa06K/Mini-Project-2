<?php
/**
 * User Registration Controller
 * Handles backend validation, email checking, and secure password hashing.
 */

include 'db.php';
include 'header.php';

$message = ""; // Use clear variable names like $message instead of $msg

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitize inputs
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = "student"; // Default role for new signups

    /* --- Backend Validation --- */
    if (empty($name) || empty($email) || empty($password)) {
        $message = "All fields are required.";
        $type    = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $type    = "danger";
    } elseif (strlen($password) < 6) {
        $message = "Security risk: Password must be at least 6 characters.";
        $type    = "danger";
    } else {
        
        // Check if the email is already in use
        $checkQuery = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $checkQuery->bind_param("s", $email);
        $checkQuery->execute();
        $checkQuery->store_result();

        if ($checkQuery->num_rows > 0) {
            $message = "This email is already registered. Try logging in.";
            $type    = "warning";
        } else {
            // Use BCRYPT for secure password storage
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user record
            $insertStmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

            if ($insertStmt->execute()) {
                $message = "Registration successful! You can now login.";
                $type    = "success";
            } else {
                $message = "System error. Please try again later.";
                $type    = "danger";
            }
            $insertStmt->close();
        }
        $checkQuery->close();
    }
}
?>

<div class="container mt-5">
    <div class="card shadow-sm border-0 p-4 mx-auto" style="max-width: 450px;">
        <h3 class="text-center mb-4">Create an Account</h3>

        <?php if ($message): ?>
            <div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" id="registrationForm" novalidate>
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input class="form-control" name="name" placeholder="Enter your name" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input class="form-control" type="email" name="email" placeholder="example@mail.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input class="form-control" type="password" name="password" placeholder="Minimum 6 characters" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">Sign Up</button>
        </form>

        <div class="mt-4 text-center">
            <span class="text-muted">Already have an account?</span> 
            <a href="login.php" class="text-decoration-none">Login here</a>
        </div>
    </div>
</div>

<script>
/**
 * Frontend Validation
 * Stops the form from submitting if basic rules aren't met.
 */
document.getElementById("registrationForm").addEventListener("submit", function(event) {
    const name  = document.querySelector("[name='name']").value.trim();
    const email = document.querySelector("[name='email']").value.trim();
    const pass  = document.querySelector("[name='password']").value.trim();

    if (!name || !email || !pass) {
        alert("Please fill in all fields.");
        event.preventDefault();
        return;
    }

    if (pass.length < 6) {
        alert("Password must be at least 6 characters long.");
        event.preventDefault();
    }
});
</script>

<?php include 'footer.php'; ?>
