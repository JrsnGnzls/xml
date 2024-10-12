<script src="assets/css/jquery-3.7.1.min.js"></script>
<script src="assets/css/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
<script>
$(document).ready(function() {
    $('.add-to-favorite').click(function() {
    var newsId = $(this).data('news-id');
    console.log("News ID:", newsId);
    
    $.post('add-to-favorites.php', { newsId: newsId }, function(response) {
       
        if(response === 'success') {
            alert('Item added to favorites successfully!');
            
        } else {
            alert('Failed to add item to favorites.');
        }
    });
});
});
</script>
</body>
</html>