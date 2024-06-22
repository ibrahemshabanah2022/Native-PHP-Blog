<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require '../config/Database.php';

// Instantiate database connection
$db = (new Database())->getConnection();

// Retrieve the user ID from the session
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Retrieve the post ID from the URL parameter
if (isset($_GET['id'])) {
    $postId = (int)$_GET['id'];

    // Retrieve post details along with the username
    $sql = "
        SELECT posts.*, users.username
        FROM posts
        JOIN users ON posts.user_id = users.id
        WHERE posts.id = :post_id
        LIMIT 1
    ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_OBJ);

    // Retrieve the total count of comments for the current post
    $sql = "
        SELECT COUNT(*) as count
        FROM comments
        WHERE post_id = :post_id
    ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $totalComments = $stmt->fetch(PDO::FETCH_OBJ)->count;

    // Retrieve all comments and replies for the current post
    $sql = "
        SELECT comments.*, users.username
        FROM comments
        JOIN users ON comments.user_id = users.id
        WHERE comments.post_id = :post_id
        ORDER BY comments.created_at ASC
    ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Organize comments and replies in a hierarchical structure
    $commentsById = [];
    foreach ($comments as $comment) {
        $commentsById[$comment->id] = $comment;
        $comment->replies = [];
    }
    foreach ($comments as $comment) {
        if ($comment->parent_id !== null && isset($commentsById[$comment->parent_id])) {
            $commentsById[$comment->parent_id]->replies[] = $comment;
        }
    }

    // Function to render comments and replies recursively
    function renderComments($comments)
    {
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        echo '<ul>';
        foreach ($comments as $comment) {
            echo '<li>';
            echo '<div class="card m-2" style="border: none;">';
            echo '<div class="card-body">';
            echo '<h4>' . htmlspecialchars($comment->username) . '</h4>';
            echo '<p>' . htmlspecialchars($comment->content) . '</p>';
            echo '<div class="text-info">' . date('F j, Y, g:i a', strtotime($comment->created_at)) . '</div>';
            echo '<div class="btn-group" role="group">';

            // Delete Button
            if ($comment->user_id == $userId) {
                echo '<form class="delete-comment-form" action="../components/delete_comment.php" method="post">';
                echo '<input type="hidden" name="comment_id" value="' . htmlspecialchars($comment->id) . '">';
                echo '<button type="submit" class="btn btn-danger">Delete</button>';
                echo '</form>';
            }
            // Reply Button
            echo '<button class="btn btn-success  dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Reply</button>';
            echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
            echo '<form action="../components/submit_reply.php" method="post">';
            echo '<input type="hidden" name="post_id" value="' . htmlspecialchars($comment->post_id) . '">';
            echo '<input type="hidden" name="parent_id" value="' . htmlspecialchars($comment->id) . '">';
            echo '<textarea name="content" required placeholder="Write your reply..."></textarea>';
            echo '<button class="btn btn-primary" type="submit">Submit Reply</button>';
            echo '</form>';
            echo '</div>';
            // Dropdown button to reveal update form
            echo '<div class="dropdown">';
            echo '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton-' . $comment->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Update</button>';
            echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton-' . $comment->id . '">';

            // Update form
            echo '<form class="update-comment-form" action="../components/update_comment.php" method="post">';
            echo '<input type="hidden" name="comment_id" value="' . htmlspecialchars($comment->id) . '">';
            echo '<textarea name="content" required placeholder="Write your updated comment...">' . htmlspecialchars($comment->content) . '</textarea>';
            echo '<button type="submit" class="btn btn-primary">Save</button>';
            echo '</form>';

            echo '</div>';
            echo '</div>';
            echo '</div>'; // End of btn-group
            if (!empty($comment->replies)) {
                renderComments($comment->replies);
            }
            echo '</div>'; // End of card-body
            echo '</div>'; // End of card
            echo '</li>';
        }
        echo '</ul>';
    }
}
?>






<!-- Your HTML code for displaying the post details -->

<!-- Loop through and display comments for the current page -->


<!-- Your HTML code for the comment form -->




<!doctype html>
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

<body data-spy="scroll" data-target=".site-navbar-target" data-offset="300">


    <div class="site-wrap" id="home-section">

        <div class="site-mobile-menu site-navbar-target">
            <div class="site-mobile-menu-header">
                <div class="site-mobile-menu-close mt-3">
                    <span class="icon-close2 js-menu-toggle"></span>
                </div>
            </div>
            <div class="site-mobile-menu-body"></div>
        </div>



        <!-- Include the navbar -->
        <?php include '../components/navbar.php'; ?>

        <div class="ftco-blocks-cover-1">
            <div class="site-section-cover overlay" data-stellar-background-ratio="0.5" style="background-image: url('../images/hero_1.jpg')">
                <div class="container">
                    <div class="row align-items-center justify-content-center text-center">
                        <div class="col-md-7">
                            <h1 class="mb-4" data-aos="fade-up" data-aos-delay="100"></h1>

                            <h1 class="mb-4" data-aos="fade-up" data-aos-delay="100"><?php echo htmlspecialchars($post->title); ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="site-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 blog-content">

                        <div class="card ">
                            <div class="card-body">
                                <h4>Posted By <b><?php echo htmlspecialchars($post->username); ?></b></h4>
                                <hr>
                                <?php if ($post->image) : ?>
                                    <img src="<?php echo htmlspecialchars($post->image); ?>" class="img-fluid mb-3" alt="Post image">
                                <?php endif; ?>
                                <!-- Post content -->
                                <p><?php echo $post->content; ?></p>
                                <!-- Post metadata -->
                                <hr>
                                <h4>Published on: <?php echo date('F j, Y, g:i a', strtotime($post->created_at)); ?>
                                </h4>
                            </div>
                        </div>

                        <br>



                        <!-- Large modal -->
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg">see comments</button>

                        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div id="comments-container">
                                        <?php
                                        if (isset($comments)) {
                                            renderComments(array_filter($comments, function ($comment) {
                                                return $comment->parent_id === null;
                                            }));
                                        } else {
                                            echo "No comments available.";
                                        }
                                        ?>
                                    </div>
                                    <!-- "Show More" button -->
                                    <?php if (isset($totalComments) && count($comments) < $totalComments) : ?>
                                        <button class="btn btn-primary" id="show-more-btn" data-offset="5" data-post-id="<?php echo $postId; ?>">Show More Comments</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>











                        <!-- Modal -->



                        <div class="pt-5">


                            <!-- END comment-list -->






                            <div class="comment-form-wrap pt-5">
                                <!-- Comment form -->
                                <?php if ($userId !== null) : ?>
                                    <div class="form-group">
                                        <form action="../components/submit_comment.php" method="post">
                                            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($postId); ?>">
                                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">
                                            <textarea class="form-control" name="content" required placeholder="Write your comment..."></textarea>
                                            <button class="btn btn-primary btn-md text-white" type="submit">Submit Comment</button>
                                        </form>



                                    </div>
                                <?php else : ?>
                                    <h3><a href="login.php">click here to leave a comment.</a href="login.php"></h3>
                                <?php endif; ?>

                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>


        <footer class="site-footer bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3">
                        <h2 class="footer-heading mb-3">Instagram</h2>
                        <div class="row">
                            <div class="col-4 gal_col">
                                <a href="#"><img src="../images/insta_1.jpg" alt="Image" class="img-fluid"></a>
                            </div>
                            <div class="col-4 gal_col">
                                <a href="#"><img src="../images/insta_2.jpg" alt="Image" class="img-fluid"></a>
                            </div>
                            <div class="col-4 gal_col">
                                <a href="#"><img src="../images/insta_3.jpg" alt="Image" class="img-fluid"></a>
                            </div>
                            <div class="col-4 gal_col">
                                <a href="#"><img src="../images/insta_4.jpg" alt="Image" class="img-fluid"></a>
                            </div>
                            <div class="col-4 gal_col">
                                <a href="#"><img src="../images/insta_5.jpg" alt="Image" class="img-fluid"></a>
                            </div>
                            <div class="col-4 gal_col">
                                <a href="#"><img src="../images/insta_6.jpg" alt="Image" class="img-fluid"></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 ml-auto">
                        <div class="row">
                            <div class="col-lg-6 ml-auto">
                                <h2 class="footer-heading mb-4">Quick Links</h2>
                                <ul class="list-unstyled">
                                    <li><a href="#">About Us</a></li>
                                    <li><a href="#">Testimonials</a></li>
                                    <li><a href="#">Terms of Service</a></li>
                                    <li><a href="#">Privacy</a></li>
                                    <li><a href="#">Contact Us</a></li>
                                </ul>
                            </div>
                            <div class="col-lg-6">
                                <h2 class="footer-heading mb-4">Newsletter</h2>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nesciunt odio iure animi ullam quam, deleniti rem!</p>
                                <form action="#" class="d-flex" class="subscribe">
                                    <input type="text" class="form-control mr-3" placeholder="Email">
                                    <input type="submit" value="Send" class="btn btn-primary">
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row pt-5 mt-5 text-center">
                    <div class="col-md-12">
                        <div class="border-top pt-5">
                            <p>
                                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                                Copyright &copy;<script>
                                    document.write(new Date().getFullYear());
                                </script> All rights reserved | This template is made with <i class="icon-heart text-danger" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
                                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </footer>

    </div>

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


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle "Show More" button click
            $('#show-more-btn').click(function() {
                var postId = $(this).data('post-id');
                var nextPage = $(this).data('page');

                // Make AJAX request to fetch next page of comments
                $.ajax({
                    url: 'fetch_comments.php', // Replace with the URL of the server-side script to fetch comments
                    type: 'GET',
                    data: {
                        id: postId,
                        page: nextPage
                    },
                    success: function(response) {
                        // Append fetched comments to the comments container
                        $('#comments-container').append(response);

                        // Update data attributes of the "Show More" button for the next page
                        var nextPageNumber = nextPage + 1;
                        $('#show-more-btn').data('page', nextPageNumber);

                        // Hide the "Show More" button if no more pages are available
                        if (response.trim() === '') {
                            $('#show-more-btn').hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error(error);
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.update-comment-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const commentId = this.getAttribute('data-comment-id');
                    const updateForm = document.querySelector('.update-comment-form[data-comment-id="' + commentId + '"]');

                    if (updateForm) {
                        // Toggle visibility of the update form
                        updateForm.classList.toggle('d-none');
                    }
                });
            });
        });
    </script>

</body>

</html>