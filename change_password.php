<?php
session_start();
require_once("config.php");

$error_message = "";
$success_message = "";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password != $confirm_password) {
        $error_message = "Error: New password and confirm password do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $current_hashed_password = $row['password'];
            if (password_verify($current_password, $current_hashed_password)) {
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_password, $user_id);
                $update_stmt->execute();
                
                $success_message = "Password changed successfully!";
            } else {
                $error_message = "Current password is incorrect.";
            }
        } else {
            $error_message = "User not found.";
        }
    }
}
?>

<?php include('includes/header.php'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Change Password</h2>
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-floating mb-3">
                        <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Current Password" required>
                            <label>Current Password</label>
                        </div>

                        <div class="form-floating mb-3">
                        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="New Password" required>
                            <label>New Password</label>
                        </div>

                        <div class="form-floating mb-3">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm New Password" required>
                            <label>Confirm New Password</label>
                        </div>

                        <button type="submit" class="btn btn-primary text-dark w-100" style="background-color: #ffb82e; border-color: #ffb82e;">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
