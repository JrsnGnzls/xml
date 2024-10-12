<?php
require_once("../config.php");


if(isset($_GET['id'])){
        $id = $_GET['id'];
}

$query = "DELETE FROM users WHERE id = '$id'";
if ($conn->query($query) === TRUE) {
    $success_message = "User deleted successfully!";
    header('Location: users.php');
} else {
    $error = "Something went wrong: " . $conn->error;
}

?>

