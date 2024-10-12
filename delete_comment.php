<?php
require_once("config.php");

if (isset($_POST['comment_id']) && !empty($_POST['comment_id'])) {
 
    $comment_id = mysqli_real_escape_string($conn, $_POST['comment_id']);
    
    $delete_query = "DELETE FROM comments WHERE id = '$comment_id'";

    if (mysqli_query($conn, $delete_query)) {
   
        header("Location: myfeedback.php");
        exit();
    } else {
     
        echo "Error: " . $delete_query . "<br>" . mysqli_error($conn);
    }
} else {
    
    echo "Error: Comment ID not provided.";
}
?>