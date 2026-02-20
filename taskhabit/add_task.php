<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$msg = "";
$user_id = $_SESSION['user_id'];

if(isset($_POST['add_task'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    $insert = mysqli_query($conn, "INSERT INTO tasks(user_id,title,description,priority,due_date) 
        VALUES('$user_id','$title','$description','$priority','$due_date')");

    if($insert){
        header("Location: dashboard.php");
        exit();
    } else {
        $msg = "Task not added!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4>Add Task</h4>
                </div>
                <div class="card-body">

                    <?php if($msg!=""){ ?>
                        <div class="alert alert-danger"><?php echo $msg; ?></div>
                    <?php } ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Task Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Priority</label>
                            <select name="priority" class="form-control">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Due Date</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>

                        <button type="submit" name="add_task" class="btn btn-primary w-100">Add Task</button>
                    </form>

                    <a href="dashboard.php" class="btn btn-secondary w-100 mt-3">Back</a>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
