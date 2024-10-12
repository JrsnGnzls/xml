<?php
session_start();
require_once("config.php");

if(isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

$error = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $email = $_POST["email"];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        $query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $error = "Username or email already exists!";
        } else {
            if ($password !== $confirm_password) {
                $error = "Passwords do not match!";
            } else {
                $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$password_hash', '$email')";
                if(mysqli_query($conn, $sql)) {
                    $success_message = "User registered successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>

<?php include('includes/header.php') ?>
<div class="mb-5"></div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 mt-5">
            <div class="card">
                <div class="card-header h4 text-center mb-3" style="background-color: #ffb82e;">
                    Create an Account
                </div>
                <div class="card-body">
                    <?php if(!empty($error)) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php } ?>
                    <?php if(!empty($success_message)) { ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success_message; ?>
                        </div>
                    <?php } ?>
                    <form action="" method="post">
                        <div class="form-floating mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                            <label>Username</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                            <label>Email</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                            <label>Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                            <label>Confirm Password</label>
                        </div>
                        <input type="submit" class="btn btn-success w-100" name="register" value="Create Account" style="font-size: 18px; font-weight: 500;">
                    </form>
                </div>
                <div class="card-footer">
                    <small class="h6 col-md-6 offset-md-3">Already have an account? <a href="login.php">Login your Account</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php') ?>
