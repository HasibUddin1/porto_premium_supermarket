<?php
// core/login.php
// Handles login (POST from login.php's "Login Now" form)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connection.php'; // exposes $conn

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

if ($email === '' || $password === '') {
    $_SESSION['login_errors'] = ['Email and password are required.'];
    header('Location: ../login.php');
    exit;
}

$stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// TEMPORARY: plain-text comparison, per explicit client-demo request.
// Once registration hashes passwords with password_hash(), replace the
// check below with: password_verify($password, $user['password'])
if (!$user || $password !== $user['password']) {
    $_SESSION['login_errors'] = ['Incorrect email or password.'];
    header('Location: ../login.php');
    exit;
}

$_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role']  = $user['role']; // 'user' or 'admin'

if ($remember) {
    // 30-day cookie carrying the user id — fine for a demo, but for
    // production this should be a random, DB-stored remember-me token,
    // never the raw user id.
    setcookie('remember_user', (string) $user['id'], time() + (30 * 24 * 60 * 60), '/');
}

header('Location: ../dashboard/index.php');
exit;
