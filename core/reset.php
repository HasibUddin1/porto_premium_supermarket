<?php
// core/reset.php
// Handles password reset (POST from reset.php's form)
//
// NOTE: this is a SIMPLIFIED reset flow with no email/token verification —
// anyone who knows an account's email can change its password. That's fine
// to show a client quickly, but before going live this should become a
// proper token-based flow: email a one-time reset link, verify the token,
// then let the user set a new password.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connection.php'; // exposes $conn

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../reset.php');
    exit;
}

$email           = trim($_POST['email'] ?? '');
$newPassword     = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

$errors = [];

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
if ($newPassword === '' || strlen($newPassword) < 6) {
    $errors[] = 'Password must be at least 6 characters.';
}
if ($newPassword !== $confirmPassword) {
    $errors[] = 'Passwords do not match.';
}

if (empty($errors)) {
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $checkStmt->bind_param('s', $email);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows === 0) {
        $errors[] = 'No account found with that email.';
    }
    $checkStmt->close();
}

if (!empty($errors)) {
    $_SESSION['reset_errors'] = $errors;
    header('Location: ../reset.php');
    exit;
}

// TEMPORARY: plain-text update, per explicit client-demo request.
// Switch to: $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
// before going live.
$updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$updateStmt->bind_param('ss', $newPassword, $email);
$updateStmt->execute();
$updateStmt->close();

$_SESSION['reset_success'] = 'Password updated successfully. You can log in now.';
header('Location: ../login.php');
exit;
