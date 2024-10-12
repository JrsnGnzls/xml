<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../config.php");

$sql = "SELECT comments.id, comments.item_id, tbl_news.title AS game_title, users.username AS username, comments.comment, comments.created_at 
        FROM comments 
        INNER JOIN users ON comments.user_id = users.id
        INNER JOIN tbl_news ON comments.item_id = tbl_news.id
        ORDER BY comments.id DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Error: " . $conn->error);
}

$rss = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$rss .= '<rss version="2.0">' . "\n";
$rss .= '<channel>' . "\n";
$rss .= '<title>Comments RSS Feed</title>' . "\n";
$rss .= '<link>http://example.com</link>' . "\n";
$rss .= '<description>List of comments from database</description>' . "\n";

while ($row = $result->fetch_assoc()) {
    $rss .= '<item>' . "\n";
    $rss .= '<title>Comment ID: ' . $row['id'] . '</title>' . "\n";
    $rss .= '<link>http://example.com/comment/' . $row['id'] . '</link>' . "\n";
    $rss .= '<description><![CDATA[';
    $rss .= 'Game Title: ' . $row['game_title'] . '<br>';
    $rss .= 'Username: ' . $row['username'] . '<br>';
    $rss .= 'Comment: ' . $row['comment'] . '<br>';
    $rss .= 'Created At: ' . $row['created_at'];
    $rss .= ']]></description>' . "\n";
    $rss .= '</item>' . "\n";
}

$rss .= '</channel>' . "\n";
$rss .= '</rss>' . "\n";

header('Content-Type: application/rss+xml; charset=utf-8');
echo $rss;
?>
