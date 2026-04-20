<?php
include 'config.php';
session_start();

if($_SESSION['role'] != 'admin'){
    exit();
}

$id = $_POST['id'] ?? 0;

if($id > 0){
    $conn->query("DELETE FROM assignments WHERE id=$id");
}

header("Location: dashboard.php");
exit();
?>