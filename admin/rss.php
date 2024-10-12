<?php
require_once("../config.php");

$items_per_page = 10;

$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $items_per_page;

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

$sql .= " ORDER BY $sort_column $sort_order LIMIT $start_from, $items_per_page";

$result = $conn->query($sql);

$count_sql = "SELECT COUNT(*) AS total FROM tbl_news";

if (!empty($search)) {
    $count_sql .= " WHERE title LIKE '%$search%'";
}

$count_result = $conn->query($count_sql);
$row = $count_result->fetch_assoc();
$total_items = $row['total'];

$total_pages = ceil($total_items / $items_per_page);
?>

<?php include('includes/header.php') ?>

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>RSS</h4>
            </div>
            <div class="card-body">

                <?php if (!empty($error)) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php } ?>
                <?php if (!empty($success_message)) { ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success_message; ?>
                    </div>
                <?php } ?>

                <div class="col-md-4">
                    <form method="post" action="" id="searchForm">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Search by title" name="search" value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>" onkeydown="if (event.keyCode == 13) document.getElementById('searchForm').submit()">
                        </div>
                    </form>
                </div>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><a href="?sort=id&order=<?php echo ($sort_column == 'id' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">ID</a></th>
                            <th><a href="?sort=title&order=<?php echo ($sort_column == 'title' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Title</a></th>
                            <th><a href="?sort=image&order=<?php echo ($sort_column == 'image' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Description</a></th>
                            <th><a href="?sort=publish&order=<?php echo ($sort_column == 'publish' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Publish Date</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $row["id"]; ?></td>
                                    <td><?php echo $row["title"]; ?></td>
                                    <td><?php echo $row["image"]; ?></td>
                                    <td><?php echo $row["publish"]; ?></td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="4">No RSS found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <nav aria-label="Pagination">
                    <ul class="pagination justify-content-center">
                        <?php for ($page = 1; $page <= $total_pages; $page++) { ?>
                            <li class="page-item <?php echo $page === $current_page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page . (isset($search) ? '&search=' . urlencode($search) : '') . '&sort=' . $sort_column . '&order=' . $sort_order; ?>"><?php echo $page; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>

                <form action="generate_rss.php" method="post" target="_blank">
                    <input type="submit" name="pdf_creater" class="btn btn-success" value="Generate PDF">
                </form>

                <form action="generate_rss_rss.php" method="post" target="_blank">
                    <input type="submit" name="xml_creator" class="btn btn-primary" value="Generate RSS">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php') ?>
