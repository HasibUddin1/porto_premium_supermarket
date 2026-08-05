<?php
require_once __DIR__ . '/../core/db_connection.php';
require_once __DIR__ . '/../core/admin_check.php';

$categories = $conn->query("
    SELECT c.id, c.name, c.slug, c.image, COUNT(p.id) AS product_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    GROUP BY c.id
    ORDER BY c.name
")->fetch_all(MYSQLI_ASSOC);

function e_dash($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}
?>

<div class="container" style="margin: 40px auto;">
    <div style="display: flex; gap: 30px;">
        <?php require __DIR__ . '/includes/sidebar.php'; ?>

        <div style="flex: 1;">
            <h2>Categories</h2>

            <div class="table-responsive" style="margin-top: 20px;">
                <table class="table table-1">
                    <tr>
                        <th><span>Image</span></th>
                        <th><span>Name</span></th>
                        <th><span>Products</span></th>
                        <th><span>Actions</span></th>
                    </tr>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><img src="../categories/<?php echo e_dash($cat['image']); ?>" alt="<?php echo e_dash($cat['name']); ?>" style="width: 50px; height: 50px; object-fit: cover;"></td>
                            <td><?php echo e_dash($cat['name']); ?></td>
                            <td><?php echo (int) $cat['product_count']; ?></td>
                            <td><a href="admin_products.php?category=<?php echo (int) $cat['id']; ?>">View Products</a></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>