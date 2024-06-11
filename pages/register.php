<?php
// Include necessary files
require '../config/Database.php';
require '../classes/QueryBuilder.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Validate input data (you can add more validation as needed)
    if (!empty($username) && !empty($email) && !empty($password)) {
        $pdo = (new Database())->getConnection();

        $queryBuilder = new QueryBuilder($pdo);
        $queryBuilder->table('users')->insert([
            'username' => $username,
            'email' => $email,
            'password' => $password
        ]);
        header("Location: login.php");
    } else {
        echo "All fields are required.";
    }
} else {
    // Display the registration form
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
        <section class="vh-100 gradient-custom">
            <div class="container py-5 h-100">
                <div class="row justify-content-center align-items-center h-100">
                    <div class="col-12 col-lg-9 col-xl-7">
                        <div class="card shadow-2-strong card-registration" style="border-radius: 15px;">
                            <div class="card-body p-4 p-md-5">
                                <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Registration Form</h3>
                                <form action="register.php" method="POST">
                                    <label for="username">Username:</label>
                                    <input class="form-control form-control-lg" type="text" name="username" id="username" required>
                                    <br>
                                    <label for="email">Email:</label>
                                    <input class="form-control form-control-lg" type="email" name="email" id="email" required>
                                    <br>
                                    <label for="password">Password:</label>
                                    <input class="form-control form-control-lg" type="password" name="password" id="password" required>
                                    <br>
                                    <button class="btn btn-primary btn-lg" type="submit" name="submit">Register</button>

                                    <!-- <div class="row">
                                        <div class="col-md-6 mb-4">

                                            <div data-mdb-input-init class="form-outline">
                                                <input name="username" type="text" id="firstName" class="form-control form-control-lg" />
                                                <label class="form-label" for="firstName">User Name</label>
                                            </div>

                                        </div>
                                        <div class="col-md-6 mb-4">

                                            <div data-mdb-input-init class="form-outline">
                                                <input name="password" type="text" id="lastName" class="form-control form-control-lg" />
                                                <label class="form-label" for="lastName">Password</label>
                                            </div>

                                        </div>
                                    </div>



                                    <div class="row">
                                        <div class="col-md-6 mb-4 pb-2">

                                            <div data-mdb-input-init class="form-outline">
                                                <input name="email" type="email" id="emailAddress" class="form-control form-control-lg" />
                                                <label class="form-label" for="emailAddress">Email</label>
                                            </div>

                                        </div>

                                    </div>



                                    <div class="mt-4 pt-2">
                                        <input data-mdb-ripple-init class="btn btn-primary btn-lg" type="submit" value="Submit" />
                                    </div> -->

                                </form>
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
                                    <a href="#"><img src="images/insta_1.jpg" alt="Image" class="img-fluid"></a>
                                </div>
                                <div class="col-4 gal_col">
                                    <a href="#"><img src="images/insta_2.jpg" alt="Image" class="img-fluid"></a>
                                </div>
                                <div class="col-4 gal_col">
                                    <a href="#"><img src="images/insta_3.jpg" alt="Image" class="img-fluid"></a>
                                </div>
                                <div class="col-4 gal_col">
                                    <a href="#"><img src="images/insta_4.jpg" alt="Image" class="img-fluid"></a>
                                </div>
                                <div class="col-4 gal_col">
                                    <a href="#"><img src="images/insta_5.jpg" alt="Image" class="img-fluid"></a>
                                </div>
                                <div class="col-4 gal_col">
                                    <a href="#"><img src="images/insta_6.jpg" alt="Image" class="img-fluid"></a>
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
            <script src="js/jquery-3.3.1.min.js"></script>
            <script src="js/jquery-migrate-3.0.0.js"></script>
            <script src="js/popper.min.js"></script>
            <script src="js/bootstrap.min.js"></script>
            <script src="js/owl.carousel.min.js"></script>
            <script src="js/jquery.sticky.js"></script>
            <script src="js/jquery.waypoints.min.js"></script>
            <script src="js/jquery.animateNumber.min.js"></script>
            <script src="js/jquery.fancybox.min.js"></script>
            <script src="js/jquery.stellar.min.js"></script>
            <script src="js/jquery.easing.1.3.js"></script>
            <script src="js/bootstrap-datepicker.min.js"></script>
            <script src="js/isotope.pkgd.min.js"></script>
            <script src="js/aos.js"></script>

            <script src="js/main.js"></script>
        </section>
    </body>

    </html>

<?php
}
?>