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
function tab_class($val, $current)
{
    return $val === $current ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon.png" />
    <title>Orders &amp; Payments - Dashboard</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="dashboard-wrapper">
        <?php require __DIR__ . '/includes/sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="dashboard-header">
                <h1>Orders &amp; Payment History</h1>
            </div>

            <div class="filter-tabs">
                <a href="admin_orders.php" class="<?php echo tab_class('', $paymentFilter); ?>">All</a>
                <a href="admin_orders.php?payment_status=paid" class="<?php echo tab_class('paid', $paymentFilter); ?>">Paid</a>
                <a href="admin_orders.php?payment_status=unpaid" class="<?php echo tab_class('unpaid', $paymentFilter); ?>">Unpaid</a>
                <a href="admin_orders.php?payment_status=failed" class="<?php echo tab_class('failed', $paymentFilter); ?>">Failed</a>
            </div>

            <?php if ($adminSuccess): ?>
                <div class="dash-alert dash-alert-success"><?php echo e_dash($adminSuccess); ?></div>
            <?php endif; ?>

            <div class="dash-table-wrap">
                <table class="dash-table">
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Delivery</th>
                    </tr>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo (int) $order['id']; ?></td>
                            <td>
                                <?php echo e_dash($order['customer_name']); ?><br>
                                <span class="text-muted"><?php echo e_dash($order['email']); ?> &middot; <?php echo e_dash($order['phone']); ?></span>
                            </td>
                            <td><?php echo e_dash(date('d M Y', strtotime($order['created_at']))); ?></td>
                            <td>$<?php echo number_format((float) $order['total_amount'], 2); ?></td>
                            <td>
                                <span class="badge badge-<?php echo e_dash($order['payment_status']); ?>"><?php echo e_dash(ucfirst($order['payment_status'])); ?></span>
                                <br><span class="text-muted"><?php echo e_dash(str_replace('_', ' ', ucfirst($order['payment_method']))); ?></span>
                            </td>
                            <td><?php echo e_dash(ucfirst($order['status'])); ?></td>
                            <td>
                                <?php if ($order['delivered_at']): ?>
                                    <span class="badge badge-delivered">Delivered</span><br>
                                    <span class="text-muted"><?php echo e_dash(date('d M Y', strtotime($order['delivered_at']))); ?></span>
                                <?php else: ?>
                                    <form action="../core/admin_order_delivered.php" method="post">
                                        <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">Mark Delivered</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </main>
    </div>
</body>

</html>