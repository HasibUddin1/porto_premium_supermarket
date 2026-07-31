<?php
// core/cart_remove.php
// AJAX endpoint — removes one product from the cart. Always returns JSON.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once __DIR__ . '/db_connection.php'; // exposes $conn
require_once __DIR__ . '/cart_helper.php';   // reuses get_cart_summary()

function respond($success, $message, $extra = [])
{
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Invalid request method.');
}

$productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
if ($productId <= 0) {
    respond(false, 'Invalid product.');
}

if (isset($_SESSION['user_id'])) {
    // ---------- Logged-in user: delete from cart_items table ----------
    $userId = (int) $_SESSION['user_id'];
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param('ii', $userId, $productId);
    $stmt->execute();
    $stmt->close();
} else {
    // ---------- Guest: remove from session cart ----------
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
}

$cart = get_cart_summary($conn);

respond(true, 'Item removed from cart.', [
    'cart_count' => $cart['count'],
    'cart_total' => number_format($cart['total'], 2),
]);
