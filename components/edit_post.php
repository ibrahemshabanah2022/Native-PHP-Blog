<?php
// Include necessary files
require '../config/Database.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if post_id is provided and valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $post_id = $_GET['id'];

    // Instantiate database connection
    $db = (new Database())->getConnection();

    // Fetch post details
    try {
        $stmt = $db->prepare('SELECT * FROM posts WHERE id = :post_id AND user_id = :user_id');
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $post = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$post) {
            // Post not found or user does not have permission to edit
            header('Location: index.php');
            exit();
        }
    } catch (PDOException $e) {
        echo 'Query failed: ' . $e->getMessage();
        exit();
    }

    // HTML form for editing post
?>

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
        <!-- Your HTML form for editing the post -->
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form action="update_post.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="post_id" value="<?php echo $post->id; ?>">

                        <!-- Title field with default value -->
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post->title); ?>">
                        </div>

                        <!-- Content field with default value -->
                        <div class="form-group">
                            <label for="content">Content:</label>
                            <textarea class="form-control" id="content" name="content"><?php echo htmlspecialchars($post->content); ?></textarea>
                        </div>

                        <!-- Post image -->
                        <div class="form-group">
                            <label for="image">Current Image:</label>
                            <?php if ($post->image) : ?>
                                <img src="<?php echo htmlspecialchars($post->image); ?>" alt="Post Image" class="img-fluid">
                            <?php else : ?>
                                <p>No image available</p>
                            <?php endif; ?>
                            <label for="new_image">Upload New Image:</label>
                            <input type="file" class="form-control-file" id="new_image" name="new_image">
                        </div>

                        <button type="submit" class="btn btn-primary">Update Post</button>
                    </form>
                </div>
            </div>
        </div>
        <?php include '../components/footer.php'; ?>

    <?php
} else {
    // Redirect back if post_id is not provided or invalid
    exit();
}
    ?>