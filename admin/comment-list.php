<?php
session_start();
require_once("../config.php");

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success_message = '';

$items_per_page = 10;

$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $items_per_page;

if (isset($_POST['delete'])) {
    $comment_id = $_POST["comment_id"];
    $delete_query = "DELETE FROM comments WHERE id = '$comment_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error: " . $delete_query . "<br>" . mysqli_error($conn);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["admin_reply"])) {
    $comment_id = $_POST["comment_id"];
    $item_id = $_POST["item_id"];
    $admin_reply = $_POST["admin_reply"];
    
    $username = $_SESSION['username'];
    $user_id_query = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($user_id_query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['id'];
        
        $sql = "INSERT INTO admin_replies (user_id, comment_id, item_id, admin_reply, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiis", $user_id, $comment_id, $item_id, $admin_reply);
        
        if ($stmt->execute()) {
            $success_message = "Admin reply added successfully.";
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "Error: Username not found.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    $search = $_POST["search"];
    $sql = "SELECT comments.id, comments.item_id, tbl_news.title AS game_title, users.username AS username, comments.comment, comments.created_at 
            FROM comments 
            INNER JOIN users ON comments.user_id = users.id
            INNER JOIN tbl_news ON comments.item_id = tbl_news.id
            WHERE tbl_news.title LIKE '%$search%'
            OR users.username LIKE '%$search%'
            OR comments.comment LIKE '%$search%'
            ORDER BY comments.id DESC
            LIMIT $start_from, $items_per_page";
} else {
    $sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'id';
    $sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
    
    $sql = "SELECT comments.id, comments.item_id, tbl_news.title AS game_title, users.username AS username, comments.comment, comments.created_at 
            FROM comments 
            INNER JOIN users ON comments.user_id = users.id
            INNER JOIN tbl_news ON comments.item_id = tbl_news.id
            ORDER BY $sort_by $sort_order
            LIMIT $start_from, $items_per_page";
}

$result = $conn->query($sql);

$total_count_sql = "SELECT COUNT(*) AS total_count FROM comments";
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
                <h4>Comment Lists</h4>
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
                            <input type="text" class="form-control" placeholder="Search by comment" name="search" value="<?php echo isset($search) ? htmlentities($search) : ''; ?>" onkeydown="if (event.keyCode == 13) document.getElementById('searchForm').submit()">
                        </div>
                    </form>
                </div>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><a href="?sort=id&order=<?php echo ($sort_by == 'id' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">ID</a></th>
                            <th><a href="?sort=game_title&order=<?php echo ($sort_by == 'game_title' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Game Title</a></th>
                            <th><a href="?sort=username&order=<?php echo ($sort_by == 'username' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Name</a></th>
                            <th>Comment</th>
                            <th><a href="?sort=created_at&order=<?php echo ($sort_by == 'created_at' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Date</a></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $row["id"]; ?></td>
                                    <td><?php echo $row["game_title"]; ?></td>
                                    <td><?php echo $row["username"]; ?></td>
                                    <td><?php echo $row["comment"]; ?></td>
                                    <td><?php echo $row["created_at"]; ?></td>
                                    <td>
                                        <form method="post" action="">
                                            <input type="hidden" name="comment_id" value="<?php echo $row["id"]; ?>">
                                            <input type="hidden" name="item_id" value="<?php echo $row["item_id"]; ?>">
                                            <textarea name="admin_reply" class="form-control mb-2" placeholder="Enter admin reply"></textarea>
                                            <button type="submit" name="reply" class="btn btn-success btn-sm">Reply</button>
                                            <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete?')" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="6">No Comments found</td>
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

                <form action="generate_pdf.php" method="post" target="_blank">
                    <input type="submit" name="pdf_creater" class="btn btn-success" value="Generate PDF">
                </form>

                <form action="generate_rss_comments.php" method="post" target="_blank">
                    <input type="submit" name="xml_creator" class="btn btn-primary" value="Generate RSS">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php') ?>
