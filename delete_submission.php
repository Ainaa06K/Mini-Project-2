<?php
include 'config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$file = $_GET['file'] ?? '';

if(empty($file)){
    header("Location: dashboard.php");
    exit();
}

/* =========================
   ADMIN CAN DELETE ANY FILE
   STUDENT ONLY OWN FILE
========================= */

if($role == 'admin'){

    $stmt = $conn->prepare("DELETE FROM submissions WHERE file = ?");
    $stmt->bind_param("s", $file);

} else {

    $stmt = $conn->prepare("DELETE FROM submissions WHERE file = ? AND user_id = ?");
    $stmt->bind_param("si", $file, $user_id);

}

$stmt->execute();
$stmt->close();

/* DELETE FILE FROM FOLDER */
$filePath = "uploads/" . $file;

if(file_exists($filePath)){
    unlink($filePath);
}

/* BACK TO DASHBOARD */
header("Location: dashboard.php?deleted=1");
exit();
?>