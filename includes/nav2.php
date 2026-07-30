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
                        <li><i class="fa fa-heart s_color" aria-hidden="true"></i><a href="#">Whishlist </a></li>
                    </ul>

                </div>


            </div>
        </div> <!-- End of .container -->
    </div> <!-- End of .top_header -->

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
                        <a href="index" class="pull-left logo"><img width="300px" src="../images/logo/porto_logo_light2.webp" alt="LOGO"></a>
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
                                        <select class="text-capitalize selectpicker" data-style="g-select" data-width="100%">
                                            <option value="0" selected="">Sign In</option>
                                            <option value="1">Sign In</option>
                                            <option value="2">Register Here</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div> <!-- End of .cart_list -->
                    </div>
                    <div class="cart_option float_left">
                        <button class="cart tran3s dropdown-toggle" id="cartDropdown"><i class="fa icon-icon-32846" aria-hidden="true"></i><span class="s_color_bg p_color">2</span></button>
                        <div class="cart-info">
                            <div>My Cart</div>
                            <div class="doller">84.00$</div>
                        </div>

                        <div class="cart_list color2_bg" aria-labelledby="cartDropdown">
                            <ul>
                                <li>
                                    <div class="cart_item_wrapper clear_fix">
                                        <div class="img_holder float_left"><img src="images/shop/9.png" alt="Cart Image" class="img-responsive"></div> <!-- End of .img_holder -->

                                        <div class="item_deatils float_left">
                                            <h6>Turmeric Powde</h6>
                                            <ul>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                            </ul>
                                            <span class="font_fix">$ 34.99</span>
                                        </div> <!-- End of .item_deatils -->
                                    </div> <!-- End of .cart_item_wrapper -->
                                </li>

                                <li>
                                    <div class="cart_item_wrapper clear_fix">
                                        <div class="img_holder float_left"><img src="images/shop/10.png" alt="Cart Image" class="img-responsive"></div> <!-- End of .img_holder -->

                                        <div class="item_deatils float_left">
                                            <h6>Pure Jeans Coffee</h6>
                                            <ul>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                            </ul>
                                            <span class="font_fix">$ 26.99</span>
                                        </div> <!-- End of .item_deatils -->
                                    </div> <!-- End of .cart_item_wrapper -->
                                </li>

                                <li>
                                    <div class="cart_item_wrapper clear_fix">
                                        <div class="img_holder float_left"><img src="images/shop/11.png" alt="Cart Image" class="img-responsive"></div> <!-- End of .img_holder -->

                                        <div class="item_deatils float_left">
                                            <h6>Columbia Chocolate</h6>
                                            <ul>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                            </ul>
                                            <span class="font_fix">$ 26.99</span>
                                        </div> <!-- End of .item_deatils -->
                                    </div> <!-- End of .cart_item_wrapper -->
                                </li>
                            </ul>

                            <div class="cart_total clear_fix">

                                <span class="total font_fix float_left">Total - 140$</span>
                                <a href="#" class="s_color_bg float_right tran3s">View Cart</a>
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