<?php
session_start();
require_once("config.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Favorite ID not provided";
    exit();
}

$favorite_id = $_GET['id'];

$username = $_SESSION['username'];
$user_id_query = "SELECT id FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $user_id_query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$user_id_result = mysqli_stmt_get_result($stmt);

if ($user_id_result && mysqli_num_rows($user_id_result) > 0) {
    $user_row = mysqli_fetch_assoc($user_id_result);
    $user_id = $user_row['id'];

    $delete_query = "DELETE FROM favorites WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "ii", $favorite_id, $user_id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: add-to-favorites.php");
        exit();
    } else {
        echo "Failed to delete favorite";
        exit();
    }
} else {
    echo "User not found";
    exit();
}
?>