<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../config/Database.php'; // Include your database connection file
require '../classes/QueryBuilder.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form submission
    $pdo = (new Database())->getConnection();

    $username = $_POST['username'];
    $email = $_POST['email'];


    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->execute([$username, $email, $user_id]);

    // Handle password change
    if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_new_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_new_password = $_POST['confirm_new_password'];

        // Fetch the user's current password hash
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_new_password) {
                // Hash the new password and update it in the database
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_new_password, $user_id]);
            } else {
                echo "New passwords do not match.";
                exit();
            }
        } else {
            echo "Current password is incorrect.";
            exit();
        }
    }

    header("Location: profile.php");
    exit();
} else {
    // Fetch user data to display in form   
    $pdo = (new Database())->getConnection();

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}
?>






<!doctype html>
<html lang="en">

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
        <div class="row">
            <div class="col-md-12">
                <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="content" class="content content-full-width">
                                <!-- begin profile -->

                                <!-- end profile -->
                                <!-- begin profile-content -->
                                <div class="profile-content">
                                    <!-- begin tab-content -->
                                    <div class="tab-content p-0">

                                        <!-- begin #profile-about tab -->
                                        <div class="tab-pane fade in active show" id="profile-about">
                                            <!-- begin table -->
                                            <div class="table-responsive">

                                                <form method="post" action="profile.php">
                                                    <div class="form-group">
                                                        <label for="username">Username:</label>
                                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="email">Email:</label>
                                                        <input readonly type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="current_password">Current Password:</label>
                                                        <input type="password" class="form-control" id="current_password" name="current_password">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="new_password">New Password:</label>
                                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="confirm_new_password">Confirm New Password:</label>
                                                        <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password">
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                                </form>
                                            </div>
                                            <!-- end table -->
                                        </div>
                                        <!-- end #profile-about tab -->
                                    </div>
                                    <!-- end tab-content -->
                                </div>
                                <!-- end profile-content -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Include the footer -->
    <?php include '../components/footer.php'; ?>
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
</body>