<?php
require_once("../config.php");

$results_per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$search = isset($_POST['search']) ? $_POST['search'] : '';
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'News_Title';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

$count_sql = "SELECT COUNT(DISTINCT news_id) AS total FROM ratings";
if (!empty($search)) {
    $count_sql .= " INNER JOIN tbl_news ON ratings.news_id = tbl_news.id WHERE tbl_news.title LIKE '%$search%'";
}

$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$total_records = $count_row['total'];

$total_pages = ceil($total_records / $results_per_page);

$start_limit = ($page - 1) * $results_per_page;

$sql = "SELECT r.news_id, n.title AS News_Title, ROUND(AVG(r.rating), 1) AS Average_Rating, COUNT(DISTINCT r.user_id) AS Number_of_Users_Rated
        FROM ratings r
        INNER JOIN tbl_news n ON r.news_id = n.id";

if (!empty($search)) {
    $sql .= " WHERE n.title LIKE '%$search%'";
}

$sql .= " GROUP BY r.news_id
          ORDER BY $sort_column $sort_order
          LIMIT $start_limit, $results_per_page";

$result = $conn->query($sql);
?>

<?php include('includes/header.php') ?>

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Most Rated Games</h4>
            </div>
            <div class="card-body">

                <div class="col-md-4">
                    <form method="post" action="" id="searchForm">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Search by name" name="search" value="<?php echo htmlentities($search); ?>" onkeydown="if (event.keyCode == 13) document.getElementById('searchForm').submit()">
                        </div>
                    </form>
                </div>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><a href="?sort=News_Title&order=<?php echo ($sort_column == 'News_Title' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Game Title</a></th>
                            <th><a href="?sort=Average_Rating&order=<?php echo ($sort_column == 'Average_Rating' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Average Ratings</a></th>
                            <th><a href="?sort=Number_of_Users_Rated&order=<?php echo ($sort_column == 'Number_of_Users_Rated' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Number of Users Rated</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $row["news_id"]; ?></td>
                                    <td><?php echo $row["News_Title"]; ?></td>
                                    <td><?php echo $row["Average_Rating"]; ?></td>
                                    <td><?php echo $row["Number_of_Users_Rated"]; ?></td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="4">No ratings found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <nav aria-label="Pagination">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                            <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i . (empty($search) ? '' : '&search=' . urlencode($search)) . '&sort=' . $sort_column . '&order=' . $sort_order; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>

                <form action="generate_pdf_rated.php" method="post" target="_blank">
                    <input type="submit" name="pdf_creator" class="btn btn-success" value="Generate PDF">
                </form>           

                <form action="generate_rss_rated_games.php" method="post" target="_blank">
                    <input type="submit" name="xml_creator" class="btn btn-primary" value="Generate RSS">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php') ?>
