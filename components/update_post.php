<?php
// Include necessary files
require '../config/Database.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if post_id and other form fields are provided
if (isset($_POST['post_id']) && isset($_POST['title']) && isset($_POST['content'])) {
    $post_id = $_POST['post_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Check if a new image is uploaded
    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK) {
        // Process the uploaded image
        $fileTmpPath = $_FILES['new_image']['tmp_name'];
        $fileName = $_FILES['new_image']['name'];
        $fileSize = $_FILES['new_image']['size'];
        $fileType = $_FILES['new_image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Allow certain file formats
        $allowedExtensions = array('jpg', 'jpeg', 'png');

        if (in_array($fileExtension, $allowedExtensions)) {
            // Generate a unique file name
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // Upload the file to the server
            $uploadFileDir = '../uploads/';
            $destPath = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // File upload successful, update the database
                $imagePath = '../uploads/' . $newFileName;

                // Instantiate database connection
                $db = (new Database())->getConnection();

                // Update the post with the new image path
                try {
                    $stmt = $db->prepare('UPDATE posts SET title = :title, content = :content, image = :image WHERE id = :post_id AND user_id = :user_id');
                    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
                    $stmt->bindParam(':image', $imagePath, PDO::PARAM_STR);
                    $stmt->execute();

                    // Redirect back to the page where the post was edited from
                    header('Location: http://localhost/Native%20PHP%20Blog/components/edit_post.php?id=' . $post_id);
                    exit();
                } catch (PDOException $e) {
                    echo 'Query failed: ' . $e->getMessage();
                    exit();
                }
            } else {
                echo "File upload failed!";
                exit();
            }
        } else {
            echo "Invalid file format! Please upload a JPG or PNG image.";
            exit();
        }
    } else {
        // No new image uploaded, update the post without changing the image
        // Instantiate database connection
        $db = (new Database())->getConnection();

        // Update the post
        try {
            $stmt = $db->prepare('UPDATE posts SET title = :title, content = :content WHERE id = :post_id AND user_id = :user_id');
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->execute();

            // Redirect back to the page where the post was edited from
            header('Location: http://localhost/Native%20PHP%20Blog/components/edit_post.php?id=' . $post_id);
            exit();
        } catch (PDOException $e) {
            echo 'Query failed: ' . $e->getMessage();
            exit();
        }
    }
} else {
    // Redirect back if form fields are not provided
    exit();
}
