<?php
require_once __DIR__ . '/../core/db_connection.php';
require_once __DIR__ . '/../core/login_check.php';

$ordersStmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$ordersStmt->bind_param('i', $currentUserId);
$ordersStmt->execute();
$orders = $ordersStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$ordersStmt->close();

function e_dash($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon.png" />
    <title>My Orders - Dashboard</title>

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
                <h1>My Orders</h1>
            </div>

            <?php if (empty($orders)): ?>
                <div class="dash-table-wrap">
                    <p class="dash-empty">You haven't placed any orders yet. <a href="../shop">Start shopping</a>.</p>
                </div>
            <?php else: ?>
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Delivery</th>
                        </tr>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo (int) $order['id']; ?></td>
                                <td><?php echo e_dash(date('d M Y', strtotime($order['created_at']))); ?></td>
                                <td>$<?php echo number_format((float) $order['total_amount'], 2); ?></td>
                                <td><span class="badge badge-<?php echo e_dash($order['payment_status']); ?>"><?php echo e_dash(ucfirst($order['payment_status'])); ?></span></td>
                                <td><?php echo e_dash(ucfirst($order['status'])); ?></td>
                                <td>
                                    <?php if ($order['delivered_at']): ?>
                                        <span class="badge badge-delivered">Delivered</span><br>
                                        <span class="text-muted"><?php echo e_dash(date('d M Y', strtotime($order['delivered_at']))); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Not delivered yet</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>