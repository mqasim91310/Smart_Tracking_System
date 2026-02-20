<?php
session_start();
include("config.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_GET['id'])){
    $habit_id = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM habits WHERE id='$habit_id' AND user_id='$user_id'");
    $habit = mysqli_fetch_assoc($result);
}

if(isset($_POST['update_habit'])){
    $habit_id = $_POST['habit_id'];
    $habit_name = $_POST['habit_name'];
    $habit_type = $_POST['habit_type'];

    mysqli_query($conn, "UPDATE habits SET 
        habit_name='$habit_name',
        habit_type='$habit_type'
        WHERE id='$habit_id' AND user_id='$user_id'");

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
    <title>Edit Habit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

          <div class="container mt-5">
          <div class="col-md-6 mx-auto">
          <div class="card shadow">
          <div class="card-header bg-info text-white">
              <h4>Edit Habit</h4>
</div>
            <div class="card-body">

               <form method="POST">
              <input type="hidden" name="habit_id" value="<?php echo $habit['id']; ?>">

                  <div class="mb-3">
               <label>Habit Name</label>
<input type="text" name="habit_name" class="form-control" value="<?php echo $habit['habit_name']; ?>" required>
</div>

                 <div class="mb-3">
          <label>Habit Type</label>
          <select name="habit_type" class="form-control">
               <option value="Daily" <?php if($habit['habit_type']=="Daily") echo "selected"; ?>>Daily</option>
               <option value="Weekly" <?php if($habit['habit_type']=="Weekly") echo "selected"; ?>>Weekly</option>
</select>
        </div>

             <button type="submit" name="update_habit" class="btn btn-info w-100">Update Habit</button>
            <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Back</a>

                                 </form>

                        </div>
                    </div>
                </div>
            </div>

        </body>
</html>