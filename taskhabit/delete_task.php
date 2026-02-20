<?php
session_start();
include("config.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if(isset($_GET['id'])){
    $task_id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM tasks WHERE id='$task_id' AND user_id='".$_SESSION['user_id']."'");
    header("Location: dashboard.php");
    exit();
}
?>
