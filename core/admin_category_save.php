<?php
// core/admin_category_save.php

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

$name = trim($_POST['name'] ?? '');
$errors = [];

if ($name === '') {
    $errors[] = 'Category name is required.';
}

$imageError = null;
$destinationDir = __DIR__ . '/../categories';
$filename = save_uploaded_image_as_webp($_FILES['category_image'] ?? [], $destinationDir, $imageError);

if ($imageError) {
    $errors[] = $imageError;
}
if (!$filename && !$imageError) {
    $errors[] = 'A category image is required.';
}

if (!empty($errors)) {
    if (!empty($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }
    $_SESSION['category_errors'] = $errors;
    header('Location: ../dashboard/admin_categories.php');
    exit;
}

$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-'));

$stmt = $conn->prepare("INSERT INTO categories (name, slug, image) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $name, $slug, $filename);
$stmt->execute();
$newCategoryId = $conn->insert_id;
$stmt->close();

// AJAX call from the product form's inline "add category" panel
if (!empty($_POST['ajax'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'id' => $newCategoryId, 'name' => $name]);
    exit;
}

$_SESSION['category_success'] = 'Category added.';
header('Location: ../dashboard/admin_categories.php');
exit;
