<?php
// Retrieve search query from URL
require '../config/Database.php';

if (isset($_GET['query'])) {
    $db = (new Database())->getConnection();

    $search_query = $_GET['query'];

    // Perform search operation (assuming $db is your database connection)
    $stmt = $db->prepare("SELECT * FROM posts WHERE title LIKE :search_query OR content LIKE :search_query");
    $stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
    $stmt->execute();
    $search_results = $stmt->fetchAll(PDO::FETCH_OBJ);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Trips &mdash; Website Template by Colorlib</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Work+Sans:400,700,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="../css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/owl.theme.default.min.css">
    <link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">
    <link rel="stylesheet" href="../css/aos.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include '../components/navbar.php'; ?>


    <div class="site-section">
        <div class="container">
            <h1>Search Results</h1>

            <div class="row">
                <?php if (isset($search_results) && count($search_results) > 0) : ?>

                    <?php foreach ($search_results as $post) : ?>
                        <div class="card m-4" style="width: 18rem;">
                            <?php if ($post->image) : ?>
                                <img class="card-img-top" src="<?php echo htmlspecialchars($post->image); ?>" alt="Post image">
                            <?php else : ?>
                                <img class="card-img-top" src="default.jpg" alt="Default image">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($post->title); ?></h5>
                                <p class="card-text"><?php echo $post->created_at; ?></p>
                                <p class="card-text"><?php echo substr($post->content, 0, 150); ?>...</p>
                                <a href="post_detail.php?id=<?php echo $post->id; ?>" class="btn btn-primary">Read More</a>
                            </div>
                        </div>
                    <?php endforeach; ?>

                <?php else : ?>
                    <p>No results found.</p>
                <?php endif; ?>
            </div>


        </div>
    </div> <!-- END .site-section -->

    <?php include '../components/footer.php'; ?>

</body>

</html>