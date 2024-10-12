<?php
session_start();
require_once("config.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply_content']) && isset($_POST['comment_id'])) {
    $username = $_SESSION['username'];
    $reply_content = mysqli_real_escape_string($conn, $_POST["reply_content"]);
    $comment_id = mysqli_real_escape_string($conn, $_POST["comment_id"]);

    $user_id_query = "SELECT id FROM users WHERE username = '$username'";
    $user_id_result = mysqli_query($conn, $user_id_query);

    if ($user_id_result && mysqli_num_rows($user_id_result) > 0) {
        $user_row = mysqli_fetch_assoc($user_id_result);
        $user_id = $user_row['id'];

        $insert_reply_query = "INSERT INTO user_replies (comment_id, user_id, reply, created_at) 
                              VALUES ('$comment_id', '$user_id', '$reply_content', CURRENT_TIMESTAMP())";

        if (mysqli_query($conn, $insert_reply_query)) {
            $comment_item_id_query = "SELECT item_id FROM comments WHERE id = '$comment_id'";
            $comment_item_id_result = mysqli_query($conn, $comment_item_id_query);

            if ($comment_item_id_result && mysqli_num_rows($comment_item_id_result) > 0) {
                $comment_item_id_row = mysqli_fetch_assoc($comment_item_id_result);
                $item_id = $comment_item_id_row['item_id'];
                header("Location: comment.php?id=$item_id");
                exit();
            } else {
                echo "Error: Unable to retrieve item ID from comments table.";
            }
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Error: Unable to retrieve user ID.";
    }
}
?>
