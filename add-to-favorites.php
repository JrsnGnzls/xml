<?php
session_start();
require_once("config.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$user_id_query = "SELECT id FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $user_id_query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$user_id_result = mysqli_stmt_get_result($stmt);

if ($user_id_result && mysqli_num_rows($user_id_result) > 0) {
    $user_row = mysqli_fetch_assoc($user_id_result);
    $user_id = $user_row['id'];

    $favorites_query = "SELECT favorites.*, tbl_news.title AS news_title, tbl_news.description 
                    FROM favorites 
                    JOIN tbl_news ON favorites.news_id = tbl_news.id 
                    WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $favorites_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$favorites_result = mysqli_stmt_get_result($stmt);
} else {
    echo "User not found";
    exit();
}

if (isset($_POST['newsId'])) {
    $newsId = $_POST['newsId'];

    $username = $_SESSION['username'];
    $user_id_query = "SELECT id FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $user_id_query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $user_id_result = mysqli_stmt_get_result($stmt);

    if ($user_id_result && mysqli_num_rows($user_id_result) > 0) {
        $user_row = mysqli_fetch_assoc($user_id_result);
        $user_id = $user_row['id'];

        $check_query = "SELECT * FROM favorites WHERE user_id = ? AND news_id = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $newsId);
        mysqli_stmt_execute($stmt);
        $check_result = mysqli_stmt_get_result($stmt);

        if ($check_result && mysqli_num_rows($check_result) > 0) {
            echo "exists";
            exit();
        }

        $insert_query = "INSERT INTO favorites (user_id, news_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $newsId);
        if (mysqli_stmt_execute($stmt)) {
            echo "success";
            exit();
        } else {
            echo "error";
            exit();
        }
    }
}
?>

<?php include('includes/header.php') ?>    
<div class="mb-5">
</div>

    <div class="container">
    <h2 class="my-4 text-center">My Favorites</h2>
    <div class="row justify-content-center">
        <?php 
        if (isset($favorites_result) && mysqli_num_rows($favorites_result) > 0) {
            while ($favorite_row = mysqli_fetch_assoc($favorites_result)) {
                $favorite_id = $favorite_row['id'];
        ?>
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <?php echo $favorite_row['news_title']; ?>
                </div>
                <div class="card-body">
                    <p><?php echo $favorite_row['description']; ?>...</p>
                    <a href="comment.php?id=<?php echo $favorite_row['news_id']; ?>" class="btn btn-success">View</a>
                    <a href="delete_favorite.php?id=<?php echo $favorite_row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove this from favorites?')">Remove from Favorites</a>
                </div>
            </div>
        </div>
        <?php 
            }
        } else {
            echo "<p class='col text-center'>No favorites yet.</p>";
        }
        ?>
    </div>
</div>

<?php include('includes/footer.php') ?>    