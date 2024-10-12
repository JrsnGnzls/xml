<?php
require_once("../config.php");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];

    $delete_sql = "DELETE FROM ratings WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success_message = "Rating deleted successfully.";
        $stmt->close();
    } else {
        $error = "Error deleting rating: " . $conn->error;
    }
} else {
    $error = "Invalid request.";
}


if (!empty($success_message)) {
    header("Location: ratings.php?success=" . urlencode($success_message));
    exit();
} else {
    header("Location: ratings.php?error=" . urlencode($error));
    exit();
}
?>
