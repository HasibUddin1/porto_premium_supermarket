<?php
// core/admin_delete_user.php

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

if ($targetUserId <= 0) {
    $_SESSION['admin_users_errors'] = ['Invalid request.'];
    header('Location: ../dashboard/admin_users.php');
    exit;
}

if ($targetUserId === (int) $_SESSION['user_id']) {
    $_SESSION['admin_users_errors'] = ['You cannot delete your own account.'];
    header('Location: ../dashboard/admin_users.php');
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param('i', $targetUserId);
$stmt->execute();
$stmt->close();

$_SESSION['admin_users_success'] = 'User deleted.';
header('Location: ../dashboard/admin_users.php');
exit;
