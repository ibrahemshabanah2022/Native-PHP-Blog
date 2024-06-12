<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require '../config/Database.php';

function updateComment($commentId, $content)
{
    $db = (new Database())->getConnection();

    // Check if the comment exists and retrieve the user_id
    $sql = "SELECT user_id FROM comments WHERE id = :comment_id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    $stmt->execute();
    $comment = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$comment) {
        return "Comment not found.";
    }

    // Check if the current user is the author of the comment or an admin
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $isAdmin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;

    if ($comment->user_id != $userId && !$isAdmin) {
        return "You do not have permission to update this comment.";
    }

    // Update the comment content
    $sql = "UPDATE comments SET content = :content WHERE id = :comment_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        return "Comment updated successfully.";
    } else {
        return "Error updating comment.";
    }
}

// Handle the update request
if (isset($_POST['comment_id']) && isset($_POST['content'])) {
    $commentId = (int)$_POST['comment_id'];
    $content = $_POST['content'];
    echo updateComment($commentId, $content);
}
