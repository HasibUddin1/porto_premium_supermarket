<?php
require_once __DIR__ . '/../core/db_connection.php';
require_once __DIR__ . '/../core/admin_check.php';

$categoryId = isset($_GET['category']) ? (int) $_GET['category'] : 0;
$search     = trim($_GET['search'] ?? '');

$perPage = 10;
$page    = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset  = ($page - 1) * $perPage;

$whereClauses = [];
if ($categoryId > 0) {
    $whereClauses[] = "p.category_id = " . $categoryId;
}
if ($search !== '') {
    $searchSafe = $conn->real_escape_string($search);
    $whereClauses[] = "p.name LIKE '%$searchSafe%'";
}
$whereSql = $whereClauses ? ' WHERE ' . implode(' AND ', $whereClauses) : '';

// ---------- Total count (for pagination) ----------
$countSql = "SELECT COUNT(*) AS total FROM products p" . $whereSql;
$totalProducts = (int) $conn->query($countSql)->fetch_assoc()['total'];
$totalPages    = max(1, (int) ceil($totalProducts / $perPage));
$page          = min($page, $totalPages); // clamp if someone requests a page past the end
$offset        = ($page - 1) * $perPage;

// ---------- Products for this page ----------
$sql = "
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    $whereSql
    ORDER BY p.id DESC
    LIMIT $perPage OFFSET $offset
";
$products = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

$adminErrors  = $_SESSION['admin_products_errors'] ?? [];
$adminSuccess = $_SESSION['admin_products_success'] ?? '';
unset($_SESSION['admin_products_errors'], $_SESSION['admin_products_success']);

function e_dash($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

// Builds a pagination/query-string link that preserves category + search, only changing page
function build_page_link($pageNum, $categoryId, $search)
{
    $params = ['page' => $pageNum];
    if ($categoryId > 0) $params['category'] = $categoryId;
    if ($search !== '')  $params['search'] = $search;
    return 'admin_products.php?' . http_build_query($params);
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

            <form action="admin_products.php" method="get" class="dash-search-form">
                <?php if ($categoryId > 0): ?>
                    <input type="hidden" name="category" value="<?php echo (int) $categoryId; ?>">
                <?php endif; ?>
                <input type="text" name="search" placeholder="Search products by name..." value="<?php echo e_dash($search); ?>">
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                <?php if ($search !== ''): ?>
                    <a href="admin_products.php<?php echo $categoryId > 0 ? '?category=' . (int) $categoryId : ''; ?>" class="text-muted" style="margin-left: 8px;">Clear search</a>
                <?php endif; ?>
            </form>

            <?php if ($categoryId > 0): ?>
                <p class="text-muted">Filtered by category. <a href="admin_products.php<?php echo $search !== '' ? '?search=' . urlencode($search) : ''; ?>">Clear category filter</a></p>
            <?php endif; ?>

            <?php if (!empty($adminErrors)): ?>
                <div class="dash-alert dash-alert-danger">
                    <ul><?php foreach ($adminErrors as $err): ?><li><?php echo e_dash($err); ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>
            <?php if ($adminSuccess): ?>
                <div class="dash-alert dash-alert-success"><?php echo e_dash($adminSuccess); ?></div>
            <?php endif; ?>

            <?php if (empty($products)): ?>
                <div class="dash-table-wrap">
                    <p class="dash-empty">No products found<?php echo $search !== '' ? ' for "' . e_dash($search) . '"' : ''; ?>.</p>
                </div>
            <?php else: ?>
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

                <?php if ($totalPages > 1): ?>
                    <div class="dash-pagination">
                        <?php if ($page > 1): ?>
                            <a href="<?php echo build_page_link($page - 1, $categoryId, $search); ?>">&laquo; Prev</a>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <a href="<?php echo build_page_link($p, $categoryId, $search); ?>" class="<?php echo $p === $page ? 'active' : ''; ?>"><?php echo $p; ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="<?php echo build_page_link($page + 1, $categoryId, $search); ?>">Next &raquo;</a>
                        <?php endif; ?>
                    </div>
                    <p class="text-muted" style="margin-top: 8px;">
                        Showing <?php echo count($products); ?> of <?php echo $totalProducts; ?> product(s) — page <?php echo $page; ?> of <?php echo $totalPages; ?>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>

    <?php require __DIR__ . '/includes/confirm_modal.php'; ?>
</body>

</html>