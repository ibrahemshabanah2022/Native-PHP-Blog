<?php
// Include necessary files
require '../config/Database.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Instantiate database connection
$db = (new Database())->getConnection();

// Pagination settings
$postsPerPage = 5;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $postsPerPage;

// Select posts with pagination
try {
    $stmt = $db->prepare('SELECT * FROM posts WHERE user_id = :user_id LIMIT :limit OFFSET :offset');
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $postsPerPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Get the total number of posts
    $stmt = $db->prepare('SELECT COUNT(*) as count FROM posts WHERE user_id = :user_id');
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $totalPosts = $stmt->fetch(PDO::FETCH_OBJ)->count;
    $totalPages = ceil($totalPosts / $postsPerPage);
} catch (PDOException $e) {
    echo 'Query failed: ' . $e->getMessage();
    exit();
}
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
                        <h1 class="mb-3 text-white">My Posts</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="site-section">
        <div class="container">
            <div class="row">



                <?php foreach ($posts as $post) : ?>
                    <!-- Inside the foreach loop where you display posts -->
                    <div class="card m-4" style="width: 18rem;">
                        <!-- Post image -->
                        <?php if ($post->image) : ?>
                            <img style="width: 100%; height: 200px; object-fit: cover;" class="card-img-top" src="<?php echo $post->image; ?>" alt="Post image" style="max-width: 100%; height: auto;">
                        <?php else : ?>
                            <img class="card-img-top" src="default.jpg" alt="Default image">
                        <?php endif; ?>

                        <!-- Card body -->
                        <div class="card-body">
                            <a href="post_detail.php?id=<?php echo $post->id; ?>">
                                <h4 style="color: black;"><?php echo htmlspecialchars($post->title); ?></h4>
                            </a>

                            <p class="p-3 mb-2  text-warning"><?php echo $post->created_at; ?></p>
                            <p class="card-text"><?php echo substr($post->content, 0, 150); ?>...</p>
                        </div>

                        <!-- Card footer -->
                        <div class="card-footer">
                            <div class="d-flex justify-content-start">
                                <form method="POST" action="../components/delete_post.php" class="mr-2">
                                    <input type="hidden" name="post_id" value="<?php echo $post->id; ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                                <a href="../components/edit_post.php?id=<?php echo $post->id; ?>" class="btn btn-success">Edit</a>
                            </div>
                        </div>
                    </div>



                <?php endforeach; ?>
            </div>

            <div class="col-12 mt-5 text-center">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($currentPage > 1) : ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Previous</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($page = 1; $page <= $totalPages; $page++) : ?>
                            <li class="page-item <?php echo $page == $currentPage ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages) : ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </li>
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