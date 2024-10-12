<?php
session_start();
require_once("config.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$user_id_query = "SELECT id FROM users WHERE username = '$username'";
$user_id_result = mysqli_query($conn, $user_id_query);

if ($user_id_result && mysqli_num_rows($user_id_result) > 0) {
    $user_row = mysqli_fetch_assoc($user_id_result);
    $user_id = $user_row['id'];

    $comments_query = "SELECT c.*, n.title AS news_title 
                       FROM comments c 
                       JOIN tbl_news n ON c.item_id = n.id 
                       WHERE c.user_id = '$user_id'";
    $comments_result = mysqli_query($conn, $comments_query);
} else {
    echo "Error: Unable to retrieve user ID.";
}
?>

<?php include('includes/header.php'); ?>
<div class="mb-5"></div>

<div class="container">
    <h2 class="my-4 text-center">My Comments</h2>
    <div class="row justify-content-center">
        <?php
        if ($comments_result && mysqli_num_rows($comments_result) > 0) {
            while ($comment_row = mysqli_fetch_assoc($comments_result)) {
        ?>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <?php echo $comment_row['news_title']; ?>
                            <form action="delete_comment.php" method="POST">
                                <input type="hidden" name="comment_id" value="<?php echo $comment_row['id']; ?>">
                                <button type="submit" class="btn btn-danger float-end" onclick="return confirm('Are you sure you want to delete?')">Delete</button>
                            </form>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?php echo $comment_row['comment']; ?></p>

                            <?php
                            $all_replies_query = "SELECT r.*, u.username 
                                         FROM (
                                             SELECT comment_id, user_id, reply, created_at 
                                             FROM user_replies 
                                             UNION ALL 
                                             SELECT comment_id, NULL AS user_id, admin_reply AS reply, created_at 
                                             FROM admin_replies
                                         ) r 
                                         LEFT JOIN users u ON r.user_id = u.id 
                                         WHERE r.comment_id = '{$comment_row['id']}' 
                                         ORDER BY r.created_at ASC";
                            $all_replies_result = mysqli_query($conn, $all_replies_query);

                            if ($all_replies_result && mysqli_num_rows($all_replies_result) > 0) {
                                echo "<h6 class='mt-3 ml-4'>Replies:</h6>";
                                while ($reply_row = mysqli_fetch_assoc($all_replies_result)) {
                                    $reply_date = date("F j, Y, g:i a", strtotime($reply_row['created_at']));
                                    $reply_content = htmlspecialchars($reply_row['reply']);
                                    $username = isset($reply_row['username']) ? $reply_row['username'] : 'Admin';

                                    echo "<p class='ml-4'><strong>{$username} - {$reply_date}</strong><br>{$reply_content}</p>";
                                }
                            }
                            ?>

                            <a href="comment.php?id=<?php echo $comment_row['item_id']; ?>#comment-<?php echo $comment_row['id']; ?>" class="btn btn-success float-end w-100">View Comments</a>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<p class='col text-center'>No comments yet.</p>";
        }
        ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var scrollLinks = document.querySelectorAll('a[href^="#"]');

        scrollLinks.forEach(function(scrollLink) {
            scrollLink.addEventListener("click", function(e) {
                e.preventDefault();
                var targetId = this.getAttribute("href").substring(1);
                var targetElement = document.getElementById(targetId);
                if (targetElement) {
                    var offsetTop = targetElement.offsetTop;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: "smooth"
                    });
                }
            });
        });
    });
</script>
<?php include('includes/footer.php'); ?>