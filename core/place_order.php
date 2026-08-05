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

$allowedPaymentMethods = ['bank_transfer', 'cheque', 'stripe', 'paypal'];
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

    // Clear the cart now — UNLESS this is a Stripe order, where we wait
    // until the payment actually succeeds (webhook / stripe_success.php)
    // before clearing it, so an abandoned/failed Stripe checkout doesn't
    // silently empty the customer's cart.
    if ($fields['payment_method'] !== 'stripe') {
        $clearStmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $clearStmt->bind_param('i', $userId);
        $clearStmt->execute();
        $clearStmt->close();
    }

    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    $_SESSION['checkout_errors'] = ['Something went wrong while placing your order. Please try again.'];
    $_SESSION['checkout_old']    = $fields;
    header('Location: ../checkout.php');
    exit;
}

// ---------- Stripe: create a Checkout Session and redirect to it ----------
if ($fields['payment_method'] === 'stripe') {
    require_once __DIR__ . '/../config/config.php';       // STRIPE_SECRET_KEY etc.
    require_once __DIR__ . '/../vendor/autoload.php';     // composer autoload

    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    $lineItems = [];
    foreach ($cart['items'] as $item) {
        $lineItems[] = [
            'price_data' => [
                'currency'     => 'eur', // change if your store charges in a different currency
                'product_data' => [
                    'name' => $item['name'],
                ],
                'unit_amount' => (int) round($item['price'] * 100), // Stripe wants the smallest currency unit (cents)
            ],
            'quantity' => (int) $item['quantity'],
        ];
    }

    // Figure out the site's base URL so success/cancel links work wherever this is hosted
    $scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']); // e.g. /Porto Preimum Supermarket/core
    $baseDir   = dirname($scriptDir);              // e.g. /Porto Preimum Supermarket
    $baseUrl   = $scheme . '://' . $_SERVER['HTTP_HOST'] . str_replace(' ', '%20', $baseDir);

    try {
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => $lineItems,
            'mode'                 => 'payment',
            'customer_email'       => $fields['email'],
            'success_url'          => $baseUrl . '/core/stripe_success.php?order_id=' . $orderId . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => $baseUrl . '/checkout.php',
            'metadata'             => [
                'order_id' => $orderId,
            ],
        ]);

        $saveSessionStmt = $conn->prepare("UPDATE orders SET stripe_session_id = ? WHERE id = ?");
        $saveSessionStmt->bind_param('si', $session->id, $orderId);
        $saveSessionStmt->execute();
        $saveSessionStmt->close();

        header('Location: ' . $session->url);
        exit;
    } catch (\Exception $e) {
        $_SESSION['checkout_errors'] = ['Could not start the Stripe checkout: ' . $e->getMessage()]; // TEMP: shows real error, revert after debugging
        header('Location: ../checkout.php');
        exit;
    }
}

header('Location: ../order_success.php?order_id=' . $orderId);
exit;
