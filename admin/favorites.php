<?php
require_once("../config.php");

$items_per_page = 10;

$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $items_per_page;

$error = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    $search = $_POST["search"];
    $sql = "SELECT f.id, u.username AS user_name, n.title AS news_title, f.created_at FROM favorites f 
            INNER JOIN users u ON f.user_id = u.id 
            INNER JOIN tbl_news n ON f.news_id = n.id 
            WHERE u.username LIKE '%$search%' OR n.title LIKE '%$search%'
            ORDER BY f.id DESC
            LIMIT $start_from, $items_per_page";
} else {
    $sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'id';
    $sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
    
    $sql = "SELECT f.id, u.username AS user_name, n.title AS news_title, f.created_at FROM favorites f 
            INNER JOIN users u ON f.user_id = u.id 
            INNER JOIN tbl_news n ON f.news_id = n.id
            ORDER BY $sort_by $sort_order
            LIMIT $start_from, $items_per_page";
}

$result = $conn->query($sql);

$total_count_sql = "SELECT COUNT(*) AS total_count FROM favorites";
$total_count_result = $conn->query($total_count_sql);
$total_count_row = $total_count_result->fetch_assoc();
$total_rows = $total_count_row['total_count'];
$total_pages = ceil($total_rows / $items_per_page);
?>

<?php include('includes/header.php') ?>

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Favorite Lists</h4>
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
                            <input type="text" class="form-control" placeholder="Search by name" name="search" value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>" onkeydown="if (event.keyCode == 13) document.getElementById('searchForm').submit()">
                        </div>
                    </form>
                </div>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><a href="?sort=id&order=<?php echo ($sort_by == 'id' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">ID</a></th>
                            <th><a href="?sort=user_name&order=<?php echo ($sort_by == 'user_name' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">User Name</a></th>
                            <th><a href="?sort=news_title&order=<?php echo ($sort_by == 'news_title' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Game Title</a></th>
                            <th><a href="?sort=created_at&order=<?php echo ($sort_by == 'created_at' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Created At</a></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $row["id"]; ?></td>
                                    <td><?php echo $row["user_name"]; ?></td>
                                    <td><?php echo $row["news_title"]; ?></td>
                                    <td><?php echo $row["created_at"]; ?></td>
                                    <td>
                                        <a href="favorites_delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete?')" class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="5">No favorites found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <nav aria-label="Pagination">
                    <ul class="pagination justify-content-center">
                        <?php for ($page = 1; $page <= $total_pages; $page++) { ?>
                            <li class="page-item <?php echo $page === $current_page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page . (isset($search) ? '&search=' . urlencode($search) : '') . '&sort=' . $sort_by . '&order=' . $sort_order; ?>"><?php echo $page; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>

                <form action="generate_favorites.php" method="post" target="_blank">
                    <input type="submit" name="pdf_creater" class="btn btn-success" value="Generate PDF">
                </form>

                <form action="generate_rss_favorites.php" method="post" target="_blank">
                    <input type="submit" name="xml_creator" class="btn btn-primary" value="Generate RSS">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php') ?>
