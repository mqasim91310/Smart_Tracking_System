<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$msg = "";
$user_id = $_SESSION['user_id'];

if(isset($_POST['add_habit'])){
    $habit_name = mysqli_real_escape_string($conn, $_POST['habit_name']);
    $habit_type = $_POST['habit_type'];

    $insert = mysqli_query($conn, "INSERT INTO habits(user_id,habit_name,habit_type) 
        VALUES('$user_id','$habit_name','$habit_type')");

    if($insert){
        header("Location: dashboard.php");
        exit();
    } else {
        $msg = "Habit not added!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Habit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4>Add Habit</h4>
                </div>
                <div class="card-body">

                    <?php if($msg!=""){ ?>
                        <div class="alert alert-danger"><?php echo $msg; ?></div>
                    <?php } ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Habit Name</label>
                            <input type="text" name="habit_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Habit Type</label>
                            <select name="habit_type" class="form-control">
                                <option value="Daily">Daily</option>
                                <option value="Weekly">Weekly</option>
                            </select>
                        </div>

                        <button type="submit" name="add_habit" class="btn btn-success w-100">Add Habit</button>
                    </form>

                    <a href="dashboard.php" class="btn btn-secondary w-100 mt-3">Back</a>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
