<?php
// core/admin_category_delete.php

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
    header('Location: ../dashboard/admin_categories.php');
    exit;
}

$categoryId = (int) ($_POST['category_id'] ?? 0);

if ($categoryId <= 0) {
    header('Location: ../dashboard/admin_categories.php');
    exit;
}

// Don't allow deleting a category that still has products in it —
// that would leave those products pointing at a category_id that no longer exists.
$countStmt = $conn->prepare("SELECT COUNT(*) AS c FROM products WHERE category_id = ?");
$countStmt->bind_param('i', $categoryId);
$countStmt->execute();
$productCount = $countStmt->get_result()->fetch_assoc()['c'];
$countStmt->close();

if ($productCount > 0) {
    $_SESSION['category_errors'] = [
        "Can't delete this category — it still has $productCount product(s) in it. Move or delete those products first."
    ];
    header('Location: ../dashboard/admin_categories.php');
    exit;
}

$imgStmt = $conn->prepare("SELECT image FROM categories WHERE id = ? LIMIT 1");
$imgStmt->bind_param('i', $categoryId);
$imgStmt->execute();
$image = $imgStmt->get_result()->fetch_assoc()['image'] ?? null;
$imgStmt->close();

$delStmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
$delStmt->bind_param('i', $categoryId);
$delStmt->execute();
$delStmt->close();

delete_stored_image($image, __DIR__ . '/../categories');

$_SESSION['category_success'] = 'Category deleted.';
header('Location: ../dashboard/admin_categories.php');
exit;
