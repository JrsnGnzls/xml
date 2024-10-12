<?php
require_once("../config.php");

$error = '';
$success_message = '';
$search = '';

$items_per_page = 10;

$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $items_per_page;

if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $sql = "SELECT * FROM users WHERE username LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM users";
}

$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sort_order = isset($_GET['order']) && strtoupper($_GET['order']) == 'DESC' ? 'DESC' : 'ASC';

$sql .= " ORDER BY $sort_column $sort_order LIMIT $start_from, $items_per_page";

$result = $conn->query($sql);

$total_count_sql = "SELECT COUNT(*) AS total_count FROM users";
if (!empty($search)) {
    $total_count_sql .= " WHERE username LIKE '%$search%'";
}
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
                <h4>
                    User Lists
                    <a href="users-create.php" class="btn btn-primary float-end">Add Users</a>
                </h4>
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
                            <input type="text" class="form-control" placeholder="Search by name" name="search" value="<?php echo htmlentities($search); ?>" onkeydown="if (event.keyCode == 13) document.getElementById('searchForm').submit()">
                        </div>
                    </form>
                </div>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><a href="?sort=id&order=<?php echo ($sort_column == 'id' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">ID</a></th>
                            <th><a href="?sort=username&order=<?php echo ($sort_column == 'username' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Name</a></th>
                            <th><a href="?sort=role&order=<?php echo ($sort_column == 'role' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Role</a></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $row["id"]; ?></td>
                                    <td><?php echo $row["username"]; ?></td>
                                    <td><?php echo ($row["role"] == 1) ? "Admin" : "User"; ?></td>
                                    <td>
                                        <a href="users-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                                        <a href="users-delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete?')" class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="4">No users found</td>
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

                <form action="pdf.php" method="post" target="_blank">
                    <input type="submit" name="pdf_creater" class="btn btn-success" value="Generate PDF">
                </form>

                <form action="generate_rss_users.php" method="post" target="_blank">
                    <input type="submit" name="xml_creator" class="btn btn-primary" value="Generate RSS">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php') ?>