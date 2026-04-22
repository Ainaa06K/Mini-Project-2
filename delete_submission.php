<?php
include 'db.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$submission_id = $_POST['submission_id'] ?? 0;

if($submission_id <= 0){
    header("Location: dashboard.php");
    exit();
}

/* GET FILE NAME FIRST */
$stmt = $conn->prepare("SELECT file, user_id FROM submissions WHERE id=?");
$stmt->bind_param("i", $submission_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if(!$result){
    exit("Not found");
}

$file = $result['file'];

/* SECURITY CHECK */
if($role != 'admin' && $result['user_id'] != $user_id){
    exit("Unauthorized");
}

/* DELETE DB */
$stmt = $conn->prepare("DELETE FROM submissions WHERE id=?");
$stmt->bind_param("i", $submission_id);
$stmt->execute();

/* DELETE FILE */
$filePath = "uploads/" . $file;
if(file_exists($filePath)){
    unlink($filePath);
}

header("Location: dashboard.php?deleted=1");
exit();
?>