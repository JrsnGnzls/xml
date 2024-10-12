<?php
require_once("../config.php");

if(isset($_GET['id'])){
    $id = $_GET['id'];

    $query = "SELECT * FROM users where id = '$id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $role = $row['role'];
}
?>

<?php
if(isset($_POST['update_user'])){

    if(isset($_GET['id_new'])){
        $idnew = $_GET['id_new'];
    }

    $username = $_POST['username'];
    $password = $_POST["password"];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $role = $_POST["role"] == true ? 1 : 0;

    $query = "UPDATE users SET username = '$username', role = '$role' WHERE id = '$idnew'";
    $result = mysqli_query($conn, $query);
    header('Location: users.php');
}
?>

<?php include('includes/header.php') ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>
                    Edit User
                    <a href="users.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">

                <form action="users-edit.php?id_new=<?php echo $id; ?>" method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo $row['username']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" value="<?php echo $row['password']; ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Select Role</label>
                        <select name="role" class="form-select">
                            <option value="">Select Role</option>
                            <option value="1" <?php if($role == 1) echo 'selected'; ?>>Admin</option>
                            <option value="0" <?php if($role == 0) echo 'selected'; ?>>User</option>
                        </select>
                    </div>
                    <div class="mb-3 text-end">
                    <input type="submit" class="btn btn-primary" name="update_user" value="Update User">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php')?>