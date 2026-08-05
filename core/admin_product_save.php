<?php
// core/admin_product_save.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/image_helper.php';

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard/admin_products.php');
    exit;
}

$productId  = (int) ($_POST['product_id'] ?? 0);
$isEdit     = $productId > 0;

$fields = [
    'category_id' => (int) ($_POST['category_id'] ?? 0),
    'name'        => trim($_POST['name'] ?? ''),
    'price'       => (float) ($_POST['price'] ?? 0),
    'status'      => in_array($_POST['status'] ?? '', ['New', 'Hot'], true) ? $_POST['status'] : 'New',
    'tags'        => trim($_POST['tags'] ?? ''),
    'description' => trim($_POST['description'] ?? ''),
];

$errors = [];
if ($fields['category_id'] <= 0) $errors[] = 'Please select a category.';
if ($fields['name'] === '')      $errors[] = 'Product name is required.';
if ($fields['price'] <= 0)       $errors[] = 'Please enter a valid price.';

// ---------- Image ----------
$imageError = null;
$destinationDir = __DIR__ . '/../products';
$newFilename = save_uploaded_image_as_webp($_FILES['product_image'] ?? [], $destinationDir, $imageError);

if ($imageError) {
    $errors[] = $imageError;
}
if (!$isEdit && !$newFilename && !$imageError) {
    $errors[] = 'A product image is required.';
}

if (!empty($errors)) {
    $_SESSION['product_form_errors'] = $errors;
    $_SESSION['product_form_old']    = array_merge($fields, ['id' => $productId]);
    header('Location: ../dashboard/admin_product_form.php' . ($isEdit ? '?id=' . $productId : ''));
    exit;
}

$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $fields['name']), '-'));

if ($isEdit) {
    if ($newFilename) {
        $oldStmt = $conn->prepare("SELECT image FROM products WHERE id = ? LIMIT 1");
        $oldStmt->bind_param('i', $productId);
        $oldStmt->execute();
        $oldImage = $oldStmt->get_result()->fetch_assoc()['image'] ?? null;
        $oldStmt->close();

        delete_stored_image($oldImage, $destinationDir);

        $stmt = $conn->prepare("
            UPDATE products SET category_id=?, name=?, slug=?, image=?, price=?, description=?, tags=?, status=?
            WHERE id=?
        ");
        $stmt->bind_param(
            'isssdsssi',
            $fields['category_id'],
            $fields['name'],
            $slug,
            $newFilename,
            $fields['price'],
            $fields['description'],
            $fields['tags'],
            $fields['status'],
            $productId
        );
    } else {
        $stmt = $conn->prepare("
            UPDATE products SET category_id=?, name=?, slug=?, price=?, description=?, tags=?, status=?
            WHERE id=?
        ");
        $stmt->bind_param(
            'issdsssi',
            $fields['category_id'],
            $fields['name'],
            $slug,
            $fields['price'],
            $fields['description'],
            $fields['tags'],
            $fields['status'],
            $productId
        );
    }
    $stmt->execute();
    $stmt->close();
    $_SESSION['admin_products_success'] = 'Product updated.';
} else {
    $stmt = $conn->prepare("
        INSERT INTO products (category_id, name, slug, image, price, description, tags, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        'isssdsss',
        $fields['category_id'],
        $fields['name'],
        $slug,
        $newFilename,
        $fields['price'],
        $fields['description'],
        $fields['tags'],
        $fields['status']
    );
    $stmt->execute();
    $stmt->close();
    $_SESSION['admin_products_success'] = 'Product added.';
}

header('Location: ../dashboard/admin_products.php');
exit;
