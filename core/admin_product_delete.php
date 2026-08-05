<?php
// core/admin_product_delete.php

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

$productId = (int) ($_POST['product_id'] ?? 0);

if ($productId <= 0) {
    header('Location: ../dashboard/admin_products.php');
    exit;
}

$stmt = $conn->prepare("SELECT image FROM products WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $productId);
$stmt->execute();
$image = $stmt->get_result()->fetch_assoc()['image'] ?? null;
$stmt->close();

$delStmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$delStmt->bind_param('i', $productId);
$delStmt->execute();
$delStmt->close();

delete_stored_image($image, __DIR__ . '/../products');

$_SESSION['admin_products_success'] = 'Product deleted.';
header('Location: ../dashboard/admin_products.php');
exit;
