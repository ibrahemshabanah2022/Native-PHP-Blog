<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Include necessary files
require '../config/Database.php';

// Instantiate database connection
$db = (new Database())->getConnection();

// Retrieve the user ID from the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $postId = $_POST['post_id'];
    $content = $_POST['content'];
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Prepare and execute SQL statement to insert new comment
    $sql = "INSERT INTO comments (post_id, user_id, content, created_at) VALUES (:post_id, :user_id, :content, NOW())";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->execute();

    // Redirect back to the post page after submitting the comment
    // header("Location: post.php?id=$postId");
    exit();
}
