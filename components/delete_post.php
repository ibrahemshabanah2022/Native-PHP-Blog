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
if (isset($_POST['post_id']) && is_numeric($_POST['post_id'])) {
    $post_id = $_POST['post_id'];

    // Instantiate database connection
    $db = (new Database())->getConnection();

    // Delete the post
    try {
        $stmt = $db->prepare('DELETE FROM posts WHERE id = :post_id AND user_id = :user_id');
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect back to the page where the post was deleted from
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } catch (PDOException $e) {
        echo 'Query failed: ' . $e->getMessage();
        exit();
    }
} else {
    // Redirect back if post_id is not provided or invalid
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
