<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require '../config/Database.php';

function deleteComment($commentId)
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
    // $isAdmin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;

    if ($comment->user_id != $userId) {
        return "You do not have permission to delete this comment.";
    }

    // Delete the comment
    $sql = "DELETE FROM comments WHERE id = :comment_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        return "Comment deleted successfully.";
    } else {
        return "Error deleting comment.";
    }
}

// Handle the delete request
if (isset($_POST['comment_id'])) {
    $commentId = (int)$_POST['comment_id'];
    $result = deleteComment($commentId);
    echo $result;
}
