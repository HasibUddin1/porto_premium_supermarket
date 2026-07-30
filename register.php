<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!doctype html>
<html lang="en">

<head>


    <?php
    $pageInfo = [
        "title" => "Register",
    ];
    ?>

    <?php include_once "includes/head.php"; ?>

</head>

<?php
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard/index");
}
?>

<body>

    <?php include_once "includes/nav.php"; ?>


    <div class="row justify-content-center">
        <div class="col-lg-4">

            <form action="core/register.php" method="POST">
                <?php if (isset($_SESSION["register_error"])) : ?>
                    <div class="alert alert-danger">
                        <strong>Opps! </strong><?php echo $_SESSION["register_error"]  ?>
                    </div>
                <?php
                    unset($_SESSION["register_error"]);
                endif;
                ?>

                <div class="form-floating mb-2">
                    <input type="email" required class="form-control" name="email" id="floatingInput" placeholder="name@example.com" value="<?php if (isset($_SESSION["register_email"])) {
                                                                                                                                                echo $_SESSION["register_email"];
                                                                                                                                                unset($_SESSION["register_email"]);
                                                                                                                                            } ?>">
                    <label for="floatingInput">Email address</label>
                </div>
                <div class="form-floating mb-2">
                    <input type="password" required class="form-control" name="password" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword">Password</label>
                </div>

                <button class="w-100 btn btn-lg btn-primary" name="register" type="submit">Register</button>

            </form>

            <br>
            <a class="mt-5 mb-3" href="login">
                Already have an account? Login
            </a>

        </div>
    </div>


    <?php include_once "includes/footer.php"; ?>


    <?php include_once "includes/scripts.php"; ?>

</body>

</html>