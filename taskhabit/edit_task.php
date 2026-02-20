<?php
session_start();
include("config.php");
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_GET['id'])){
    $task_id = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM tasks WHERE id='$task_id' AND user_id='$user_id'");
    $task = mysqli_fetch_assoc($result);
}

if(isset($_POST['update_task'])){
    $task_id = $_POST['task_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    mysqli_query($conn, "UPDATE tasks SET 
        title='$title',
        description='$description',
        priority='$priority',
        due_date='$due_date'
        WHERE id='$task_id' AND user_id='$user_id'");

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
    <title>Edit Task</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
        <body class="bg-light">

    <div class="container mt-5">
    <div class="col-md-6 mx-auto">
    <div class="card shadow">
    <div class="card-header bg-warning">
        <h4>Edit Task</h4>
</div>
    <div class="card-body">

    <form method="POST">
        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo $task['title']; ?>" required>
</div>

        <div class="mb-3">
    <label>Description</label>
    <textarea name="description" class="form-control"><?php echo $task['description']; ?></textarea>
</div>

<div class="mb-3">
<label>Priority</label>
<select name="priority" class="form-control">
<option value="Low" <?php if($task['priority']=="Low") echo "selected"; ?>>Low</option>
<option value="Medium" <?php if($task['priority']=="Medium") echo "selected"; ?>>Medium</option>
<option value="High" <?php if($task['priority']=="High") echo "selected"; ?>>High</option>
</select>
</div>

<div class="mb-3">
<label>Due Date</label>
<input type="date" name="due_date" class="form-control" value="<?php echo $task['due_date']; ?>">
</div>

<button type="submit" name="update_task" class="btn btn-warning w-100">Update Task</button>
<a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Back</a>

</form>

</div>
</div>
</div>
</div>

</body>
</html>