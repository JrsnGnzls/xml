<?php
session_start();
require_once("../config.php");

function redirectIfNotLoggedIn() {
    if (!isset($_SESSION['username'])) {
        header("Location: ../login.php");
        exit();
    }
}

function redirectIfNotAdmin() {
    if (isset($_SESSION['role']) && $_SESSION['role'] != 1) {
        header("Location: ../index.php"); 
        exit();
    }
}

redirectIfNotLoggedIn();

redirectIfNotAdmin();

$sqlUsers = "SELECT COUNT(*) AS total_users FROM users";
$resultUsers = $conn->query($sqlUsers);
$rowUsers = $resultUsers->fetch_assoc();
$totalUsers = $rowUsers['total_users'];

$sqlFeedbacks = "SELECT COUNT(*) AS total_feedbacks FROM comments";
$resultFeedbacks = $conn->query($sqlFeedbacks);
$rowFeedbacks = $resultFeedbacks->fetch_assoc();
$totalFeedbacks = $rowFeedbacks['total_feedbacks'];

$sqlFavorites = "SELECT COUNT(*) AS total_favorites FROM favorites";
$resultFavorites = $conn->query($sqlFavorites);
$rowFavorites = $resultFavorites->fetch_assoc();
$totalFavorites = $rowFavorites['total_favorites'];

$sqlRss = "SELECT COUNT(*) AS total_rss FROM tbl_news";
$resultRss = $conn->query($sqlRss);
$rowRss = $resultRss->fetch_assoc();
$totalRss = $rowRss['total_rss'];

$sqlRatings = "SELECT COUNT(*) AS total_ratings FROM ratings";
$resultRatings = $conn->query($sqlRatings);
$rowRatings = $resultRatings->fetch_assoc();
$totalRatings = $rowRatings['total_ratings'];

$conn->close();
?>


<?php include('includes/header.php') ?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card card-body p-3">  
            <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Users</p>
            <h5 class="font-weight-bolder mb-0">
                <?php echo $totalUsers; ?>
            </h5>     
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card card-body p-3">  
            <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Comments</p>
            <h5 class="font-weight-bolder mb-0">
                <?php echo $totalFeedbacks; ?>
            </h5>     
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card card-body p-3">  
            <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Favorites</p>
            <h5 class="font-weight-bolder mb-0">
                <?php echo $totalFavorites; ?>
            </h5>     
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card card-body p-3">  
            <p class="text-sm mb-0 text-capitalize font-weight-bold">Total RSS</p>
            <h5 class="font-weight-bolder mb-0">
                <?php echo $totalRss; ?>
            </h5>     
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card card-body p-3">  
            <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Ratings</p>
            <h5 class="font-weight-bolder mb-0">
                <?php echo $totalRatings; ?>
            </h5>     
        </div>
    </div>
</div>

<?php include('includes/footer.php') ?>