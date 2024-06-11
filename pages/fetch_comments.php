<?php
// Include necessary files
require '../config/Database.php';

// Instantiate database connection
$db = (new Database())->getConnection();

// Function to fetch the next 5 comments and their replies for a post
// Function to fetch the next 5 comments and their replies for a post
function fetchNextCommentsWithReplies($postId, $limit, $offset)
{
    global $db;

    // Retrieve the next 5 comments and their replies for the specified page
    $sql = "
        SELECT c.*, u.username,
            (SELECT GROUP_CONCAT(cr.id, '|', cr.content, '|', cr.user_id, '|', u2.username, '|', cr.created_at ORDER BY cr.created_at ASC SEPARATOR ';') 
            FROM comments cr 
            JOIN users u2 ON cr.user_id = u2.id 
            WHERE cr.parent_id = c.id) AS replies
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.post_id = :post_id AND c.parent_id IS NULL
        ORDER BY c.created_at ASC
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_OBJ);



    // Process replies
    foreach ($comments as $comment) {
        $comment->replies = [];
        if (!empty($comment->replies)) {
            $replies = explode(';', $comment->replies);
            foreach ($replies as $reply) {
                $replyData = explode('|', $reply);
                $replyObj = (object) [
                    'id' => $replyData[0],
                    'content' => $replyData[1],
                    'user_id' => $replyData[2],
                    'username' => $replyData[3],
                    'created_at' => $replyData[4],
                    'replies' => fetchReplies($replyData[0]) // Fetch nested replies recursively
                ];
                $comment->replies[] = $replyObj;
            }
        }
    }

    return $comments;
}

// Function to fetch nested replies recursively
function fetchReplies($commentId)
{
    global $db;

    $sql = "
        SELECT cr.id, cr.content, cr.user_id, u.username, cr.created_at
        FROM comments cr
        JOIN users u ON cr.user_id = u.id
        WHERE cr.parent_id = :comment_id
        ORDER BY cr.created_at ASC
    ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    $stmt->execute();
    $replies = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Process nested replies recursively
    foreach ($replies as $reply) {
        $reply->replies = fetchReplies($reply->id);
    }

    return $replies;
}


// Retrieve the post ID and page number from the GET parameters
if (isset($_GET['id']) && isset($_GET['page'])) {
    $postId = (int)$_GET['id'];
    $page = (int)$_GET['page'];
    $commentsPerPage = 5;
    $offset = ($page - 1) * $commentsPerPage;

    // Fetch the next 5 comments and their replies for the specified page
    $nextComments = fetchNextCommentsWithReplies($postId, $commentsPerPage, $offset);

    // Render the next 5 comments and their replies
    renderCommentsWithReplies($nextComments);
}

// Function to render comments and their replies in hierarchy
function renderCommentsWithReplies($comments)
{
    echo '<ul>';
    foreach ($comments as $comment) {
        echo '<li>';
        echo '<div class="card m-2 " style="border: none;">';
        echo '<div class="card-body">';
        echo '<h4>' . htmlspecialchars($comment->username) . '</h4>';
        echo '<p>' . htmlspecialchars($comment->content) . '</p>';
        echo '<div class="text-info">' . date('F j, Y, g:i a', strtotime($comment->created_at)) . '</div>';
        echo '<div class="dropdown">';
        echo '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Reply</button>';
        echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
        echo '<form action="../components/submit_reply.php" method="post">';
        echo '<input type="hidden" name="post_id" value="' . htmlspecialchars($comment->post_id) . '">';
        echo '<input type="hidden" name="parent_id" value="' . htmlspecialchars($comment->id) . '">';
        echo '<textarea name="content" required placeholder="Write your reply..."></textarea>';
        echo '<button type="submit">Submit Reply</button>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
        if (!empty($comment->replies)) {
            renderCommentsWithReplies($comment->replies);
        }
        echo '</div>';
        echo '</div>';
        echo '</li>';
    }
    echo '</ul>';
}
