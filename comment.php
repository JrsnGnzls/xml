<?php
session_start();
require_once("config.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$id = "";
$title = "";
$description = "";

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $url = "https://www.gameinformer.com/reviews.xml";
    $feedArr = json_decode(json_encode(simplexml_load_file($url)), true);

    if (isset($feedArr['channel']) && isset($feedArr['channel']['item'])) {
        foreach ($feedArr['channel']['item'] as $item) {
            if ($item['guid'] == $id) {
                $title = $item['title'];
                $description = $item['description'];
                break;
            }
        }
    } else {
        echo "Error: News item not found.";
        exit();
    }
} else {
    echo "Error: News item ID not provided.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'])) {
    $username = $_SESSION['username'];
    $comment = mysqli_real_escape_string($conn, $_POST["comment"]);

    $user_id_query = "SELECT id FROM users WHERE username = '$username'";
    $user_id_result = mysqli_query($conn, $user_id_query);

    if ($user_id_result && mysqli_num_rows($user_id_result) > 0) {
        $user_row = mysqli_fetch_assoc($user_id_result);
        $user_id = $user_row['id'];

        $insert_comment_query = "INSERT INTO comments (user_id, item_id, comment, created_at) VALUES ('$user_id', '$id', '$comment', CURRENT_TIMESTAMP())";
        if (mysqli_query($conn, $insert_comment_query)) {
            header("Location: comment.php?id=$id");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Error: Unable to retrieve user ID.";
    }
}

$comments_query = "SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.item_id = '$id'";
$comments_result = mysqli_query($conn, $comments_query);

$admin_replies_query = "SELECT ar.*, u.username FROM admin_replies ar JOIN users u ON ar.user_id = u.id WHERE ar.item_id = '$id'";
$admin_replies_result = mysqli_query($conn, $admin_replies_query);
?>

<?php include('includes/header.php') ?>
<div class="mb-5">
</div>

<div class="container">
    <?php
    $url = "https://www.gameinformer.com/reviews.xml";
    $feedArr = json_decode(json_encode(simplexml_load_file($url)), true);

    $list = null;

    if (isset($feedArr['channel'])) {
        if (isset($feedArr['channel']['item'])) {
            echo "<div class='container'>";

            $list = $feedArr['channel']['item'][$id - 1];

            echo "<a href='" . $list['link'] . "'target='_blank' style='text-decoration: none; color: inherit;'><h2>" . $list['title'] . "</h2></a>";
            echo "<p>" . $list['description'] . "...</p>";


            echo "</div>";
        }
    }
    ?>
    <hr>
    <h3>Rate the Game</h3>
    <div class="row">
        <div class="rateyo" id="rating" data-rateyo-rating="0" data-rateyo-num-stars="5">
        </div>
        <form id="ratingForm" method="post">
            <input type="hidden" name="rating" id="ratingInput">
            <input type="hidden" name="news_id" value="<?php echo htmlspecialchars($id); ?>">
            <button id="ratingSubmit" class="btn btn-primary btn-sm my-3">Submit Rating</button>
        </form>

        <div class="alert alert-success d-none" id="ratingSuccessMessage" role="alert">
            Rating submitted successfully.
        </div>

        <form method="post" action="comment.php?id=<?php echo htmlspecialchars($id); ?>">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <div>
                <label for="comment">Comment:</label><br>
                <textarea id="comment" name="comment" class="form-control" rows="4" cols="50" required></textarea>
            </div>
            <br>
            <div class="mb-3">
                <input type="submit" class="btn btn-success w-25" value="Submit">
            </div>
        </form>
        <div class="comments-box">
            <h3>Comments</h3>
            <?php
            if ($comments_result && mysqli_num_rows($comments_result) > 0) {
                while ($comment_row = mysqli_fetch_assoc($comments_result)) {
                    $comment_date = date("F j, Y, g:i a", strtotime($comment_row['created_at']));
                    $comment_id = $comment_row['id'];

                    // Display main comment
                    echo "<div class='comment' id='comment-$comment_id'>";
                    echo "<p><strong> " . htmlspecialchars($comment_row['username']) . "</strong></p>";
                    echo "<p><small>$comment_date</small></p>";
                    echo "<p>" . htmlspecialchars($comment_row['comment']) . "</p>";

                    echo "<button class='btn btn-link reply-btn btn-sm' data-comment-id='$comment_id' data-username='" . htmlspecialchars($comment_row['username']) . "'>Reply</button>";

                    $admin_replies_query = "SELECT admin_reply, created_at FROM admin_replies WHERE comment_id = '$comment_id'";
                    $admin_replies_result = mysqli_query($conn, $admin_replies_query);
                    if ($admin_replies_result && mysqli_num_rows($admin_replies_result) > 0) {
                        echo "<div class='admin-replies'>";
                        while ($admin_reply_row = mysqli_fetch_assoc($admin_replies_result)) {
                            $admin_reply_date = date("F j, Y, g:i a", strtotime($admin_reply_row['created_at']));
                            echo "<div class='admin-reply'>";
                            echo "<p><strong>Admin</strong></p>";
                            echo "<p><small>$admin_reply_date</small></p>";
                            echo "<p>" . htmlspecialchars($admin_reply_row['admin_reply']) . "</p>";

                            echo "<button class='btn btn-link reply-btn btn-sm' data-comment-id='$comment_id' data-username='Admin'>Reply</button>";

                            echo "</div>";
                        }
                        echo "</div>";
                    }

                    $user_replies_query = "SELECT u.reply, u.created_at, us.username 
                                  FROM user_replies u 
                                  INNER JOIN users us ON u.user_id = us.id 
                                  WHERE u.comment_id = '$comment_id' 
                                  ORDER BY u.created_at ASC";
                    $user_replies_result = mysqli_query($conn, $user_replies_query);
                    if ($user_replies_result && mysqli_num_rows($user_replies_result) > 0) {
                        echo "<div class='user-replies'>";
                        while ($user_reply_row = mysqli_fetch_assoc($user_replies_result)) {
                            $user_reply_date = date("F j, Y, g:i a", strtotime($user_reply_row['created_at']));
                            echo "<div class='user-reply'>";
                            echo "<p><strong>" . htmlspecialchars($user_reply_row['username']) . "</strong></p>";
                            echo "<p><small>$user_reply_date</small></p>";
                            echo "<p>" . htmlspecialchars($user_reply_row['reply']) . "</p>";

                            echo "<button class='btn btn-link reply-btn btn-sm' data-comment-id='$comment_id' data-username='" . htmlspecialchars($user_reply_row['username']) . "'>Reply</button>";

                            echo "</div>";
                        }
                        echo "</div>";
                    }

                    echo "<div class='reply-form' style='display: none;'>";
                    echo "<form class='reply-form-inner' method='post' action='process_reply.php'>";
                    echo "<input type='hidden' name='comment_id' value='$comment_id'>";
                    echo "<div class='form-group'>";
                    echo "<textarea class='form-control' name='reply_content' rows='3' placeholder='Reply to " . htmlspecialchars($comment_row['username']) . "' required></textarea>";
                    echo "</div>";
                    echo "<button type='submit' class='btn btn-primary mt-3 btn-sm'>Submit Reply</button>";
                    echo "</form>";
                    echo "</div>";

                    echo "</div>";
                    echo "<hr>";
                }
            } else {
                echo "<p>No comments yet.</p>";
            }
            ?>
        </div>
    </div>

    <style>
        .container {
            width: 100%;
            margin: auto;
        }

        .item {
            float: left;
        }

        .comments-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
        }

        .comment {
            margin-bottom: 15px;
            padding-left: 20px;
            border-left: 3px solid #007bff;
        }

        .comment p {
            margin: 0;
        }

        .admin-replies {
            margin-left: 20px;
        }

        .admin-reply {
            margin-bottom: 15px;
            padding-left: 10px;
            border-left: 3px solid #dc3545;
        }

        .admin-reply p {
            margin: 0;
        }

        .user-reply {
            margin-bottom: 15px;
            padding-left: 10px;
            border-left: 3px solid #28a745;
        }

        .user-reply p {
            margin: 0;
        }

        .reply-btn {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            padding: 0;
            margin: 0;
            margin-top: 5px;
            margin-bottom: 10px;
            background: none;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .reply-btn:hover {
            text-decoration: none;
        }

        .reply-form {
            margin-top: 10px;
        }

        .reply-form textarea {
            width: 100%;
            margin-top: 5px;
        }
    </style>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const replyButtons = document.querySelectorAll(".reply-btn");

            replyButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const commentId = this.getAttribute("data-comment-id");
                    const username = this.getAttribute("data-username");
                    const replyForm = document.querySelector(`#comment-${commentId} .reply-form`);
                    const replyTextarea = replyForm.querySelector("textarea");

                    replyTextarea.value = `@${username} `;

                    if (replyForm.style.display === "none" || replyForm.style.display === "") {
                        replyForm.style.display = "block";
                    } else {
                        replyForm.style.display = "none";
                    }
                });
            });
        });
    </script>


    <script>
        $(function() {
            $(".rateyo").rateYo({
                rating: 0,
                fullStar: true,
                onSet: function(rating, rateYoInstance) {
                    $('#ratingInput').val(rating);
                }
            });

            $('#ratingSubmit').click(function(event) {
                event.preventDefault();

                var formData = $('#ratingForm').serialize();

                $.ajax({
                    type: "POST",
                    url: "submit_rating.php",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            alert("Rating submitted successfully");
                        } else {
                            alert("Failed to submit rating: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: " + error);
                    }
                });
            });
        });
    </script>


    <?php include('includes/footer.php') ?>