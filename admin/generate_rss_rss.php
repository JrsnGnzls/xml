<?php
require_once("../config.php");

$error = '';
$success_message = '';
$search = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search = $_POST['search'];
}

$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sort_order = isset($_GET['order']) && strtoupper($_GET['order']) == 'DESC' ? 'DESC' : 'ASC';

$sql = "SELECT * FROM tbl_news";

if (!empty($search)) {
    $sql .= " WHERE title LIKE '%$search%'";
}

$sql .= " ORDER BY $sort_column $sort_order";

$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $rss = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $rss .= '<rss version="2.0">' . "\n";
    $rss .= '<channel>' . "\n";
    $rss .= '<title>News RSS Feed</title>' . "\n";
    $rss .= '<link>http://example.com</link>' . "\n";
    $rss .= '<description>List of news items from database</description>' . "\n";

    while ($row = $result->fetch_assoc()) {
        $rss .= '<item>' . "\n";
        $rss .= '<title>' . htmlspecialchars($row['title']) . '</title>' . "\n";
        $rss .= '<link>http://example.com/news/' . $row['id'] . '</link>' . "\n";
        $rss .= '<description><![CDATA[';
        $rss .= 'Image: <img src="' . $row['image'] . '"><br>';
        $rss .= 'Publish Date: ' . $row['publish'];
        $rss .= ']]></description>' . "\n";
        $rss .= '</item>' . "\n";
    }

    $rss .= '</channel>' . "\n";
    $rss .= '</rss>' . "\n";

    header('Content-Type: application/rss+xml; charset=utf-8');
    echo $rss;
} else {
    echo 'No news items found.';
}
?>
