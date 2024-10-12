<?php
session_start();
require_once("config.php");

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$email = $password = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row && password_verify($password, $row["password"])) {
        $_SESSION["username"] = $row["username"];
        $_SESSION["role"] = $row["role"];
        $_SESSION["user_id"] = $row["id"];

        if ($row["role"] == 1) {
            header("Location: admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $error = "Invalid email or password!";
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
                    Login your Account
                </div>
                <div class="card-body">
                    <?php if (!empty($error)) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php } ?>
                    <form action="" method="post">
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                            <label>Email</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                            <label>Password</label>
                        </div>
                        <div>
                            <input type="submit" class="btn btn-success w-100" name="login" value="Login" style="font-size: 18px; font-weight: 500;">
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <small class="h6 col-md-6 offset-md-3">Don't have an account? <a href="registration.php">Create an Account</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php') ?>
