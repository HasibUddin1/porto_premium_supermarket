<?php
require_once __DIR__ . '/../core/db_connection.php';
require_once __DIR__ . '/../core/admin_check.php';

$categoryId = isset($_GET['category']) ? (int) $_GET['category'] : 0;

$sql = "
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
";
if ($categoryId > 0) {
    $sql .= " WHERE p.category_id = $categoryId ";
}
$sql .= " ORDER BY p.id DESC ";

$products = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

$adminErrors  = $_SESSION['admin_products_errors'] ?? [];
$adminSuccess = $_SESSION['admin_products_success'] ?? '';
unset($_SESSION['admin_products_errors'], $_SESSION['admin_products_success']);

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
    <title>Manage Products - Dashboard</title>

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
                <h1>Manage Products</h1>
                <a href="admin_product_form.php" class="btn btn-primary">+ Add Product</a>
            </div>

            <?php if ($categoryId > 0): ?>
                <p class="text-muted">Filtered by category. <a href="admin_products.php">Clear filter</a></p>
            <?php endif; ?>

            <?php if (!empty($adminErrors)): ?>
                <div class="dash-alert dash-alert-danger">
                    <ul><?php foreach ($adminErrors as $err): ?><li><?php echo e_dash($err); ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>
            <?php if ($adminSuccess): ?>
                <div class="dash-alert dash-alert-success"><?php echo e_dash($adminSuccess); ?></div>
            <?php endif; ?>

            <div class="dash-table-wrap">
                <table class="dash-table">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><img src="../products/<?php echo e_dash($product['image']); ?>" alt="<?php echo e_dash($product['name']); ?>" class="dash-thumb"></td>
                            <td><?php echo e_dash($product['name']); ?></td>
                            <td><?php echo e_dash($product['category_name'] ?? '—'); ?></td>
                            <td>$<?php echo number_format((float) $product['price'], 2); ?></td>
                            <td><span class="badge badge-<?php echo strtolower(e_dash($product['status'] ?? 'new')); ?>"><?php echo e_dash($product['status'] ?? '—'); ?></span></td>
                            <td>
                                <a href="admin_product_form.php?id=<?php echo (int) $product['id']; ?>">Edit</a>
                                &nbsp;|&nbsp;
                                <form action="../core/admin_product_delete.php" method="post" style="display: inline;" class="js-confirm-delete" data-message="Delete this product? This can't be undone.">
                                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                    <button type="submit" class="btn-link-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </main>
    </div>

    <?php require __DIR__ . '/includes/confirm_modal.php'; ?>
</body>

</html>