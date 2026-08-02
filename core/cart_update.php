<?php
// core/cart_update.php
// Handles the cart page's "Update Cart" submit: updates quantities and
// removes any rows whose "Remove" checkbox was ticked.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connection.php'; // exposes $conn

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../cart.php');
    exit;
}

$quantities = $_POST['quantity'] ?? [];   // [product_id => qty]
$removeIds  = $_POST['remove'] ?? [];     // [product_id, product_id, ...]
$removeIds  = array_map('intval', $removeIds);

if (isset($_SESSION['user_id'])) {
    // ---------- Logged-in user: cart_items table ----------
    $userId = (int) $_SESSION['user_id'];

    foreach ($quantities as $productId => $qty) {
        $productId = (int) $productId;
        $qty       = (int) $qty;

        if ($productId <= 0) {
            continue;
        }

        if (in_array($productId, $removeIds, true) || $qty <= 0) {
            $delStmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
            $delStmt->bind_param('ii', $userId, $productId);
            $delStmt->execute();
            $delStmt->close();
            continue;
        }

        $updStmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $updStmt->bind_param('iii', $qty, $userId, $productId);
        $updStmt->execute();
        $updStmt->close();
    }
} else {
    // ---------- Guest: session cart ----------
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    foreach ($quantities as $productId => $qty) {
        $productId = (int) $productId;
        $qty       = (int) $qty;

        if ($productId <= 0) {
            continue;
        }

        if (in_array($productId, $removeIds, true) || $qty <= 0) {
            unset($_SESSION['cart'][$productId]);
            continue;
        }

        $_SESSION['cart'][$productId] = $qty;
    }
}

header('Location: ../cart.php');
exit;
