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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit Product' : 'Add Product'; ?> - Dashboard</title>

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
                <h1><?php echo $isEdit ? 'Edit Product' : 'Add Product'; ?></h1>
            </div>

            <div class="action-card" style="max-width: 720px;">
                <?php if (!empty($formErrors)): ?>
                    <div class="dash-alert dash-alert-danger">
                        <ul><?php foreach ($formErrors as $err): ?><li><?php echo e_dash($err); ?></li><?php endforeach; ?></ul>
                    </div>
                <?php endif; ?>

                <?php if ($isEdit && $product['image']): ?>
                    <img src="../products/<?php echo e_dash($product['image']); ?>" alt="Current image" class="dash-thumb" style="width: 100px; height: 100px; margin-bottom: 15px;">
                <?php endif; ?>

                <form action="../core/admin_product_save.php" method="post" enctype="multipart/form-data" class="dash-form" id="productForm">
                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">

                    <div class="form_group">
                        <label>Product Image <?php echo $isEdit ? '(leave empty to keep current image)' : ''; ?></label>
                        <input type="file" name="product_image" accept="image/jpeg,image/png,image/gif,image/webp" <?php echo $isEdit ? '' : 'required'; ?>>
                        <p class="helper-text">
                            Please upload a <strong>1:1 (square) ratio</strong> image — for example <strong>1000&times;1000px</strong>.
                            The image will automatically be converted to WebP.
                        </p>
                    </div>

                    <div class="form_group">
                        <label>Category *</label>
                        <div style="display: flex; gap: 10px; align-items: flex-start;">
                            <select name="category_id" id="categorySelect" required style="flex: 1;">
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo (int) $cat['id']; ?>" <?php echo ((int) $product['category_id'] === (int) $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo e_dash($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" id="toggleNewCategory" class="btn btn-primary btn-sm" style="white-space: nowrap;">+ New Category</button>
                        </div>

                        <div id="newCategoryPanel" style="display: none; margin-top: 15px; padding: 15px; background: var(--bg-light); border-radius: var(--radius); border: 1px solid var(--border-color);">
                            <div class="form_group">
                                <label>New Category Name</label>
                                <input type="text" id="newCategoryName">
                            </div>
                            <div class="form_group">
                                <label>New Category Image</label>
                                <input type="file" id="newCategoryImage" accept="image/jpeg,image/png,image/gif,image/webp">
                                <p class="helper-text">1:1 (square) ratio recommended — e.g. <strong>1000&times;1000px</strong>.</p>
                            </div>
                            <div id="newCategoryError" class="dash-alert dash-alert-danger" style="display: none;"></div>
                            <button type="button" id="saveNewCategory" class="btn btn-primary btn-sm">Save Category</button>
                            <button type="button" id="cancelNewCategory" class="btn-link-danger" style="margin-left: 10px;">Cancel</button>
                        </div>
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
                        <label>Tags</label>
                        <div class="tag-input-wrap">
                            <div class="tag-chips" id="tagChips"></div>
                            <input type="text" id="tagInput" placeholder="Type a tag and press Enter or comma...">
                        </div>
                        <input type="hidden" name="tags" id="tagsHidden" value="<?php echo e_dash($product['tags']); ?>">
                        <p class="helper-text">Press <strong>Enter</strong> or <strong>,</strong> after each tag.</p>
                    </div>

                    <div class="form_group">
                        <label>Description</label>
                        <textarea name="description" rows="5"><?php echo e_dash($product['description']); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Update Product' : 'Add Product'; ?></button>
                </form>
            </div>
        </main>
    </div>

    <script>
        // ---------- Tags: comma-separated text -> chip/token UI ----------
        (function() {
            var chipsWrap = document.getElementById('tagChips');
            var tagInput = document.getElementById('tagInput');
            var tagsHidden = document.getElementById('tagsHidden');

            function currentTags() {
                return tagsHidden.value ? tagsHidden.value.split(',').map(function(t) {
                    return t.trim();
                }).filter(Boolean) : [];
            }

            function renderChips() {
                chipsWrap.innerHTML = '';
                currentTags().forEach(function(tag, index) {
                    var chip = document.createElement('span');
                    chip.className = 'tag-chip';
                    chip.textContent = tag;

                    var removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'tag-chip-remove';
                    removeBtn.textContent = '\u00D7';
                    removeBtn.addEventListener('click', function() {
                        var tags = currentTags();
                        tags.splice(index, 1);
                        tagsHidden.value = tags.join(',');
                        renderChips();
                    });

                    chip.appendChild(removeBtn);
                    chipsWrap.appendChild(chip);
                });
            }

            function addTag(value) {
                value = value.trim();
                if (!value) return;
                var tags = currentTags();
                if (tags.indexOf(value) === -1) {
                    tags.push(value);
                    tagsHidden.value = tags.join(',');
                    renderChips();
                }
                tagInput.value = '';
            }

            tagInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    addTag(tagInput.value);
                } else if (e.key === 'Backspace' && tagInput.value === '') {
                    var tags = currentTags();
                    if (tags.length) {
                        tags.pop();
                        tagsHidden.value = tags.join(',');
                        renderChips();
                    }
                }
            });

            tagInput.addEventListener('blur', function() {
                if (tagInput.value.trim()) addTag(tagInput.value);
            });

            renderChips(); // initial render (populates from PHP value on edit)
        })();

        // ---------- Inline "add new category" panel (AJAX, no page reload) ----------
        (function() {
            var toggleBtn = document.getElementById('toggleNewCategory');
            var panel = document.getElementById('newCategoryPanel');
            var cancelBtn = document.getElementById('cancelNewCategory');
            var saveBtn = document.getElementById('saveNewCategory');
            var nameInput = document.getElementById('newCategoryName');
            var imageInput = document.getElementById('newCategoryImage');
            var errorBox = document.getElementById('newCategoryError');
            var categorySelect = document.getElementById('categorySelect');

            toggleBtn.addEventListener('click', function() {
                panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
            });
            cancelBtn.addEventListener('click', function() {
                panel.style.display = 'none';
                errorBox.style.display = 'none';
                nameInput.value = '';
                imageInput.value = '';
            });

            saveBtn.addEventListener('click', function() {
                errorBox.style.display = 'none';

                var formData = new FormData();
                formData.append('name', nameInput.value);
                if (imageInput.files[0]) {
                    formData.append('category_image', imageInput.files[0]);
                }
                formData.append('ajax', '1');

                fetch('../core/admin_category_save.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(function(res) {
                        return res.json();
                    })
                    .then(function(data) {
                        if (data.success) {
                            var option = document.createElement('option');
                            option.value = data.id;
                            option.textContent = data.name;
                            option.selected = true;
                            categorySelect.appendChild(option);

                            panel.style.display = 'none';
                            nameInput.value = '';
                            imageInput.value = '';
                        } else {
                            errorBox.textContent = (data.errors || ['Could not add category.']).join(' ');
                            errorBox.style.display = 'block';
                        }
                    })
                    .catch(function() {
                        errorBox.textContent = 'Something went wrong. Please try again.';
                        errorBox.style.display = 'block';
                    });
            });
        })();
    </script>
</body>

</html>