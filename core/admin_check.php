<?php
// core/admin_check.php
// Include at the top of any admin-only page:
//   require_once __DIR__ . '/../core/admin_check.php';
// Ensures the visitor is logged in AND has the admin role.

require_once __DIR__ . '/login_check.php'; // handles the logged-in check, sets $currentUserId etc.

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: index.php'); // send non-admins back to their own dashboard
    exit;
}
