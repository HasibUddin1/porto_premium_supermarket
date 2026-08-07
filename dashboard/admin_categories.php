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

$categoryErrors  = $_SESSION['category_errors'] ?? [];
$categorySuccess = $_SESSION['category_success'] ?? '';
unset($_SESSION['category_errors'], $_SESSION['category_success']);

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
    <title>Categories - Dashboard</title>

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
                <h1>Categories</h1>
            </div>

            <?php if (!empty($categoryErrors)): ?>
                <div class="dash-alert dash-alert-danger">
                    <ul><?php foreach ($categoryErrors as $err): ?><li><?php echo e_dash($err); ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>
            <?php if ($categorySuccess): ?>
                <div class="dash-alert dash-alert-success"><?php echo e_dash($categorySuccess); ?></div>
            <?php endif; ?>

            <div class="action-card" style="max-width: 480px;">
                <h3 style="margin-top: 0;">Add New Category</h3>
                <form action="../core/admin_category_save.php" method="post" enctype="multipart/form-data" class="dash-form">
                    <div class="form_group">
                        <label>Category Name *</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form_group">
                        <label>Category Image *</label>
                        <input type="file" name="category_image" accept="image/jpeg,image/png,image/gif,image/webp" required>
                        <p class="helper-text">Please use a <strong>1:1 (square)</strong> image — for example <strong>1000&times;1000px</strong>.</p>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </form>
            </div>

            <div class="dash-table-wrap">
                <table class="dash-table">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Products</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><img src="../categories/<?php echo e_dash($cat['image']); ?>" alt="<?php echo e_dash($cat['name']); ?>" class="dash-thumb"></td>
                            <td><?php echo e_dash($cat['name']); ?></td>
                            <td><?php echo (int) $cat['product_count']; ?></td>
                            <td>
                                <a href="admin_products.php?category=<?php echo (int) $cat['id']; ?>">View Products</a>
                                &nbsp;|&nbsp;
                                <form action="../core/admin_category_delete.php" method="post" class="js-confirm-delete" data-message="Delete this category? This can't be undone.">
                                    <input type="hidden" name="category_id" value="<?php echo (int) $cat['id']; ?>">
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