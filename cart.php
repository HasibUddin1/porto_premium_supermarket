<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>


<?php
require_once __DIR__ . '/core/db_connection.php'; // exposes $conn
require_once __DIR__ . '/core/cart_helper.php';   // exposes get_cart_summary()

$cart = get_cart_summary($conn);

function e_cart_page($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!doctype html>
<html lang="en">

<head>


    <?php
    $pageInfo = [
        "title" => "Porto Premium Supermarket - Cart",
    ];
    ?>

    <?php include_once "includes/head.php"; ?>

</head>

<body>



    <div class="main_page cart">
        <?php include_once "includes/nav.php"; ?>



        <!-- Cart Breadcrumb -->
        <section
            class="breadcrumb-area"
            style="background-image: url(images/background/2.jpg)">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="breadcrumbs text-center">
                            <h1>Shopping Cart</h1>
                            <h4>Welcome to certified online organic products suppliers</h4>
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
                                <li>
                                    <a href="#"><i class="fa fa-angle-right"></i></a>
                                </li>
                                <li><a href="#">Gallery</a></li>
                                <li>
                                    <a href="#"><i class="fa fa-angle-right"></i></a>
                                </li>
                                <li>Shopping Cart</li>
                            </ul>
                        </div>
                        <div class="col-lg-4 col-md-7 col-sm-7">
                            <p>We provide <span>100% organic</span> products</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- cart Table*************************** -->

        <div class="shop_cart_table container">

            <?php if (empty($cart['items'])): ?>

                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p style="padding: 40px 0; text-align: center;">
                            Your cart is empty. <a href="shop">Continue shopping</a>
                        </p>
                    </div>
                </div>

            <?php else: ?>

                <form action="core/cart_update.php" method="post">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-1">
                                    <tr>
                                        <th><span>Product</span></th>
                                        <th><span>Quantity</span></th>
                                        <th><span>Avalability</span></th>
                                        <th><span>Price</span></th>
                                        <th><span>Total</span></th>
                                        <th><span>Remove</span></th>
                                    </tr>

                                    <?php foreach ($cart['items'] as $item): ?>

                                        <tr>
                                            <td class="flex_item clear_fix">
                                                <img
                                                    src="<?php echo e_cart_page($item['image']); ?>"
                                                    width="70"
                                                    alt="images"
                                                    class="float_left" />
                                                <h6 class="float_left"><?php echo e_cart_page($item['name']); ?></h6>
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    name="quantity[<?php echo (int) $item['id']; ?>]"
                                                    min="0"
                                                    value="<?php echo (int) $item['quantity']; ?>" />
                                            </td>
                                            <td>
                                                <div class="icon_holder border_round">
                                                    <i class="fa fa-check"></i>
                                                </div>
                                                <span class="item">Item(s) <br />Avilable Now</span>
                                            </td>
                                            <td><span>$ <?php echo number_format((float) $item['price'], 2); ?></span></td>
                                            <td><span class="color2">$ <?php echo number_format((float) $item['subtotal'], 2); ?></span></td>
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    name="remove[]"
                                                    value="<?php echo (int) $item['id']; ?>"
                                                    style="vertical-align: -2px" />
                                                <span style="padding-left: 7px">Remove</span>
                                            </td>
                                        </tr>
                                        <!-- /tr -->

                                    <?php endforeach; ?>
                                </table>
                            </div>
                            <!-- /table-responsive -->
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <input
                                type="text"
                                placeholder="Enter Coupon Code..."
                                class="coupon" />
                            <button type="button" class="cart_btn1 tran3s color1_bg">Apply Coupon</button>
                        </div>
                        <div
                            class="col-lg-6 col-md-6 col-sm-6 col-xs-12 cart_update"
                            style="text-align: right">
                            <button type="submit" class="cart_btn3 tran3s">Update Cart</button>
                            <button
                                type="button"
                                class="cart_btn2 tran3s color1_bg"
                                onclick="window.location.href='checkout.php'">
                                Proceed to Checkout
                            </button>
                        </div>
                    </div>
                    <!-- /row -->

                    <div class="row shipping_address">
                        <div
                            class="col-lg-6 col-md-6 col-sm-5 col-xs-12 submit_form wow fadeInUp">
                            <h4>Calculate Shipping</h4>
                            <div class="row" style="margin-top: 33px">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <select class="selectmenu">
                                        <option selected="selected">United Kingdom (UK)</option>
                                        <option>United State (USA)</option>
                                        <option>France</option>
                                    </select>
                                </div>
                                <div
                                    class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space-fix-right">
                                    <input type="text" placeholder="State / Country" required />
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space-fix-left">
                                    <input type="text" placeholder="Zip Code" required />
                                </div>
                            </div>
                            <button type="button" class="cart_btn1 tran3s color1_bg">update Totals</button>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-7 col-xs-12 wow fadeInUp">
                            <h4>Cart Totals</h4>
                            <div class="table-responsive">
                                <table class="table table-2">
                                    <tbody>
                                        <tr>
                                            <td><span>Cart Subtotal</span></td>
                                            <td><span>$<?php echo number_format($cart['total'], 2); ?></span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Shipping and Handling</span></td>
                                            <td><span>Free Shipping</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Order Total</span></td>
                                            <td><span>$<?php echo number_format($cart['total'], 2); ?></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /table-responsive -->
                            <button
                                type="button"
                                class="cart_btn2 tran3s float_right color1_bg"
                                onclick="window.location.href='checkout.php'">
                                Proceed to Checkout
                            </button>
                        </div>
                    </div>
                </form>

            <?php endif; ?>

        </div>
        <!-- /cart_table -->


        <?php include_once "includes/footer.php"; ?>


        <?php include_once "includes/scripts.php"; ?>
    </div>

</body>

</html>