<?php
require_once("../config.php");

$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'News_Title';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

$sql = "SELECT r.news_id, n.title AS News_Title, ROUND(AVG(r.rating), 1) AS Average_Rating, COUNT(DISTINCT r.user_id) AS Number_of_Users_Rated
        FROM ratings r
        INNER JOIN tbl_news n ON r.news_id = n.id";

if (!empty($search)) {
    $sql .= " WHERE n.title LIKE '%$search%'";
}

$sql .= " GROUP BY r.news_id
          ORDER BY $sort_column $sort_order";

$result = $conn->query($sql);

$rss = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$rss .= '<rss version="2.0">' . "\n";
$rss .= '<channel>' . "\n";
$rss .= '<title>Most Rated Games RSS Feed</title>' . "\n";
$rss .= '<link>http://example.com</link>' . "\n";
$rss .= '<description>List of most rated games from database</description>' . "\n";

while ($row = $result->fetch_assoc()) {
    $rss .= '<item>' . "\n";
    $rss .= '<title>' . htmlspecialchars($row['News_Title']) . '</title>' . "\n";
    $rss .= '<link>http://example.com/game/' . $row['news_id'] . '</link>' . "\n";
    $rss .= '<description><![CDATA[';
    $rss .= 'Average Rating: ' . $row['Average_Rating'] . '<br>';
    $rss .= 'Number of Users Rated: ' . $row['Number_of_Users_Rated'];
    $rss .= ']]></description>' . "\n";
    $rss .= '</item>' . "\n";
}

$rss .= '</channel>' . "\n";
$rss .= '</rss>' . "\n";

header('Content-Type: application/rss+xml; charset=utf-8');
echo $rss;
?>
