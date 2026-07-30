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
        "title" => "Reset",
    ];
    ?>

    <?php include_once "includes/head.php"; ?>

</head>

<?php
if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
}
?>

<body>

    <?php include_once "includes/nav.php"; ?>


    <div class="row justify-content-center">
        <div class="col-lg-4">

            <form action="core/reset.php" method="POST">

                <h1 class="h3 mb-3 fw-normal">Please Enter your Email</h1>

                <?php if (isset($_SESSION["reset_error"])) : ?>
                    <div class="alert alert-danger">
                        <strong>Opps! </strong><?php echo $_SESSION["reset_error"]  ?>
                    </div>
                <?php
                    unset($_SESSION["reset_error"]);
                endif;

                if (isset($_SESSION["reset_success"])) : ?>

                    <div class="alert alert-success">
                        <strong>Success! </strong><?php echo $_SESSION["reset_success"]  ?>
                    </div>

                <?php
                    unset($_SESSION["reset_success"]);
                endif;
                ?>

                <div class="form-floating">
                    <input type="email" required name="email" class="form-control" id="floatingInput" value="<?php if (isset($_SESSION["reset_email"])) {
                                                                                                                    echo $_SESSION["reset_email"];
                                                                                                                    unset($_SESSION["reset_email"]);
                                                                                                                } ?>" placeholder="name@example.com">
                    <label for="floatingInput">Email address</label>
                </div>

                <br>

                <button class="w-100 btn btn-lg btn-primary" type="submit" name="reset">Reset</button>

            </form>

            <br>

            <a class="mt-5 mb-3" href="login">
                Back to Login
            </a>
            <br>

            <br>
            <a class="mt-5 mb-3" href="register">
                Don't have an account? Register
            </a>

        </div>
    </div>


    <?php include_once "includes/footer.php"; ?>


    <?php include_once "includes/scripts.php"; ?>

</body>

</html>