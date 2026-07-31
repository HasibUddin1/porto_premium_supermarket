<?php
// core/cart_add.php
// AJAX endpoint — always returns JSON, never redirects.
// Guests: cart stored in $_SESSION['cart'] as [product_id => quantity].
// Logged-in users: cart stored in the cart_items DB table.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once __DIR__ . '/db_connection.php'; // exposes $conn

function respond($success, $message, $extra = [])
{
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Invalid request method.');
}

$productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$quantity  = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

if ($productId <= 0) {
    respond(false, 'Invalid product.');
}
if ($quantity < 1) {
    $quantity = 1;
}

// confirm the product actually exists
$checkStmt = $conn->prepare("SELECT id FROM products WHERE id = ? LIMIT 1");
$checkStmt->bind_param('i', $productId);
$checkStmt->execute();
$checkStmt->store_result();
if ($checkStmt->num_rows === 0) {
    $checkStmt->close();
    respond(false, 'Product not found.');
}
$checkStmt->close();

if (isset($_SESSION['user_id'])) {
    // ---------- Logged-in user: cart_items table ----------
    $userId = (int) $_SESSION['user_id'];

    $existStmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ? LIMIT 1");
    $existStmt->bind_param('ii', $userId, $productId);
    $existStmt->execute();
    $existing = $existStmt->get_result()->fetch_assoc();
    $existStmt->close();

    if ($existing) {
        $newQty = $existing['quantity'] + $quantity;
        $updateStmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $updateStmt->bind_param('ii', $newQty, $existing['id']);
        $updateStmt->execute();
        $updateStmt->close();
    } else {
        $insertStmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insertStmt->bind_param('iii', $userId, $productId, $quantity);
        $insertStmt->execute();
        $insertStmt->close();
    }

    $countStmt = $conn->prepare("SELECT COALESCE(SUM(quantity), 0) AS total FROM cart_items WHERE user_id = ?");
    $countStmt->bind_param('i', $userId);
    $countStmt->execute();
    $cartCount = (int) $countStmt->get_result()->fetch_assoc()['total'];
    $countStmt->close();
} else {
    // ---------- Guest: PHP session cart ----------
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }

    $cartCount = array_sum($_SESSION['cart']);
}

respond(true, 'Product added to cart.', ['cart_count' => $cartCount]);
