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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Porto Supermarket</title>

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
                <div>
                    <h1>Welcome, <?php echo e_dash($currentUserName); ?></h1>
                    <p class="dashboard-subtext"><?php echo $isAdmin ? 'Admin overview' : 'Your account overview'; ?></p>
                </div>
            </div>

            <?php if ($isAdmin): ?>
                <div class="stat-grid">
                    <div class="stat-card">
                        <p class="stat-value"><?php echo (int) $userCount; ?></p>
                        <p class="stat-label">Total Users</p>
                    </div>
                    <div class="stat-card">
                        <p class="stat-value"><?php echo (int) $productCount; ?></p>
                        <p class="stat-label">Total Products</p>
                    </div>
                    <div class="stat-card">
                        <p class="stat-value"><?php echo (int) $orderCount; ?></p>
                        <p class="stat-label">Total Orders</p>
                    </div>
                    <div class="stat-card">
                        <p class="stat-value">$<?php echo number_format((float) $revenue, 2); ?></p>
                        <p class="stat-label">Revenue (paid orders)</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="action-card">
                    <p>You've placed <strong><?php echo (int) $myOrderCount; ?></strong> order(s) so far.</p>
                    <a href="orders.php" class="btn btn-primary">View Order History</a>
                    <a href="profile.php" class="btn btn-primary">Update My Account</a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>