<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>


<!-- Checkout Functionality -->
<?php
require_once __DIR__ . '/core/db_connection.php'; // exposes $conn
require_once __DIR__ . '/core/login_check.php';   // checkout requires login — redirects to login.php if not logged in
require_once __DIR__ . '/core/cart_helper.php';   // exposes get_cart_summary() and e_cart()

$cart = get_cart_summary($conn);

// Nothing to check out — send them back to the cart.
if (empty($cart['items'])) {
    header('Location: cart.php');
    exit;
}

// Prefill billing info from the account where the fields line up.
$userStmt = $conn->prepare("SELECT name, email, phone, location FROM users WHERE id = ? LIMIT 1");
$userStmt->bind_param('i', $currentUserId);
$userStmt->execute();
$accountInfo = $userStmt->get_result()->fetch_assoc();
$userStmt->close();

$nameParts    = explode(' ', trim($accountInfo['name'] ?? ''), 2);
$prefillFirst = $nameParts[0] ?? '';
$prefillLast  = $nameParts[1] ?? '';
$prefillEmail = $accountInfo['email'] ?? '';
$prefillPhone = $accountInfo['phone'] ?? '';

$checkoutErrors = $_SESSION['checkout_errors'] ?? [];
$oldInput       = $_SESSION['checkout_old'] ?? [];
unset($_SESSION['checkout_errors'], $_SESSION['checkout_old']);

function old_val($oldInput, $key, $default = '')
{
    return e_cart($oldInput[$key] ?? $default);
}
?>




<!doctype html>
<html lang="en">

<head>


    <?php
    $pageInfo = [
        "title" => "Porto Premium Supermarket - Checkout",
    ];
    ?>

    <?php include_once "includes/head.php"; ?>

</head>

<body>

    <div class="main_page">
        <?php include_once "includes/nav.php"; ?>

        <section class="breadcrumb-area" style="background-image:url(images/background/2.jpg);">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="breadcrumbs text-center">
                            <h1>checkout</h1>
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
                                <li>checkout</li>
                            </ul>
                        </div>
                        <div class="col-lg-4 col-md-7 col-sm-7">
                            <p>We provide <span>100% organic</span> products</p>
                        </div>
                    </div>
                </div>
            </div>

        </section>


        <!-- Checkout page content******************* -->

        <?php if (!empty($checkoutErrors)): ?>
            <div class="check_out_form container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($checkoutErrors as $err): ?>
                                    <li><?php echo e_cart($err); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="check_out_form container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 submit_form">
                    <div class="theme-title">
                        <h2>Billing Address</h2>
                    </div>
                    <form action="#" class="row" id="billingForm">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <span>Country *</span>
                            <input type="text" name="billing_country" value="<?php echo old_val($oldInput, 'billing_country'); ?>">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <span>First Name *</span>
                            <input type="text" name="billing_first_name" placeholder="" value="<?php echo old_val($oldInput, 'billing_first_name', $prefillFirst); ?>">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <span>Last Name *</span>
                            <input type="text" name="billing_last_name" placeholder="" value="<?php echo old_val($oldInput, 'billing_last_name', $prefillLast); ?>">
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <span>Address</span>
                            <input type="text" name="billing_address1" placeholder="" value="<?php echo old_val($oldInput, 'billing_address1', $accountInfo['location'] ?? ''); ?>">
                            <input type="text" name="billing_address2" placeholder="" value="<?php echo old_val($oldInput, 'billing_address2'); ?>">
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <span>Town / City *</span>
                            <input type="text" name="billing_city" placeholder="" value="<?php echo old_val($oldInput, 'billing_city'); ?>">
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <span>Contact Info *</span>
                            <input type="email" name="email" placeholder="Email Address" value="<?php echo old_val($oldInput, 'email', $prefillEmail); ?>">
                            <input type="text" name="phone" placeholder="Phone Number" value="<?php echo old_val($oldInput, 'phone', $prefillPhone); ?>">
                        </div>
                    </form>
                </div> <!-- /submit_form -->

                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 submit_form shipping_address">
                    <div class="theme-title">
                        <h2>Shipping Address <input type="checkbox" id="shipDifferentCheckbox" <?php echo !empty($oldInput['ship_different']) ? 'checked' : ''; ?>></h2>
                    </div>
                    <form action="#" class="row" id="shippingForm" style="<?php echo !empty($oldInput['ship_different']) ? '' : 'display: none;'; ?>">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <span>Country *</span>
                            <input type="text" name="shipping_country" value="<?php echo old_val($oldInput, 'shipping_country'); ?>">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <span>First Name *</span>
                            <input type="text" name="shipping_first_name" placeholder="" value="<?php echo old_val($oldInput, 'shipping_first_name'); ?>">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <span>Last Name *</span>
                            <input type="text" name="shipping_last_name" placeholder="" value="<?php echo old_val($oldInput, 'shipping_last_name'); ?>">
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <span>Address</span>
                            <input type="text" name="shipping_address1" placeholder="" value="<?php echo old_val($oldInput, 'shipping_address1'); ?>">
                            <input type="text" name="shipping_address2" placeholder="" value="<?php echo old_val($oldInput, 'shipping_address2'); ?>">
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <span>Town / City *</span>
                            <input type="text" name="shipping_city" placeholder="" value="<?php echo old_val($oldInput, 'shipping_city'); ?>">
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <span>Other Notes</span>
                            <textarea name="shipping_notes"><?php echo old_val($oldInput, 'shipping_notes'); ?></textarea>
                        </div>
                    </form>
                </div> <!-- /submit_form -->
            </div> <!-- /row -->
        </div> <!-- /check_out_form -->




        <!-- cart table*********************** -->
        <div class="cart_table container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-1">
                            <thead>
                                <tr>
                                    <th><span>Product</span></th>
                                    <th style="padding-left:0"><span>Quantity</span></th>
                                    <th><span style="margin-left: 9px;">Total</span></th>
                                </tr>
                            </thead> <!-- /thead -->
                            <tbody>

                                <?php foreach ($cart['items'] as $item): ?>
                                    <tr>
                                        <td class="flex_item clear_fix">
                                            <img width="70" src="products/<?php echo e_cart($item['image']); ?>" alt="images" class="float_left">
                                            <h6 class="float_left"><?php echo e_cart($item['name']); ?></h6>
                                        </td>
                                        <td><input type="number" value="<?php echo (int) $item['quantity']; ?>" min="0" disabled></td>
                                        <td><span>$<?php echo number_format((float) $item['subtotal'], 2); ?></span></td>
                                    </tr> <!-- /tr -->
                                <?php endforeach; ?>

                            </tbody> <!-- /tbody -->
                        </table>
                    </div> <!-- /table-responsive -->
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
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
                    </div> <!-- /table-responsive -->
                    <div class="payment_system">
                        <div class="pay1">
                            <input type="radio" name="payment_method" value="bank_transfer" <?php echo (($oldInput['payment_method'] ?? '') === 'bank_transfer') ? 'checked' : ''; ?>>
                            <span>Direct Bank Transfer</span>
                            <p>Make your payment directly into our bank account. Please use your Order ID as the payment reference.order won't be shipped until the funds have cleared.</p>
                        </div>
                        <div class="pay1">
                            <input type="radio" name="payment_method" value="cheque" <?php echo (($oldInput['payment_method'] ?? '') === 'cheque') ? 'checked' : ''; ?>>
                            <span>Cheque Payment</span>
                        </div>
                        <div class="pay1">
                            <input type="radio" name="payment_method" value="stripe" <?php echo (($oldInput['payment_method'] ?? '') === 'stripe') ? 'checked' : ''; ?>>
                            <span>Pay with Card (Stripe)</span>
                            <img src="images/check-out/1.jpg" alt="image" class="float_right">
                        </div>
                        <div class="pay1">
                            <input type="radio" name="payment_method" value="paypal" <?php echo (($oldInput['payment_method'] ?? '') === 'paypal') ? 'checked' : ''; ?>>
                            <span>Paypal</span>
                            <img src="images/check-out/2.jpg" alt="image" class="float_right">
                        </div>
                        <a href="#" id="placeOrderBtn" class="tran3s color2_bg float_right">Place Order</a>
                    </div>
                </div>
            </div>
        </div> <!-- /cart_table -->


        <?php include_once "includes/footer.php"; ?>


        <?php include_once "includes/scripts.php"; ?>
    </div>

</body>

</html>