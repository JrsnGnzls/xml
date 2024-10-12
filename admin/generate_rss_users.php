<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function generateRSS() {
    require_once("../config.php");

    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
  
        $rss = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $rss .= '<rss version="2.0">' . "\n";
        $rss .= '<channel>' . "\n";
        $rss .= '<title>Users RSS Feed</title>' . "\n";
        $rss .= '<link>http://example.com</link>' . "\n";
        $rss .= '<description>List of users from database</description>' . "\n";

        while($row = $result->fetch_assoc()) {
            $rss .= '<item>' . "\n";
            $rss .= '<title>User ID: ' . $row['id'] . '</title>' . "\n";
            $rss .= '<link>http://example.com/user/' . $row['id'] . '</link>' . "\n";
            $rss .= '<description>Username: ' . $row['username'] . '<br>Role: ' . ($row['role'] == 1 ? "admin" : "user") . '</description>' . "\n";
            $rss .= '</item>' . "\n";
        }

        $rss .= '</channel>' . "\n";
        $rss .= '</rss>' . "\n";

        header('Content-Type: application/rss+xml; charset=utf-8');
        echo $rss;
    } else {
        echo "No users found";
    }
}

generateRSS();
?>
