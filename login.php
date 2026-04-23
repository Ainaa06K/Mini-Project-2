<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
include 'header.php';

$msg = "";

// Start login process when form is submitted
if(isset($_POST['login'])){

    // Get and clean user input
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if fields are empty
    if(empty($email) || empty($password)){
        $msg = "<div class='alert error'>All fields required</div>";

    } else {

        // Get user data from database using email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();

        // Check if user exists and password is correct
        if($user && password_verify($password, $user['password'])){

            // Store user session data after successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect user to dashboard
            header("Location: dashboard.php");
            exit();

        } else {
            // Show error message for invalid login
            $msg = "<div class='alert error'>Invalid email or password</div>";
        }
    }
}
?>

<style>
body{
    margin:0;
    font-family:'Segoe UI', Tahoma, sans-serif;
    background: radial-gradient(circle at top, #eef2f7, #dbe7f5);
}

/* layout wrapper */
.wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    gap:50px;
    padding:20px;
}

/* left info panel */
.intro{
    width:360px;
    padding:30px;
    border-radius:20px;
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    color:white;
    box-shadow:0 20px 50px rgba(0,0,0,0.2);
    position:relative;
    overflow:hidden;
}

/* glow effect decoration */
.intro::before{
    content:"";
    position:absolute;
    width:200px;
    height:200px;
    background:rgba(255,255,255,0.15);
    border-radius:50%;
    top:-50px;
    right:-50px;
    filter: blur(30px);
}

/* title */
.intro h2{
    margin:0;
    font-size:24px;
    font-weight:700;
    position:relative;
}

/* description text */
.intro p{
    margin-top:12px;
    font-size:13px;
    line-height:1.7;
    opacity:0.9;
    position:relative;
}

/* small tag */
.tag{
    display:inline-block;
    margin-top:18px;
    padding:6px 12px;
    font-size:11px;
    background:rgba(255,255,255,0.15);
    border-radius:20px;
}

/* login card */
.card{
    width:340px;
    background:rgba(255,255,255,0.85);
    backdrop-filter: blur(12px);
    padding:30px;
    border-radius:18px;
    box-shadow:0 20px 60px rgba(0,0,0,0.15);
    text-align:center;
}

/* login title */
.card h3{
    margin-bottom:18px;
    font-weight:600;
    color:#1e3c72;
}

/* input container */
.input-group{
    margin-bottom:12px;
}

/* input fields */
.input-group input{
    width:100%;
    padding:11px;
    border-radius:10px;
    border:1px solid #e0e0e0;
    outline:none;
    font-size:13px;
    transition:0.2s;
}

/* focus effect */
.input-group input:focus{
    border-color:#2a5298;
    box-shadow:0 0 0 3px rgba(42,82,152,0.12);
}

/* login button */
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
    transform: translateY(-1px);
    box-shadow:0 10px 20px rgba(0,0,0,0.15);
}

/* alert message */
.alert{
    padding:10px;
    border-radius:10px;
    margin-bottom:12px;
    font-size:13px;
}

/* error style */
.error{
    background:#fdecea;
    color:#e74c3c;
}

/* bottom link text */
.link{
    margin-top:14px;
    font-size:12px;
    color:#666;
}

/* link style */
.link a{
    color:#2a5298;
    text-decoration:none;
    font-weight:500;
}

/* responsive design */
@media(max-width:768px){
    .wrapper{
        flex-direction:column;
        gap:25px;
    }

    .intro{
        width:100%;
        text-align:center;
    }
}
</style>

<div class="wrapper">

    <!-- left introduction panel -->
    <div class="intro">
        <h2>Assignment Submission System</h2>
        <p>
            A simple platform for students to submit assignments and lecturers to manage submissions efficiently in one place.
        </p>

        <div class="tag">Secure • Fast • Simple</div>
    </div>

    <!-- login form card -->
    <div class="card">

        <h3>Login</h3>

        <?= $msg ?>

        <form method="POST" id="loginForm">

            <div class="input-group">
                <input type="email" name="email" placeholder="Email">
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Password">
            </div>

            <button class="btn" name="login">Login</button>

        </form>

        <div class="link">
            No account? <a href="register.php">Register</a>
        </div>

    </div>

</div>

<script>
// Basic frontend validation before form submit
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