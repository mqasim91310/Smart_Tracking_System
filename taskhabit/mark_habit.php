<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if(isset($_GET['id'])){
    $habit_id = $_GET['id'];
    $dateToday = date("Y-m-d");

    // check already marked 
    $check = mysqli_query($conn, "SELECT * FROM habit_logs WHERE habit_id='$habit_id' AND log_date='$dateToday'");

    if(mysqli_num_rows($check) == 0){
        mysqli_query($conn, "INSERT INTO habit_logs(habit_id,log_date,status) VALUES('$habit_id','$dateToday','Done')");
    }

    header("Location: dashboard.php");
    exit();
}
?>
