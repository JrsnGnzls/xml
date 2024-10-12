<?php
require_once("../config.php");

$sql = "SELECT r.id, u.username AS user_name, n.title AS news_title, r.rating, r.created_at 
        FROM ratings r
        INNER JOIN users u ON r.user_id = u.id
        INNER JOIN tbl_news n ON r.news_id = n.id
        ORDER BY r.id DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Error: " . $conn->error);
}

$rss = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$rss .= '<rss version="2.0">' . "\n";
$rss .= '<channel>' . "\n";
$rss .= '<title>Ratings RSS Feed</title>' . "\n";
$rss .= '<link>http://example.com</link>' . "\n";
$rss .= '<description>List of ratings from database</description>' . "\n";

while ($row = $result->fetch_assoc()) {
    $rss .= '<item>' . "\n";
    $rss .= '<title>Rating ID: ' . $row['id'] . '</title>' . "\n";
    $rss .= '<link>http://example.com/rating/' . $row['id'] . '</link>' . "\n";
    $rss .= '<description><![CDATA[';
    $rss .= 'User Name: ' . $row['user_name'] . '<br>';
    $rss .= 'News Title: ' . $row['news_title'] . '<br>';
    $rss .= 'Rating: ' . $row['rating'] . '<br>';
    $rss .= 'Created At: ' . $row['created_at'];
    $rss .= ']]></description>' . "\n";
    $rss .= '</item>' . "\n";
}

$rss .= '</channel>' . "\n";
$rss .= '</rss>' . "\n";

header('Content-Type: application/rss+xml; charset=utf-8');
echo $rss;
?>
