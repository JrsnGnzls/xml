<?php
session_start();
require_once("config.php");

$search_results = [];

if(isset($_GET['search'])) {
    $search_query = $_GET['search'];

    $sql = "SELECT * FROM tbl_news WHERE title LIKE '%$search_query%' OR description LIKE '%$search_query%'";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $search_results = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

include('includes/header.php');
?>

<div class="mb-5"></div>

<div class="container">
    <?php
    if (!empty($search_results)) {
        foreach ($search_results as $result) {
            $id = $result['id'];
            $news_title = $result['title'];
            $news_description = $result['description'];

            $average_rating_query = "SELECT AVG(rating) AS average_rating FROM ratings WHERE news_id = '$id'";
            $average_rating_result = mysqli_query($conn, $average_rating_query);
            $average_rating_row = mysqli_fetch_assoc($average_rating_result);
            $average_rating = $average_rating_row['average_rating'];
            $average_rating_display = $average_rating ? number_format($average_rating, 1) : 'Not rated yet';

            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

            if ($user_id !== null) {
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
                    echo "<button class='btn btn-success add-to-favorite' disabled><i class='fas fa-heart'></i> Added to Favorites</button>";
                } else {
                    echo "<button class='btn btn-primary add-to-favorite' data-news-id='$id'><i class='far fa-heart'></i> Add to Favorites</button>";
                }
            } else {
                echo "<a href='login.php' class='btn btn-primary'><i class='far fa-heart'></i> Add to Favorites</a>";
            }
            
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No results found for '" . $search_query . "'</p>";
    }
    ?>
</div>

<?php include('includes/footer.php'); ?>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>

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
    $('.rateyo').each(function () {
        var rating = parseFloat($(this).data('rating')) || 0;
        $(this).rateYo({
            rating: rating,
            readOnly: true,
            starWidth: "25px"
        });
    });

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
