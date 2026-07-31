<?php
// core/login_check.php
// Include this at the top of any page that should only be visible to
// logged-in users, e.g.:
//   require_once __DIR__ . '/core/login_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Convenience variables for pages that include this file
$currentUserId    = $_SESSION['user_id'];
$currentUserName  = $_SESSION['user_name']  ?? '';
$currentUserEmail = $_SESSION['user_email'] ?? '';
