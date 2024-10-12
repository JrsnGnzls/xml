<?php
require_once("../config.php");


if(isset($_GET['id'])){
        $id = $_GET['id'];
}

$query = "DELETE FROM favorites WHERE id = '$id'";
if ($conn->query($query) === TRUE) {
    $success_message = "Favorite deleted successfully!";
    header('Location: favorites.php');
} else {
    $error = "Something went wrong: " . $conn->error;
}

?>

