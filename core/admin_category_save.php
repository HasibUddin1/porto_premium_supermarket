<?php
// core/admin_category_save.php
// Handles both adding a new category and updating an existing one
// (category_id present + >0 => update, otherwise => insert).

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
$isEdit     = $categoryId > 0;

$name = trim($_POST['name'] ?? '');
$errors = [];

if ($name === '') {
    $errors[] = 'Category name is required.';
}

$imageError = null;
$destinationDir = __DIR__ . '/../categories';
$newImagePath = save_uploaded_image_as_webp($_FILES['category_image'] ?? [], $destinationDir, $imageError);

if ($imageError) {
    $errors[] = $imageError;
}
if (!$isEdit && !$newImagePath && !$imageError) {
    $errors[] = 'A category image is required.';
}

if (!empty($errors)) {
    if (!empty($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }
    $_SESSION['category_errors'] = $errors;
    header('Location: ../dashboard/admin_categories.php' . ($isEdit ? '?edit=' . $categoryId : ''));
    exit;
}

$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-'));

if ($isEdit) {
    if ($newImagePath) {
        $oldStmt = $conn->prepare("SELECT image FROM categories WHERE id = ? LIMIT 1");
        $oldStmt->bind_param('i', $categoryId);
        $oldStmt->execute();
        $oldImage = $oldStmt->get_result()->fetch_assoc()['image'] ?? null;
        $oldStmt->close();

        // delete_stored_image() expects the base dir + basename, and the
        // stored value may include the "categories/" prefix — basename()
        // inside that helper strips it safely either way.
        delete_stored_image($oldImage, $destinationDir);

        $stmt = $conn->prepare("UPDATE categories SET name = ?, slug = ?, image = ? WHERE id = ?");
        $stmt->bind_param('sssi', $name, $slug, $newImagePath, $categoryId);
    } else {
        $stmt = $conn->prepare("UPDATE categories SET name = ?, slug = ? WHERE id = ?");
        $stmt->bind_param('ssi', $name, $slug, $categoryId);
    }
    $stmt->execute();
    $stmt->close();

    $_SESSION['category_success'] = 'Category updated.';
    header('Location: ../dashboard/admin_categories.php');
    exit;
}

// ---------- Insert (new category) ----------
$stmt = $conn->prepare("INSERT INTO categories (name, slug, image) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $name, $slug, $newImagePath);
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