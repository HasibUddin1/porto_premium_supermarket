<?php
require_once __DIR__ . '/../core/db_connection.php';
require_once __DIR__ . '/../core/admin_check.php';

$paymentFilter = $_GET['payment_status'] ?? '';

$sql = "SELECT * FROM orders";
if (in_array($paymentFilter, ['paid', 'unpaid', 'failed'], true)) {
    $paymentFilterSafe = $conn->real_escape_string($paymentFilter);
    $sql .= " WHERE payment_status = '$paymentFilterSafe' ";
}
$sql .= " ORDER BY created_at DESC ";

$orders = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

$adminSuccess = $_SESSION['admin_orders_success'] ?? '';
unset($_SESSION['admin_orders_success']);

function e_dash($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}
?>

<div class="container" style="margin: 40px auto;">
    <div style="display: flex; gap: 30px;">
        <?php require __DIR__ . '/includes/sidebar.php'; ?>

        <div style="flex: 1;">
            <h2>Orders &amp; Payment History</h2>

            <div style="margin: 15px 0;">
                <a href="admin_orders.php" style="margin-right: 12px; <?php echo $paymentFilter === '' ? 'font-weight: bold;' : ''; ?>">All</a>
                <a href="admin_orders.php?payment_status=paid" style="margin-right: 12px; <?php echo $paymentFilter === 'paid' ? 'font-weight: bold;' : ''; ?>">Paid</a>
                <a href="admin_orders.php?payment_status=unpaid" style="margin-right: 12px; <?php echo $paymentFilter === 'unpaid' ? 'font-weight: bold;' : ''; ?>">Unpaid</a>
                <a href="admin_orders.php?payment_status=failed" style="<?php echo $paymentFilter === 'failed' ? 'font-weight: bold;' : ''; ?>">Failed</a>
            </div>

            <?php if ($adminSuccess): ?>
                <div class="alert alert-success"><?php echo e_dash($adminSuccess); ?></div>
            <?php endif; ?>

            <div class="table-responsive" style="margin-top: 10px;">
                <table class="table table-1">
                    <tr>
                        <th><span>Order #</span></th>
                        <th><span>Customer</span></th>
                        <th><span>Date</span></th>
                        <th><span>Total</span></th>
                        <th><span>Payment</span></th>
                        <th><span>Status</span></th>
                        <th><span>Delivery</span></th>
                    </tr>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo (int) $order['id']; ?></td>
                            <td>
                                <?php echo e_dash($order['customer_name']); ?><br>
                                <span style="font-size: 12px; color: #888;"><?php echo e_dash($order['email']); ?> &middot; <?php echo e_dash($order['phone']); ?></span>
                            </td>
                            <td><?php echo e_dash(date('d M Y', strtotime($order['created_at']))); ?></td>
                            <td>$<?php echo number_format((float) $order['total_amount'], 2); ?></td>
                            <td>
                                <?php
                                $paymentColor = ['paid' => '#4caf50', 'unpaid' => '#f0ad4e', 'failed' => '#e53935'][$order['payment_status']] ?? '#888';
                                ?>
                                <span style="color: <?php echo $paymentColor; ?>; font-weight: bold;"><?php echo e_dash(ucfirst($order['payment_status'])); ?></span>
                                <br><span style="font-size: 12px; color: #888;"><?php echo e_dash(str_replace('_', ' ', ucfirst($order['payment_method']))); ?></span>
                            </td>
                            <td><?php echo e_dash(ucfirst($order['status'])); ?></td>
                            <td>
                                <?php if ($order['delivered_at']): ?>
                                    <span style="color: #4caf50;">Delivered<br><?php echo e_dash(date('d M Y', strtotime($order['delivered_at']))); ?></span>
                                <?php else: ?>
                                    <form action="../core/admin_order_delivered.php" method="post">
                                        <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
                                        <button type="submit" class="tran3s color1_bg" style="padding: 4px 10px; font-size: 12px;">Mark Delivered</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>