<?php
session_start();
require_once("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['rating']) || !isset($_POST['news_id'])) {
        $response = array("success" => false, "message" => "Missing rating or news_id");
        echo json_encode($response);
        exit();
    }

    if (!isset($_SESSION['username'])) {
        $response = array("success" => false, "message" => "User not logged in");
        echo json_encode($response);
        exit();
    }
    $username = $_SESSION['username'];

    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $news_id = mysqli_real_escape_string($conn, $_POST['news_id']);

    $user_id_query = "SELECT id FROM users WHERE username = '$username'";
    $user_id_result = mysqli_query($conn, $user_id_query);

    if ($user_id_result && mysqli_num_rows($user_id_result) > 0) {
        $user_row = mysqli_fetch_assoc($user_id_result);
        $user_id = $user_row['id'];

        $check_rating_query = "SELECT id FROM ratings WHERE user_id = '$user_id' AND news_id = '$news_id'";
        $check_rating_result = mysqli_query($conn, $check_rating_query);

        if (mysqli_num_rows($check_rating_result) > 0) {
            $response = array("success" => false, "message" => "You have already rated this news article");
            echo json_encode($response);
            exit();
        }

        $insert_rating_query = "INSERT INTO ratings (user_id, news_id, rating, created_at) 
                               VALUES ('$user_id', '$news_id', '$rating', CURRENT_TIMESTAMP())";
        if (mysqli_query($conn, $insert_rating_query)) {
            $response = array("success" => true);
            echo json_encode($response);
            exit();
        } else {
            $response = array("success" => false, "message" => "Error inserting rating: " . mysqli_error($conn));
            echo json_encode($response);
            exit();
        }
    } else {
        $response = array("success" => false, "message" => "Unable to retrieve user ID");
        echo json_encode($response);
        exit();
    }
} else {
    $response = array("success" => false, "message" => "Invalid request method");
    echo json_encode($response);
    exit();
}
?>
