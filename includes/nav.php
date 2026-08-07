<?php
$curPageName = basename($_SERVER["SCRIPT_NAME"]);
?>


<!-- Header *******************************  -->
<header>
    <div class="top_header">
        <div class="container">
            <div class="pull-left header_left">
                <ul>
                    <li><a href="tel:+351920526147">Order On Phone: <span>+351920526147</span></a></li>
                    <li><i class="fa fa-envelope-o s_color" aria-hidden="true"></i><a href="mailto:sabedoria.porto@gmail.com">sabedoria.porto@gmail.com</a></li>
                </ul>
            </div>

            <div class="pull-right header_right">
                <div class="state" id="value1">
                    <ul>
                        <li><i class="fa fa-user s_color" aria-hidden="true"></i><a href="#">Account</a></li>
                        <li><i class="fa fa-shopping-basket s_color" aria-hidden="true"></i><a href="shop">Our Store</a></li>
                    </ul>

                </div>


            </div>
        </div> <!-- End of .container -->
    </div> <!-- End of .top_header -->

    <!-- TODO: Need to make search field work -->

    <div class="bottom_header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="search-box">
                        <form action="#" class="clearfix">
                            <input type="text" placeholder="Search...">
                            <button><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="col-md-4 col-sm-5 col-xs-6 logo-responsive">
                    <div class="logo-area">
                        <a href="index" class="pull-left logo"><img width="300px" src="images/logo/porto_logo_light2.webp" alt="LOGO"></a>
                    </div>
                </div>
                <div class="col-md-4 col-sm-7 col-xs-6 pdt-14">
                    <div class="login_option float_left">
                        <div class="login_form">
                            <div class="user">
                                <i class="icon-photo"></i>
                            </div>
                            <div class="login-info">
                                <div class="welcome">Welcome!</div>
                                <!-- select menu -->
                                <form action="#" class="select-form">
                                    <div class="g-input f1 mb-30">
                                        <?php
                                        // Place this in includes/nav.php in place of the existing static <select> block.
                                        // Session is likely already started elsewhere in nav.php (e.g. via cart_helper.php),
                                        // but this guards it in case this block is included on its own.
                                        if (session_status() === PHP_SESSION_NONE) {
                                            session_start();
                                        }

                                        $isLoggedIn = isset($_SESSION['user_id']);
                                        ?>

                                        <select
                                            class="text-capitalize selectpicker"
                                            data-style="g-select"
                                            data-width="100%"
                                            onchange="if(this.value) window.location.href=this.value;">
                                            <?php if ($isLoggedIn): ?>
                                                <option value="">My Account</option>
                                                <option value="dashboard/index.php">Dashboard</option>
                                                <option value="core/logout.php">Logout</option>
                                            <?php else: ?>
                                                <option value="">Sign In</option>
                                                <option value="login">Sign In</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <?php
                    require_once __DIR__ . '/../core/db_connection.php'; // exposes $conn
                    require_once __DIR__ . '/../core/cart_helper.php';

                    $cart = get_cart_summary($conn);
                    ?>

                    <div class="cart_option float_left">
                        <button class="cart tran3s dropdown-toggle" id="cartDropdown">
                            <i class="fa icon-icon-32846" aria-hidden="true"></i>
                            <span class="s_color_bg p_color cart_count"><?php echo (int) $cart['count']; ?></span>
                        </button>
                        <div class="cart-info">
                            <div>My Cart</div>
                            <div class="doller cart_total_amount">$<?php echo number_format($cart['total'], 2); ?></div>
                        </div>

                        <div class="cart_list color2_bg" aria-labelledby="cartDropdown">
                            <ul>
                                <?php if (empty($cart['items'])): ?>
                                    <li>
                                        <p style="padding: 15px 0;">Your cart is empty.</p>
                                    </li>
                                <?php else: ?>
                                    <?php foreach ($cart['items'] as $item): ?>
                                        <li data-product-id="<?php echo (int) $item['id']; ?>">
                                            <div class="cart_item_wrapper clear_fix">
                                                <div class="img_holder float_left">
                                                    <img width="70" src="products/<?php echo e_cart($item['image']); ?>" alt="Cart Image" class="img-responsive">
                                                </div> <!-- End of .img_holder -->

                                                <div class="item_deatils float_left">
                                                    <h6><?php echo e_cart($item['name']); ?></h6>
                                                    <span class="font_fix">$ <?php echo number_format($item['price'], 2); ?> &times; <?php echo (int) $item['quantity']; ?></span>
                                                </div> <!-- End of .item_deatils -->

                                                <i class="fa fa-times-circle cart_remove_icon" aria-hidden="true"></i>
                                            </div> <!-- End of .cart_item_wrapper -->
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>

                            <?php $cartTotalHidden = empty($cart['items']) ? ' style="display: none;"' : ''; ?>
                            <div class="cart_total clear_fix" <?php echo $cartTotalHidden; ?>>
                                <span class="total font_fix float_left cart_total_text">Total - $<?php echo number_format($cart['total'], 2); ?></span>
                                <a href="cart.php" class="s_color_bg float_right tran3s">View Cart</a>
                            </div>
                        </div> <!-- End of .cart_list -->
                    </div>

                </div>

            </div>

        </div>
    </div> <!-- End of .bottom_header -->
</header>





<!-- Menu ******************************* -->
<div class="theme_menu color1_bg">
    <div class="container">
        <nav class="menuzord pull-left" id="main_menu">
            <ul class="menuzord-menu">
                <li class="<?php echo ($curPageName == 'index.php') ? 'current_page' : ''; ?>">
                    <a href="index">Home</a>
                </li>



                <li class="<?php echo ($curPageName == 'contact.php') ? 'current_page' : ''; ?>">
                    <a href="contact">Contact us</a>
                </li>
                <li class="<?php echo ($curPageName == 'shop.php') ? 'current_page' : ''; ?>">
                    <a href="shop">Shop</a>
                </li>
            </ul> <!-- End of .menuzord-menu -->
        </nav> <!-- End of #main_menu -->


        <!-- ******* Cart And Search Option ******** -->
        <div class="nav_side_content pull-right">
            <ul class="icon_header">
                <li class="border_round tran3s"><a href="#"><i class="fa fa-facebook"></i></a></li>
                <li class="border_round tran3s"><a href="#"><i class="fa fa-twitter"></i></a></li>
                <li class="border_round tran3s"><a href="#"><i class="fa fa-google-plus"></i></a></li>
                <li class="border_round tran3s"><a href="#"><i class="fa fa-pinterest"></i></a></li>
            </ul>
        </div> <!-- End of .nav_side_content -->

    </div> <!-- End of .conatiner -->
</div> <!-- End of .theme_menu -->