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

// ---------- Merge guest-session cart into the DB cart ----------
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $prodId => $qty) {
        $prodId = (int) $prodId;
        $qty    = (int) $qty;
        if ($prodId <= 0 || $qty < 1) {
            continue;
        }

        $existStmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ? LIMIT 1");
        $existStmt->bind_param('ii', $user['id'], $prodId);
        $existStmt->execute();
        $existingItem = $existStmt->get_result()->fetch_assoc();
        $existStmt->close();

        if ($existingItem) {
            $newQty = $existingItem['quantity'] + $qty;
            $updateStmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $updateStmt->bind_param('ii', $newQty, $existingItem['id']);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            $insertStmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $insertStmt->bind_param('iii', $user['id'], $prodId, $qty);
            $insertStmt->execute();
            $insertStmt->close();
        }
    }
    unset($_SESSION['cart']);
}

if ($remember) {
    // 30-day cookie carrying the user id — fine for a demo, but for
    // production this should be a random, DB-stored remember-me token,
    // never the raw user id.
    setcookie('remember_user', (string) $user['id'], time() + (30 * 24 * 60 * 60), '/');
}

header('Location: ../dashboard/index.php');
exit;
