<?php
require_once("../config.php");

include('includes/header.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST["username"];
    $password = $_POST["password"];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $role = $_POST["role"] == true ? 1:0;

    $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password_hash','$role')";
    if ($conn->query($query) === TRUE) {
        $success_message = "User created successfully!";
    } else {
        $error = "Something went wrong: " . $conn->error;
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>
                    Add User
                    <a href="users.php" class="btn btn-danger float-end">Back</a>
                </h4>
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
                <form action=""  method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Select Role</label>
                        <select name="role" class="form-select">
                            <option value="">Select Role</option>
                            <option value="1">Admin</option>
                            <option value="0">User</option>
                        </select>
                    </div>
                    <div class="mb-3 text-end">
                    <input type="submit" class="btn btn-primary" value="Create">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php include('includes/footer.php') ?>