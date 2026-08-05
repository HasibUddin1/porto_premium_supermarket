<?php
// core/update_profile.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/image_helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard/profile.php');
    exit;
}

$userId   = (int) $_SESSION['user_id'];
$name     = trim($_POST['name'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$location = trim($_POST['location'] ?? '');

$errors = [];
if ($name === '')  $errors[] = 'Name is required.';
if ($phone === '') $errors[] = 'Phone number is required.';

if (!empty($errors)) {
    $_SESSION['profile_errors'] = $errors;
    header('Location: ../dashboard/profile.php');
    exit;
}

// ---------- Profile image (optional) ----------
$imageError = null;
$destinationDir = __DIR__ . '/../users';
$newFilename = save_uploaded_image_as_webp($_FILES['profile_image'] ?? [], $destinationDir, $imageError);

if ($imageError) {
    $_SESSION['profile_errors'] = [$imageError];
    header('Location: ../dashboard/profile.php');
    exit;
}

if ($newFilename) {
    // Delete the old profile image, then update the DB with the new one
    $oldStmt = $conn->prepare("SELECT image FROM users WHERE id = ? LIMIT 1");
    $oldStmt->bind_param('i', $userId);
    $oldStmt->execute();
    $oldImage = $oldStmt->get_result()->fetch_assoc()['image'] ?? null;
    $oldStmt->close();

    delete_stored_image($oldImage, $destinationDir);

    $updateStmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, location = ?, image = ? WHERE id = ?");
    $updateStmt->bind_param('ssssi', $name, $phone, $location, $newFilename, $userId);
} else {
    $updateStmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, location = ? WHERE id = ?");
    $updateStmt->bind_param('sssi', $name, $phone, $location, $userId);
}

$updateStmt->execute();
$updateStmt->close();

// Keep the session name in sync (used across the site, e.g. dashboard greeting)
$_SESSION['user_name'] = $name;

$_SESSION['profile_success'] = 'Your account has been updated.';
header('Location: ../dashboard/profile.php');
exit;
