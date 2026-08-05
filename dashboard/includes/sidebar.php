<?php
$isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';
$currentPage = basename($_SERVER['SCRIPT_NAME']);

function nav_active($page, $currentPage)
{
    return $page === $currentPage ? ' style="font-weight: bold;"' : '';
}
?>
<div class="dashboard_sidebar" style="min-width: 220px; padding: 20px; border-right: 1px solid #eee;">
    <ul style="list-style: none; padding: 0; margin: 0;">
        <li style="margin-bottom: 12px;">
            <a href="index.php" <?php echo nav_active('index.php', $currentPage); ?>>Overview</a>
        </li>

        <?php if ($isAdmin): ?>
            <li style="margin-bottom: 12px;">
                <a href="admin_users.php" <?php echo nav_active('admin_users.php', $currentPage); ?>>Manage Users</a>
            </li>
            <li style="margin-bottom: 12px;">
                <a href="admin_products.php" <?php echo nav_active('admin_products.php', $currentPage); ?>>Manage Products</a>
            </li>
            <li style="margin-bottom: 12px;">
                <a href="admin_categories.php" <?php echo nav_active('admin_categories.php', $currentPage); ?>>Categories</a>
            </li>
            <li style="margin-bottom: 12px;">
                <a href="admin_orders.php" <?php echo nav_active('admin_orders.php', $currentPage); ?>>Orders &amp; Payments</a>
            </li>
        <?php else: ?>
            <li style="margin-bottom: 12px;">
                <a href="orders.php" <?php echo nav_active('orders.php', $currentPage); ?>>My Orders</a>
            </li>
            <li style="margin-bottom: 12px;">
                <a href="profile.php" <?php echo nav_active('profile.php', $currentPage); ?>>My Account</a>
            </li>
        <?php endif; ?>

        <li style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 12px;">
            <a href="../core/logout.php">Logout</a>
        </li>
    </ul>
</div>