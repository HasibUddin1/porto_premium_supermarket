<?php
$isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';
$currentPage = basename($_SERVER['SCRIPT_NAME']);

function nav_active_class($page, $currentPage)
{
    return $page === $currentPage ? ' class="active"' : '';
}
?>
<aside class="dashboard-sidebar">
    <div class="brand">Porto Supermarket</div>
    <ul>
        <li><a href="index.php" <?php echo nav_active_class('index.php', $currentPage); ?>>Overview</a></li>

        <?php if ($isAdmin): ?>
            <li><a href="admin_users.php" <?php echo nav_active_class('admin_users.php', $currentPage); ?>>Manage Users</a></li>
            <li><a href="admin_products.php" <?php echo nav_active_class('admin_products.php', $currentPage); ?>>Manage Products</a></li>
            <li><a href="admin_product_form.php" <?php echo nav_active_class('admin_product_form.php', $currentPage); ?>>Add Product</a></li>
            <li><a href="admin_categories.php" <?php echo nav_active_class('admin_categories.php', $currentPage); ?>>Categories</a></li>
            <li><a href="admin_orders.php" <?php echo nav_active_class('admin_orders.php', $currentPage); ?>>Orders &amp; Payments</a></li>
        <?php else: ?>
            <li><a href="orders.php" <?php echo nav_active_class('orders.php', $currentPage); ?>>My Orders</a></li>
            <li><a href="profile.php" <?php echo nav_active_class('profile.php', $currentPage); ?>>My Account</a></li>
        <?php endif; ?>
    </ul>

    <div class="sidebar-section">
        <ul>
            <li><a href="../core/logout.php" class="logout-link">Logout</a></li>
        </ul>
    </div>
</aside>