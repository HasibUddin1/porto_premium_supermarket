<?php
// core/cart_helper.php
// Returns the current visitor's cart (guest session or logged-in DB) as a
// unified array, plus item count and cart total.
//
// Usage (from includes/nav.php or anywhere the cart dropdown is shown):
//   require_once __DIR__ . '/../core/db_connection.php';
//   require_once __DIR__ . '/../core/cart_helper.php';
//   $cart = get_cart_summary($conn);
//   // $cart['items'] (array), $cart['count'] (int), $cart['total'] (float)

function get_cart_summary($conn)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $items = [];
    $count = 0;
    $total = 0.0;

    if (isset($_SESSION['user_id'])) {
        // ---------- Logged-in user: cart_items table ----------
        $userId = (int) $_SESSION['user_id'];
        $stmt = $conn->prepare("
            SELECT p.id, p.name, p.image, p.price, ci.quantity
            FROM cart_items ci
            INNER JOIN products p ON p.id = ci.product_id
            WHERE ci.user_id = ?
            ORDER BY ci.created_at DESC
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $row['subtotal'] = $row['price'] * $row['quantity'];
            $items[] = $row;
            $count += $row['quantity'];
            $total += $row['subtotal'];
        }
        $stmt->close();
    } elseif (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        // ---------- Guest: PHP session cart ----------
        $productIds   = array_map('intval', array_keys($_SESSION['cart']));
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $types        = str_repeat('i', count($productIds));

        $stmt = $conn->prepare("SELECT id, name, image, price FROM products WHERE id IN ($placeholders)");
        $stmt->bind_param($types, ...$productIds);
        $stmt->execute();
        $result = $stmt->get_result();

        $productsById = [];
        while ($row = $result->fetch_assoc()) {
            $productsById[$row['id']] = $row;
        }
        $stmt->close();

        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $productId = (int) $productId;
            if (!isset($productsById[$productId])) {
                continue; // product may have been removed since it was added
            }
            $product  = $productsById[$productId];
            $subtotal = $product['price'] * $quantity;

            $items[] = [
                'id'       => $product['id'],
                'name'     => $product['name'],
                'image'    => $product['image'],
                'price'    => $product['price'],
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
            $count += $quantity;
            $total += $subtotal;
        }
    }

    return [
        'items' => $items,
        'count' => $count,
        'total' => $total,
    ];
}

function e_cart($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
