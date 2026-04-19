<?php
include 'config.php';
session_start();

/* CHECK LOGIN */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

/* CHECK ROLE (ONLY ADMIN) */
if($_SESSION['role'] !== 'admin'){
    header("Location: dashboard.php");
    exit();
}

$assignment_id = $_GET['id'] ?? 0;

if($assignment_id == 0){
    header("Location: dashboard.php");
    exit();
}

/* STEP 1: DELETE SUBMISSIONS FIRST (FOREIGN KEY SAFE) */
$stmt1 = $conn->prepare("DELETE FROM submissions WHERE assignment_id = ?");
$stmt1->bind_param("i", $assignment_id);
$stmt1->execute();
$stmt1->close();

/* STEP 2: DELETE ASSIGNMENT */
$stmt2 = $conn->prepare("DELETE FROM assignments WHERE id = ?");
$stmt2->bind_param("i", $assignment_id);
$stmt2->execute();
$stmt2->close();

/* BACK */
header("Location: dashboard.php?deleted_assignment=1");
exit();
?>