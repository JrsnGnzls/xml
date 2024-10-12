<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../config.php");

$sql = "SELECT f.id, u.username AS user_name, n.title AS news_title, f.created_at 
        FROM favorites f 
        INNER JOIN users u ON f.user_id = u.id 
        INNER JOIN tbl_news n ON f.news_id = n.id
        ORDER BY f.id DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Error: " . $conn->error);
}

$rss = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$rss .= '<rss version="2.0">' . "\n";
$rss .= '<channel>' . "\n";
$rss .= '<title>Favorites RSS Feed</title>' . "\n";
$rss .= '<link>http://example.com</link>' . "\n";
$rss .= '<description>List of favorites from database</description>' . "\n";

while ($row = $result->fetch_assoc()) {
    $rss .= '<item>' . "\n";
    $rss .= '<title>Favorite ID: ' . $row['id'] . '</title>' . "\n";
    $rss .= '<link>http://example.com/favorite/' . $row['id'] . '</link>' . "\n";
    $rss .= '<description><![CDATA[';
    $rss .= 'User Name: ' . $row['user_name'] . '<br>';
    $rss .= 'News Title: ' . $row['news_title'] . '<br>';
    $rss .= 'Created At: ' . $row['created_at'];
    $rss .= ']]></description>' . "\n";
    $rss .= '</item>' . "\n";
}

$rss .= '</channel>' . "\n";
$rss .= '</rss>' . "\n";

header('Content-Type: application/rss+xml; charset=utf-8');
echo $rss;
?>
