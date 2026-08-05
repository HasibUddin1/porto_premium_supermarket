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
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Manage Products</h2>
                <a href="admin_product_form.php" class="tran3s color1_bg" style="padding: 10px 20px;">+ Add Product</a>
            </div>

            <?php if ($categoryId > 0): ?>
                <p style="margin-top: 10px;">Filtered by category. <a href="admin_products.php">Clear filter</a></p>
            <?php endif; ?>

            <?php if (!empty($adminErrors)): ?>
                <div class="alert alert-danger">
                    <ul><?php foreach ($adminErrors as $err): ?><li><?php echo e_dash($err); ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>
            <?php if ($adminSuccess): ?>
                <div class="alert alert-success"><?php echo e_dash($adminSuccess); ?></div>
            <?php endif; ?>

            <div class="table-responsive" style="margin-top: 20px;">
                <table class="table table-1">
                    <tr>
                        <th><span>Image</span></th>
                        <th><span>Name</span></th>
                        <th><span>Category</span></th>
                        <th><span>Price</span></th>
                        <th><span>Status</span></th>
                        <th><span>Actions</span></th>
                    </tr>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><img src="../products/<?php echo e_dash($product['image']); ?>" alt="<?php echo e_dash($product['name']); ?>" style="width: 50px; height: 50px; object-fit: cover;"></td>
                            <td><?php echo e_dash($product['name']); ?></td>
                            <td><?php echo e_dash($product['category_name'] ?? '—'); ?></td>
                            <td>$<?php echo number_format((float) $product['price'], 2); ?></td>
                            <td><?php echo e_dash($product['status'] ?? '—'); ?></td>
                            <td>
                                <a href="admin_product_form.php?id=<?php echo (int) $product['id']; ?>">Edit</a>
                                &nbsp;|&nbsp;
                                <form action="../core/admin_product_delete.php" method="post" style="display: inline;" onsubmit="return confirm('Delete this product?');">
                                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                    <button type="submit" style="border: none; background: none; color: #e53935; cursor: pointer; padding: 0;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>