<?php
session_start();
// Include necessary files
require '../config/Database.php';
require '../classes/QueryBuilder.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate input data
    if (!empty($email) && !empty($password)) {
        $pdo = (new Database())->getConnection();

        $queryBuilder = new QueryBuilder($pdo);
        $user = $queryBuilder->table('users')->where('email', '=', $email)->get();

        if ($user && password_verify($password, $user[0]->password)) {
            // Successful login
            $_SESSION['user_id'] = $user[0]->id;
            $_SESSION['loggedin'] = $user[0]->username;
            header("Location: blogs.php"); // Redirect to a dashboard or home page
            exit;
        } else {
            $errorMessage = "Invalid email or password.";
        }
    } else {
        $errorMessage = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
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
<!-- Include the navbar -->
<?php include '../components/navbar.php'; ?>

<body>
    <div class="container">
        <h2 class="mt-5">Login</h2>
        <?php if (isset($errorMessage)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST" class="mt-3">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>