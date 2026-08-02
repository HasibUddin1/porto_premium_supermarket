<?php
// core/place_order.php
// Processes the checkout form: validates input, saves the order + its
// items, clears the user's cart, then sends them to a confirmation page.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connection.php'; // exposes $conn
require_once __DIR__ . '/cart_helper.php';   // exposes get_cart_summary()

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../checkout.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$cart   = get_cart_summary($conn);

if (empty($cart['items'])) {
    header('Location: ../cart.php');
    exit;
}

// ---------- Collect + validate input ----------
$fields = [
    'billing_country'    => trim($_POST['billing_country'] ?? ''),
    'billing_first_name' => trim($_POST['billing_first_name'] ?? ''),
    'billing_last_name'  => trim($_POST['billing_last_name'] ?? ''),
    'billing_address1'   => trim($_POST['billing_address1'] ?? ''),
    'billing_address2'   => trim($_POST['billing_address2'] ?? ''),
    'billing_city'       => trim($_POST['billing_city'] ?? ''),
    'email'              => trim($_POST['email'] ?? ''),
    'phone'              => trim($_POST['phone'] ?? ''),
    'ship_different'     => isset($_POST['ship_different']) ? '1' : '',
    'shipping_country'   => trim($_POST['shipping_country'] ?? ''),
    'shipping_first_name' => trim($_POST['shipping_first_name'] ?? ''),
    'shipping_last_name' => trim($_POST['shipping_last_name'] ?? ''),
    'shipping_address1'  => trim($_POST['shipping_address1'] ?? ''),
    'shipping_address2'  => trim($_POST['shipping_address2'] ?? ''),
    'shipping_city'      => trim($_POST['shipping_city'] ?? ''),
    'shipping_notes'     => trim($_POST['shipping_notes'] ?? ''),
    'payment_method'     => trim($_POST['payment_method'] ?? ''),
];

$errors = [];

if ($fields['billing_country'] === '')    $errors[] = 'Billing country is required.';
if ($fields['billing_first_name'] === '') $errors[] = 'Billing first name is required.';
if ($fields['billing_last_name'] === '')  $errors[] = 'Billing last name is required.';
if ($fields['billing_address1'] === '')   $errors[] = 'Billing address is required.';
if ($fields['billing_city'] === '')       $errors[] = 'Billing town/city is required.';
if ($fields['email'] === '' || !filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
if ($fields['phone'] === '') $errors[] = 'Phone number is required.';

$allowedPaymentMethods = ['bank_transfer', 'cheque', 'credit_card', 'paypal'];
if (!in_array($fields['payment_method'], $allowedPaymentMethods, true)) {
    $errors[] = 'Please choose a payment method.';
}

if ($fields['ship_different'] === '1') {
    if ($fields['shipping_country'] === '')    $errors[] = 'Shipping country is required.';
    if ($fields['shipping_first_name'] === '') $errors[] = 'Shipping first name is required.';
    if ($fields['shipping_last_name'] === '')  $errors[] = 'Shipping last name is required.';
    if ($fields['shipping_address1'] === '')   $errors[] = 'Shipping address is required.';
    if ($fields['shipping_city'] === '')       $errors[] = 'Shipping town/city is required.';
}

if (!empty($errors)) {
    $_SESSION['checkout_errors'] = $errors;
    $_SESSION['checkout_old']    = $fields;
    header('Location: ../checkout.php');
    exit;
}

// ---------- Build address strings ----------
$customerName = trim($fields['billing_first_name'] . ' ' . $fields['billing_last_name']);

$billingAddress = trim(implode(', ', array_filter([
    $fields['billing_address1'],
    $fields['billing_address2'],
    $fields['billing_city'],
    $fields['billing_country'],
])));

if ($fields['ship_different'] === '1') {
    $shippingName = trim($fields['shipping_first_name'] . ' ' . $fields['shipping_last_name']);
    $shippingAddress = trim(implode(', ', array_filter([
        $shippingName,
        $fields['shipping_address1'],
        $fields['shipping_address2'],
        $fields['shipping_city'],
        $fields['shipping_country'],
        $fields['shipping_notes'],
    ])));
} else {
    // Same as billing
    $shippingAddress = $billingAddress;
}

// ---------- Save the order ----------
$conn->begin_transaction();

try {
    $orderStmt = $conn->prepare("
        INSERT INTO orders
            (user_id, customer_name, email, phone, billing_address, shipping_address, payment_method, total_amount, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    $orderStmt->bind_param(
        'issssssd',
        $userId,
        $customerName,
        $fields['email'],
        $fields['phone'],
        $billingAddress,
        $shippingAddress,
        $fields['payment_method'],
        $cart['total']
    );
    $orderStmt->execute();
    $orderId = $conn->insert_id;
    $orderStmt->close();

    $itemStmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");
    foreach ($cart['items'] as $item) {
        $productId = (int) $item['id'];
        $quantity  = (int) $item['quantity'];
        $price     = (float) $item['price'];
        $itemStmt->bind_param('iiid', $orderId, $productId, $quantity, $price);
        $itemStmt->execute();
    }
    $itemStmt->close();

    // Clear the cart now that the order is placed
    $clearStmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $clearStmt->bind_param('i', $userId);
    $clearStmt->execute();
    $clearStmt->close();

    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    $_SESSION['checkout_errors'] = ['Something went wrong while placing your order. Please try again.'];
    $_SESSION['checkout_old']    = $fields;
    header('Location: ../checkout.php');
    exit;
}

header('Location: ../order_success.php?order_id=' . $orderId);
exit;
