<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$resetErrors  = $_SESSION['reset_errors'] ?? [];
$resetSuccess = $_SESSION['reset_success'] ?? '';

unset($_SESSION['reset_errors'], $_SESSION['reset_success']);
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

    <div class="main_page">
        <?php include_once "includes/nav.php"; ?>

        <!-- Reset Password Page Content*********************** -->
        <div class="account_page">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 login_form" style="float: none; margin: 0 auto;">
                        <div class="theme-title">
                            <h2>Reset Password</h2>
                        </div>

                        <?php if (!empty($resetErrors)): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach ($resetErrors as $err): ?>
                                        <li><?php echo e($err); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ($resetSuccess): ?>
                            <div class="alert alert-success"><?php echo e($resetSuccess); ?></div>
                        <?php endif; ?>

                        <form action="core/reset.php" method="post">
                            <div class="form_group">
                                <label>Email</label>
                                <div class="input_group">
                                    <input type="email" name="email" placeholder="youremail@gmail.com" required>
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                </div> <!-- End of .input_group -->
                            </div> <!-- End of .form_group -->

                            <div class="form_group">
                                <label>New Password</label>
                                <div class="input_group">
                                    <input type="password" name="password" placeholder="********" required>
                                    <i class="fa fa-lock" aria-hidden="true"></i>
                                </div> <!-- End of .input_group -->
                            </div> <!-- End of .form_group -->

                            <div class="form_group">
                                <label>Confirm New Password</label>
                                <div class="input_group">
                                    <input type="password" name="confirm_password" placeholder="********" required>
                                    <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                                </div> <!-- End of .input_group -->
                            </div> <!-- End of .form_group -->

                            <div class="clear_fix">
                                <a href="login" class="float_right">Back to Login</a>
                            </div>
                            <button class="color1_bg tran3s" type="submit">Reset Password</button>
                        </form>
                    </div> <!-- End of .login_form -->
                </div> <!-- End of .row -->
            </div> <!-- End of .container -->
        </div>


        <?php include_once "includes/footer.php"; ?>


        <?php include_once "includes/scripts.php"; ?>
    </div>

</body>

</html>