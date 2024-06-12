<?php
require '../config/Database.php';

// Create a new instance of the Database class
$database = new Database();
$db = $database->getConnection();

// Pagination parameters
$limit = 5; // Number of posts per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number
$start = ($page - 1) * $limit;

// Query to get total number of posts
$totalQuery = $db->query("SELECT COUNT(*) as total FROM posts");
$totalResult = $totalQuery->fetch(PDO::FETCH_ASSOC);
$totalPosts = $totalResult['total'];
$totalPages = ceil($totalPosts / $limit);

// Query to fetch posts for the current page
$stmt = $db->prepare("SELECT * FROM posts LIMIT :start, :limit");
$stmt->bindParam(':start', $start, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Trips &mdash; Website Template by Colorlib</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Work+Sans:400,700,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../fonts/icomoon/style.css">

    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="../css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../fonts/flaticon/font/flaticon.css">
    <link rel="stylesheet" href="../css/aos.css">

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <div class="ftco-blocks-cover-1">
        <div class="site-section-cover overlay" style="background-image: url('../images/hero_1.jpg')">
            <div class="container">
                <div class="row align-items-center justify-content-center text-center">
                    <div class="col-md-5" data-aos="fade-up">
                        <h1 class="mb-3 text-white">All Posts</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="site-section">
        <div class="container">
            <div class="row">
                <?php foreach ($posts as $post) : ?>
                    <div class="card m-4" style="width: 18rem;">
                        <!-- Post image -->
                        <?php if ($post['image']) : ?>
                            <img style="width: 100%; height: 200px; object-fit: cover;" class="card-img-top" src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post image">
                        <?php else : ?>
                            <img style="width: 100%; height: 200px; object-fit: cover;" class="card-img-top" src="default.jpg" alt="Default image">
                        <?php endif; ?>

                        <!-- Card body -->
                        <div class="card-body">
                            <a href="post_detail.php?id=<?php echo $post['id']; ?>">
                                <h4 style="color: black;"><?php echo htmlspecialchars($post['title']); ?></h4>
                            </a>
                            <p class="p-3 mb-2  text-warning">Created at :<?php echo $post['created_at']; ?></p>
                            <p class="card-text"><?php echo substr($post['content'], 0, 150); ?>...</p>
                        </div>


                    </div>

                <?php endforeach; ?>
            </div>

            <div class="col-12 mt-5 text-center">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?php echo ($page - 1); ?>">Previous</a></li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?php echo ($page + 1); ?>">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>

        </div>
    </div> <!-- END .site-section -->
    <?php include '../components/footer.php'; ?>

    <script src="../js/jquery-3.3.1.min.js"></script>
    <script src="../js/jquery-migrate-3.0.0.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/owl.carousel.min.js"></script>
    <script src="../js/jquery.sticky.js"></script>
    <script src="../js/jquery.waypoints.min.js"></script>
    <script src="../js/jquery.animateNumber.min.js"></script>
    <script src="../js/jquery.fancybox.min.js"></script>
    <script src="../js/jquery.stellar.min.js"></script>
    <script src="../js/jquery.easing.1.3.js"></script>
    <script src="../js/bootstrap-datepicker.min.js"></script>
    <script src="../js/isotope.pkgd.min.js"></script>
    <script src="../js/aos.js"></script>
    <script src="../js/main.js"></script>
</body>

</html>