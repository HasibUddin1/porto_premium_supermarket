<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>


<?php
require_once __DIR__ . '/core/db_connection.php';
require_once __DIR__ . '/core/login_check.php';

$orderId = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;

$order = null;
$items = [];

if ($orderId > 0) {
    $orderStmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
    $orderStmt->bind_param('ii', $orderId, $currentUserId);
    $orderStmt->execute();
    $order = $orderStmt->get_result()->fetch_assoc();
    $orderStmt->close();

    if ($order) {
        $itemsStmt = $conn->prepare("
            SELECT oi.quantity, oi.price, p.name, p.image
            FROM order_items oi
            INNER JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = ?
        ");
        $itemsStmt->bind_param('i', $orderId);
        $itemsStmt->execute();
        $items = $itemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $itemsStmt->close();
    }
}

function e_order($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>


<!doctype html>
<html lang="en">

<head>


    <?php
    $pageInfo = [
        "title" => "Porto Premium Supermarket - Order Success!",
    ];
    ?>

    <?php include_once "includes/head.php"; ?>

</head>

<body>



    <div class="main_page">
        <?php include_once "includes/nav.php"; ?>

        <div class="check_out_form container">
            <?php if (!$order): ?>
                <div class="row">
                    <div class="col-lg-12">
                        <p style="padding: 40px 0; text-align: center;">
                            We couldn't find that order. <a href="shop">Continue shopping</a>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="theme-title">
                            <h2>Thank you, <?php echo e_order($order['customer_name']); ?>!</h2>
                        </div>
                        <p>Your order <strong>#<?php echo (int) $order['id']; ?></strong> has been placed and is currently <strong><?php echo e_order($order['status']); ?></strong>.</p>
                        <p>A confirmation will be sent to <?php echo e_order($order['email']); ?>.</p>

                        <div class="table-responsive" style="margin-top: 20px;">
                            <table class="table table-1">
                                <thead>
                                    <tr>
                                        <th><span>Product</span></th>
                                        <th><span>Quantity</span></th>
                                        <th><span>Total</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td class="flex_item clear_fix">
                                                <img src="<?php echo e_order($item['image']); ?>" alt="images" class="float_left">
                                                <h6 class="float_left"><?php echo e_order($item['name']); ?></h6>
                                            </td>
                                            <td><?php echo (int) $item['quantity']; ?></td>
                                            <td><span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <p style="margin-top: 15px;"><strong>Shipping to:</strong> <?php echo e_order($order['shipping_address']); ?></p>
                        <p><strong>Order Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>

                        <a href="shop" class="tran3s color1_bg" style="display: inline-block; margin-top: 20px; padding: 10px 25px;">Continue Shopping</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>


        <?php include_once "includes/footer.php"; ?>


        <?php include_once "includes/scripts.php"; ?>
    </div>

</body>

</html>