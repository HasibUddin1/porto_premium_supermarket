<?php
// core/admin_update_role.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connection.php';

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard/admin_users.php');
    exit;
}

$targetUserId = (int) ($_POST['user_id'] ?? 0);
$newRole      = $_POST['role'] ?? '';

if (!in_array($newRole, ['user', 'admin'], true) || $targetUserId <= 0) {
    $_SESSION['admin_users_errors'] = ['Invalid request.'];
    header('Location: ../dashboard/admin_users.php');
    exit;
}

// Prevent an admin from demoting themselves and getting locked out
if ($targetUserId === (int) $_SESSION['user_id'] && $newRole !== 'admin') {
    $_SESSION['admin_users_errors'] = ['You cannot change your own role.'];
    header('Location: ../dashboard/admin_users.php');
    exit;
}

$stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->bind_param('si', $newRole, $targetUserId);
$stmt->execute();
$stmt->close();

$_SESSION['admin_users_success'] = 'User role updated.';
header('Location: ../dashboard/admin_users.php');
exit;
