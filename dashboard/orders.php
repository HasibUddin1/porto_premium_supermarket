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

<head>
    <?php
    $pageInfo = [
        "title" => "Porto Premium Supermarket - Dashboard",
    ];
    ?>

    <?php include_once __DIR__ . '/includes/head.php'; ?>
</head>

<div class="container" style="margin: 40px auto;">
    <div style="display: flex; gap: 30px;">
        <?php require __DIR__ . '/includes/sidebar.php'; ?>

        <div style="flex: 1;">
            <h2>My Orders</h2>

            <?php if (empty($orders)): ?>
                <p style="margin-top: 15px;">You haven't placed any orders yet. <a href="../shop">Start shopping</a>.</p>
            <?php else: ?>
                <div class="table-responsive" style="margin-top: 20px;">
                    <table class="table table-1">
                        <tr>
                            <th><span>Order #</span></th>
                            <th><span>Date</span></th>
                            <th><span>Total</span></th>
                            <th><span>Payment</span></th>
                            <th><span>Status</span></th>
                            <th><span>Delivery</span></th>
                        </tr>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo (int) $order['id']; ?></td>
                                <td><?php echo e_dash(date('d M Y', strtotime($order['created_at']))); ?></td>
                                <td>$<?php echo number_format((float) $order['total_amount'], 2); ?></td>
                                <td><?php echo e_dash(ucfirst($order['payment_status'])); ?></td>
                                <td><?php echo e_dash(ucfirst($order['status'])); ?></td>
                                <td>
                                    <?php if ($order['delivered_at']): ?>
                                        Delivered on <?php echo e_dash(date('d M Y', strtotime($order['delivered_at']))); ?>
                                    <?php else: ?>
                                        Not delivered yet
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>