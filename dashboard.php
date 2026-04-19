<?php
include 'config.php';
include 'header.php';

$name = $_SESSION['name'] ?? 'User';
$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? 'student';

$sql = "SELECT * FROM submissions ORDER BY id DESC LIMIT 4";
$result = $conn->query($sql);
?>

<style>
body{
    background:#f4f6f9;
    font-family: Arial;
}

/* WELCOME BOX */
.welcome{
    background:#2c3e50;
    color:white;
    padding:20px;
    border-radius:10px;
}

/* ROLE LABEL */
.role-box{
    margin-top:8px;
    display:inline-block;
    padding:5px 12px;
    border-radius:20px;
    font-size:14px;
    background:#f39c12;
    color:white;
}

/* BOX */
.box{
    background:white;
    padding:20px;
    border-radius:10px;
    margin-top:20px;
}

/* BUTTON */
.btn-view-custom{
    display:inline-block;
    margin-top:15px;
    padding:10px 15px;
    background:#8e44ad;
    color:white;
    text-decoration:none;
    border-radius:8px;
}
/* Guna nama yang spesifik supaya tak gaduh dengan navbar */
.btn-dashboard-view {
    display: inline-block;    /* Wajib ada supaya padding & margin berfungsi */
    margin-top: 15px;
    padding: 10px 20px;
    background-color: #8e44ad; /* Warna ungu */
    color: #ffffff !important; /* Paksa teks jadi putih */
    text-decoration: none !important; /* Buang garis bawah */
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: 0.3s;
}

.btn-dashboard-view:hover {
    background-color: #732d91; /* Ungu gelap sikit masa hover */
    color: #ffffff !important;
}

</style>

<div class="container mt-3">

    <!-- WELCOME -->
    <div class="welcome">
        <h3>Welcome <?php echo htmlspecialchars($name); ?></h3>

        <!-- ROLE DISPLAY -->
        <div class="role-box">
            Role: <?php echo ucfirst($role); ?>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="box">

        <h4>Recent Submission</h4>

        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Date</th>
            </tr>

            <?php
            if($result && $result->num_rows > 0){
                while($row = $result->fetch_assoc()){
            ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='3'>No submission yet</td></tr>";
            }
            ?>

        </table>

        <!-- BUTTON TEXT ROLE BASED -->
       <?php if($role == "admin"){ ?>
        <a href="view_submission.php" class="btn-dashboard-view">View All Submission</a>
        <?php } else { ?>
            <a href="view_submission.php" class="btn-dashboard-view">View all your submission</a>
        <?php } ?>

    </div>

</div>

<?php include 'footer.php'; ?>