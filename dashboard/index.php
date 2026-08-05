<?php
require_once __DIR__ . '/../core/db_connection.php';
require_once __DIR__ . '/../core/login_check.php';

$isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';

if ($isAdmin) {
    $userCount    = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
    $productCount = $conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'];
    $orderCount   = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];
    $revenue      = $conn->query("SELECT COALESCE(SUM(total_amount),0) AS s FROM orders WHERE payment_status = 'paid'")->fetch_assoc()['s'];
} else {
    $orderStmt = $conn->prepare("SELECT COUNT(*) AS c FROM orders WHERE user_id = ?");
    $orderStmt->bind_param('i', $currentUserId);
    $orderStmt->execute();
    $myOrderCount = $orderStmt->get_result()->fetch_assoc()['c'];
    $orderStmt->close();
}

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
            <h2>Welcome, <?php echo e_dash($currentUserName); ?></h2>

            <?php if ($isAdmin): ?>
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-3 col-sm-6" style="padding: 15px; border: 1px solid #eee; margin: 0 10px 10px 0; display: inline-block;">
                        <h4><?php echo (int) $userCount; ?></h4>
                        <span>Total Users</span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="padding: 15px; border: 1px solid #eee; margin: 0 10px 10px 0; display: inline-block;">
                        <h4><?php echo (int) $productCount; ?></h4>
                        <span>Total Products</span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="padding: 15px; border: 1px solid #eee; margin: 0 10px 10px 0; display: inline-block;">
                        <h4><?php echo (int) $orderCount; ?></h4>
                        <span>Total Orders</span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="padding: 15px; border: 1px solid #eee; margin: 0 10px 10px 0; display: inline-block;">
                        <h4>$<?php echo number_format((float) $revenue, 2); ?></h4>
                        <span>Revenue (paid orders)</span>
                    </div>
                </div>
            <?php else: ?>
                <p style="margin-top: 15px;">You've placed <strong><?php echo (int) $myOrderCount; ?></strong> order(s) so far.</p>
                <p><a href="orders.php" class="tran3s color1_bg" style="display: inline-block; padding: 10px 20px;">View Order History</a></p>
                <p><a href="profile.php" class="tran3s color1_bg" style="display: inline-block; padding: 10px 20px;">Update My Account</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>