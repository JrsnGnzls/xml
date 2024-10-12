<?php
session_start();
require_once("config.php");

include 'storedproceduredcall/select.php';

$url = "https://www.gameinformer.com/reviews.xml";
$feedArr = json_decode(json_encode(simplexml_load_file($url)), true);

?>

<?php include('includes/header.php') ?>
<div class="mb-5"></div>

<?php
if (isset($feedArr['channel']) && isset($feedArr['channel']['item'])) {
    echo "<div class='container'>";
    foreach ($feedArr['channel']['item'] as $list) {
        $tt = str_replace("'", "\'", $list['title']);
        $ss = str_replace("'", "\'", $list['guid']);
        $dd = str_replace("'", "\'", $list['description']);
        $iii = $list['link'];
        $pub = $list['pubDate'];

        $sql = "SELECT id, title, description FROM tbl_news WHERE title = '$tt'";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $id = $row['id'];
            $news_title = $row['title'];
            $news_description = $row['description'];

            $average_rating_query = "SELECT AVG(rating) AS average_rating FROM ratings WHERE news_id = '$id'";
            $average_rating_result = mysqli_query($conn, $average_rating_query);
            $average_rating_row = mysqli_fetch_assoc($average_rating_result);
            $average_rating = $average_rating_row['average_rating'];
            $average_rating_display = $average_rating ? number_format($average_rating, 1) : 'Not rated yet';

            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

            if ($user_id) {
                $is_favorite_query = "SELECT 1 FROM favorites WHERE user_id = '$user_id' AND news_id = '$id'";
                $is_favorite_result = mysqli_query($conn, $is_favorite_query);
                $is_favorite = mysqli_num_rows($is_favorite_result) > 0;
            } else {
                $is_favorite = false;
            }
            

            echo "<div class='item'>";
            echo "<div class='card mb-4'>";
            echo "<h3 class='card-header' style='background-color: #ffb82e;'>" . $news_title . "</h3>";
            echo "<div class='card-body'>";
            echo "<p>" . $news_description . "..." . "</p>";
            
            echo "<div id='averageRating_$id' class='rateyo' data-rating='$average_rating'></div>";
            echo "<p><strong>Ratings:</strong> <span id='averageRatingDisplay_$id'>" . $average_rating_display . "</span></p>";

            echo "<div class='button-group'>";
            echo "<a href='comment.php?id={$id}' class='btn btn-success leave-comment-btn' style='margin-right: 10px;'>View Full Article</a>";

            if ($user_id) {
                if ($is_favorite) {
                    echo "<button class='btn btn-success add-to-favorite disabled' disabled data-news-id='" . $id . "'><i class='fas fa-heart'></i> Added to Favorites</button>";
                } else {
                    echo "<button class='btn btn-primary add-to-favorite' data-news-id='" . $id . "'><i class='far fa-heart'></i> Add to Favorites</button>";
                }
            } else {
                echo "<a href='login.php' class='btn btn-primary'><i class='far fa-heart'></i> Add to Favorites</a>";
            }
            
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";

        } else {
            echo "<div class='item'>";
            echo "<div class='card mb-4'>";
            echo "<h3 class='card-header' style='background-color: #ffb82e;'>News Not Found</h3>";
            echo "<div class='card-body'>";
            echo "<p>The requested news item does not exist.</p>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
        $sp_insert_data = executeselect("sp_insert_data", "'$tt','$dd','$iii','$pub'");
    }
    echo "</div>";
} else {
    echo "Invalid Feed Link";
}
?>

<?php include('includes/footer.php') ?>



<script>
$(document).ready(function () {
    $('.rateyo').each(function () {
        var rating = parseFloat($(this).data('rating')) || 0;
        $(this).rateYo({
            rating: rating,
            readOnly: true,
            starWidth: "25px"
        });
    });
});
</script>

<script>
$(document).ready(function () {
    $(document).on('click', '.add-to-favorite', function () {
        var button = $(this);
        var newsId = button.data('news-id');

        var userId = <?php echo json_encode($_SESSION['user_id'] ?? null); ?>;
        if (!userId) {
            window.location.href = 'login.php';
            return;
        }

        button.prop('disabled', true).html('<i class="fas fa-heart"></i> Adding to Favorites...');

        setTimeout(function () {
            button.removeClass('btn-primary').addClass('btn-success disabled').prop('disabled', true).html('<i class="fas fa-heart"></i> Added to Favorites');
        }, 1000);
    });
});
</script>
