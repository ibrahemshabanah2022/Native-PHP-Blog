<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require '../config/Database.php';
// var_dump($_POST);
try {
    // Instantiate database connection
    $db = new Database();
    $conn = $db->getConnection();

    // Set PDO to throw exceptions on error
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve the user ID from the session
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if (!$user_id) {
        echo "You must be logged in to submit a reply.";
        exit;
    }

    // Capture form data
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0;
    $content = isset($_POST['content']) ? $_POST['content'] : '';

    if ($post_id <= 0 || $parent_id < 0 || empty($content)) {
        echo "Invalid data provided.";
        exit;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content, parent_id) VALUES (?, ?, ?, ?)");

    // Bind parameters
    $stmt->bindParam(1, $post_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $user_id, PDO::PARAM_INT);
    $stmt->bindParam(3, $content, PDO::PARAM_STR);
    $stmt->bindParam(4, $parent_id, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        header("Location: http://localhost/Native%20PHP%20Blog/pages/post_detail.php?id=" . $post_id);
    } else {
        echo "Error executing query.";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
