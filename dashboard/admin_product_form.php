<?php
require_once __DIR__ . '/../core/db_connection.php';
require_once __DIR__ . '/../core/admin_check.php';

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$isEdit    = $productId > 0;
$product   = [
    'id' => 0,
    'category_id' => '',
    'name' => '',
    'slug' => '',
    'image' => '',
    'price' => '',
    'description' => '',
    'tags' => '',
    'status' => 'New',
];

if ($isEdit) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $found = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$found) {
        header('Location: admin_products.php');
        exit;
    }
    $product = $found;
}

$categories = $conn->query("SELECT id, name FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$formErrors = $_SESSION['product_form_errors'] ?? [];
$oldInput   = $_SESSION['product_form_old'] ?? [];
unset($_SESSION['product_form_errors'], $_SESSION['product_form_old']);

if (!empty($oldInput)) {
    $product = array_merge($product, $oldInput);
}

function e_dash($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}
?>

<div class="container" style="margin: 40px auto;">
    <div style="display: flex; gap: 30px;">
        <?php require __DIR__ . '/includes/sidebar.php'; ?>

        <div style="flex: 1; max-width: 700px;">
            <h2><?php echo $isEdit ? 'Edit Product' : 'Add Product'; ?></h2>

            <?php if (!empty($formErrors)): ?>
                <div class="alert alert-danger">
                    <ul><?php foreach ($formErrors as $err): ?><li><?php echo e_dash($err); ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <?php if ($isEdit && $product['image']): ?>
                <img src="../products/<?php echo e_dash($product['image']); ?>" alt="Current image" style="width: 120px; height: 120px; object-fit: cover; margin-bottom: 15px;">
            <?php endif; ?>

            <form action="../core/admin_product_save.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">

                <div class="form_group">
                    <label>Product Image <?php echo $isEdit ? '(leave empty to keep current image)' : ''; ?></label>
                    <input type="file" name="product_image" accept="image/jpeg,image/png,image/gif,image/webp" <?php echo $isEdit ? '' : 'required'; ?>>
                    <p style="font-size: 12px; color: #888; margin-top: 5px;">
                        Please upload a <strong>1:1 (square) ratio</strong> image — for example <strong>1000&times;1000px</strong>.
                        The image will automatically be converted to WebP.
                    </p>
                </div>

                <div class="form_group">
                    <label>Category *</label>
                    <select name="category_id" required>
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo (int) $cat['id']; ?>" <?php echo ((int) $product['category_id'] === (int) $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo e_dash($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form_group">
                    <label>Product Name *</label>
                    <input type="text" name="name" value="<?php echo e_dash($product['name']); ?>" required>
                </div>

                <div class="form_group">
                    <label>Price ($) *</label>
                    <input type="number" step="0.01" min="0" name="price" value="<?php echo e_dash($product['price']); ?>" required>
                </div>

                <div class="form_group">
                    <label>Status</label>
                    <select name="status">
                        <option value="New" <?php echo $product['status'] === 'New' ? 'selected' : ''; ?>>New</option>
                        <option value="Hot" <?php echo $product['status'] === 'Hot' ? 'selected' : ''; ?>>Hot</option>
                    </select>
                </div>

                <div class="form_group">
                    <label>Tags (comma-separated)</label>
                    <input type="text" name="tags" value="<?php echo e_dash($product['tags']); ?>" placeholder="fresh, organic, healthy">
                </div>

                <div class="form_group">
                    <label>Description</label>
                    <textarea name="description" rows="5"><?php echo e_dash($product['description']); ?></textarea>
                </div>

                <button type="submit" class="tran3s color1_bg"><?php echo $isEdit ? 'Update Product' : 'Add Product'; ?></button>
            </form>
        </div>
    </div>
</div>