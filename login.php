<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard/index.php");
}
?>

<!doctype html>
<html lang="en">

<head>


    <?php
    $pageInfo = [
        "title" => "Login",
    ];
    ?>

    <?php include_once "includes/head.php"; ?>

</head>


<body>


    <?php include_once "includes/nav.php"; ?>


    <div class="row justify-content-center">
        <div class="col-lg-4">

            <form action="core/login.php" method="POST">
                <?php if (isset($_SESSION["login_error"])) : ?>
                    <div class="alert alert-danger">
                        <strong>Opps! </strong><?php echo $_SESSION["login_error"]  ?>
                    </div>
                <?php
                    unset($_SESSION["login_error"]);
                endif;
                ?>
                <div class="form-floating mb-2">
                    <input type="email" required name="email" class="form-control" id="floatingInput" value="<?php if (isset($_SESSION["login_email"])) {
                                                                                                                    echo $_SESSION["login_email"];
                                                                                                                    unset($_SESSION["login_email"]);
                                                                                                                } ?>" placeholder="name@example.com">
                    <label for="floatingInput">Email address</label>
                </div>
                <div class="form-floating mb-2">
                    <input type="password" required name="password" class="form-control" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword">Password</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit" name="login">Login</button>
            </form>

            <div class="d-flex">
                <a class="mt-5 mx-2" href="reset">
                    Forgot Password? Reset
                </a>
                <a class="mt-5 mx-2" href="register">
                    Don't have an account? Register
                </a>
            </div>

        </div>
    </div>


    <?php include_once "includes/footer.php"; ?>


    <?php include_once "includes/scripts.php"; ?>

</body>

</html>