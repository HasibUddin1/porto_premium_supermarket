<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard/index.php");
    exit;
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$loginErrors     = $_SESSION['login_errors'] ?? [];
$registerErrors  = $_SESSION['register_errors'] ?? [];
$registerOld     = $_SESSION['register_old'] ?? [];
$registerSuccess = $_SESSION['register_success'] ?? '';

unset(
    $_SESSION['login_errors'],
    $_SESSION['register_errors'],
    $_SESSION['register_old'],
    $_SESSION['register_success']
);
?>

<!doctype html>
<html lang="en">

<head>


    <?php
    $pageInfo = [
        "title" => "Porto Premium Supermarket - Login",
    ];
    ?>

    <?php include_once "includes/head.php"; ?>

</head>


<body>


    <div class="main_page">
        <?php include_once "includes/nav.php"; ?>


        <!-- Login Page Breadcrumb -->
        <section class="breadcrumb-area" style="background-image:url(images/background/2.jpg);">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="breadcrumbs text-center">
                            <h1>Login / Register</h1>
                            <h4>Welcome to certified online organic products suppliersr</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="breadcrumb-bottom-area">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-md-5 col-sm-5">
                            <ul>
                                <li><a href="#">Home</a></li>
                                <li><a href="#"><i class="fa fa-angle-right"></i></a></li>
                                <li><a href="#">Gallery</a></li>
                                <li><a href="#"><i class="fa fa-angle-right"></i></a></li>
                                <li>Login / Register</li>
                            </ul>
                        </div>
                        <div class="col-lg-4 col-md-7 col-sm-7">
                            <p>We provide <span>100% organic</span> products</p>
                        </div>
                    </div>
                </div>
            </div>

        </section>


        <!-- Login Page Content*********************** -->
        <div class="account_page">
            <div class="container">

                <?php if (!empty($loginErrors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($loginErrors as $err): ?>
                                <li><?php echo e($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($registerSuccess): ?>
                    <div class="alert alert-success"><?php echo e($registerSuccess); ?></div>
                <?php endif; ?>

                <?php if (!empty($registerErrors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($registerErrors as $err): ?>
                                <li><?php echo e($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 login_form">
                        <div class="theme-title">
                            <h2>Login Now</h2>
                        </div>
                        <form action="core/login.php" method="post">
                            <div class="form_group">
                                <label>Email</label>
                                <div class="input_group">
                                    <input type="email" name="email" placeholder="youremail@gmail.com">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                </div> <!-- End of .input_group -->
                            </div> <!-- End of .form_group -->

                            <div class="form_group">
                                <label>Password</label>
                                <div class="input_group">
                                    <input type="password" name="password" placeholder="********">
                                    <i class="fa fa-lock" aria-hidden="true"></i>
                                </div> <!-- End of .input_group -->
                            </div> <!-- End of .form_group -->

                            <div class="clear_fix">
                                <div class="single_checkbox float_left">
                                    <input type="checkbox" id="remember" name="remember">
                                    <label for="remember">Remember me</label>
                                </div> <!-- End .single_checkbox -->
                                <a href="reset" class="float_right">Forgot Password?</a>
                            </div>
                            <button class="color1_bg tran3s" type="submit">Login now</button>
                        </form>
                    </div> <!-- End of .login_form -->

                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12 register_form">
                        <div class="theme-title">
                            <h2>Register Here</h2>
                        </div>
                        <form action="core/register.php" method="post">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form_group">
                                        <label>Name</label>
                                        <div class="input_group">
                                            <input type="text" name="name" value="<?php echo e($registerOld['name'] ?? ''); ?>">
                                            <i class="fa fa-user" aria-hidden="true"></i>
                                        </div> <!-- End of .input_group -->
                                    </div> <!-- End of .form_group -->

                                    <div class="form_group">
                                        <label>Password</label>
                                        <div class="input_group">
                                            <input type="password" name="password">
                                            <i class="fa fa-lock" aria-hidden="true"></i>
                                        </div> <!-- End of .input_group -->
                                    </div> <!-- End of .form_group -->

                                    <div class="form_group">
                                        <label>Phone Number</label>
                                        <div class="input_group">
                                            <input type="text" name="phone" value="<?php echo e($registerOld['phone'] ?? ''); ?>">
                                            <i class="fa fa-phone" aria-hidden="true"></i>
                                        </div> <!-- End of .input_group -->
                                    </div> <!-- End of .form_group -->
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form_group">
                                        <label>Email Address</label>
                                        <div class="input_group">
                                            <input type="email" name="email" value="<?php echo e($registerOld['email'] ?? ''); ?>">
                                            <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                        </div> <!-- End of .input_group -->
                                    </div> <!-- End of .form_group -->

                                    <div class="form_group">
                                        <label>Confirm Password</label>
                                        <div class="input_group">
                                            <input type="password" name="confirm_password">
                                            <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                                        </div> <!-- End of .input_group -->
                                    </div> <!-- End of .form_group -->

                                    <div class="form_group">
                                        <label>Location</label>
                                        <div class="input_group">
                                            <input type="text" name="location" value="<?php echo e($registerOld['location'] ?? ''); ?>">
                                            <i class="fa fa-location-arrow" aria-hidden="true"></i>
                                        </div> <!-- End of .input_group -->
                                    </div> <!-- End of .form_group -->
                                </div>
                            </div> <!-- End of .row -->

                            <div class="clear_fix">
                                <div class="single_checkbox float_left">
                                    <input type="checkbox" id="terms" name="terms">
                                    <label for="terms">I agree the term’s & conditions</label>
                                </div> <!-- End .single_checkbox -->
                            </div>
                            <button class="color1_bg tran3s" type="submit">Create Account</button>
                        </form>
                    </div> <!-- End of .register_form -->
                </div> <!-- End of .row -->
            </div> <!-- End of .container -->
        </div>


        <?php include_once "includes/footer.php"; ?>


        <?php include_once "includes/scripts.php"; ?>
    </div>

</body>

</html>