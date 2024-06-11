<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: blogs.php");
    exit();
}

// Get the search query
if (isset($_GET['query'])) {
    require_once '../config/Database.php';

    $search_query = $_GET['query'];
    // var_dump($search_query);
    // Instantiate database connection
    $db = (new Database())->getConnection();

    // Prepare and execute the search query
    $stmt = $db->prepare("SELECT * FROM posts WHERE title LIKE :search_query OR content LIKE :search_query");
    $stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
    $stmt->execute();
    $search_results = $stmt->fetchAll(PDO::FETCH_OBJ);
}

?>



<nav class="navbar navbar-expand-lg  navbar-dark bg-primary">
    <a class="navbar-brand" href="#"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) : ?>

                <li class="nav-item active">
                    <a href="../pages/blogs.php" class="nav-link">Blog</a>
                </li>


                <li class="nav-item active"><a class="nav-link" href="?logout=true">Logout</a></li>
                <li class="nav-item active"><a class="nav-link" href="../pages/profile.php">Profile Setting</a></li>
                <li class="nav-item active"><a class="nav-link" href="../pages/add_post.php">Add Post</a></li>
                <li class="nav-item active"><a class="nav-link" href="../pages/myPosts.php">My Posts</a></li>


            <?php else : ?>
                <li class="nav-item active"><a href="../pages/blogs.php" class="nav-link">Blog</a></li>
                <li class="nav-item active"><a href="../pages/login.php" class="nav-link">Login</a></li>
                <li class="nav-item active"><a href="../pages/register.php" class="nav-link">Register</a></li>
            <?php endif; ?>
        </ul>
        <form class="form-inline my-2 my-lg-0" action="../pages/search_results.php" method="GET">
            <input class="form-control mr-sm-2" type="text" name="query" placeholder="Search" aria-label="Search">
            <button class="btn outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>