<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Include necessary files
require '../config/Database.php';
require '../classes/QueryBuilder.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $slug = $_POST['slug'];
    $content = $_POST['content'];
    $status = $_POST['status'];

    // Handle file upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/"; // Ensure this directory exists and is writable
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $target_file; // Store the full path in the database
        }
    }

    $pdo = (new Database())->getConnection();
    $queryBuilder = new QueryBuilder($pdo);
    // Prepare and execute the insert query
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, slug, content, status, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $slug, $content, $status, $image]);

    header("Location: blogs.php"); // Redirect to a page showing all posts
    exit();
}
?>

<!DOCTYPE html>
<html>

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

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>
    <!-- Include the navbar -->
    <?php include '../components/navbar.php'; ?>
    <div class="container">
        <h1 class="mt-5">Add New Post</h1>
        <form method="post" action="add_post.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="slug">Slug:</label>
                <input type="text" class="form-control" id="slug" name="slug" required>
            </div>
            <div class="form-group">
                <label for="content">Content:</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" class="form-control" id="image" name="image">
            </div>
            <button type="submit" class="btn btn-primary">Add Post</button>
        </form>
        <a href="profile.php" class="btn btn-secondary mt-3">Back to Profile</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>