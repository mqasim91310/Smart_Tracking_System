<?php
session_start();
include("config.php");

$msg = "";

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($query) > 0){
        $row = mysqli_fetch_assoc($query);

        if(password_verify($password, $row['password'])){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['fullname'] = $row['fullname'];

            header("Location: dashboard.php");
            exit();
        } else {
            $msg = "Invalid password!";
        }
    } else {
        $msg = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Login</h4>
                </div>
                <div class="card-body">

                    <?php if($msg!=""){ ?>
                        <div class="alert alert-danger"><?php echo $msg; ?></div>
                    <?php } ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" name="login" class="btn btn-dark w-100">Login</button>
                    </form>

                    <p class="mt-3 text-center">
                        Don't have account? <a href="register.php">Register</a>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
