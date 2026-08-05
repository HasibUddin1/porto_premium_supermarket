<?php
// core/stripe_success.php
// Stripe redirects the customer here after a successful Checkout Session.
// This is a convenience confirmation for the customer's browser — the
// WEBHOOK (stripe_webhook.php) is the authoritative source of truth for
// whether payment actually succeeded, in case the customer closes the tab
// before this page loads.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

$orderId   = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;
$sessionId = $_GET['session_id'] ?? '';

if ($orderId <= 0 || $sessionId === '') {
    header('Location: ../checkout.php');
    exit;
}

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

try {
    $session = \Stripe\Checkout\Session::retrieve($sessionId);
} catch (\Exception $e) {
    header('Location: ../checkout.php');
    exit;
}

// Confirm this session really belongs to this order and was actually paid
if ((int) ($session->metadata->order_id ?? 0) !== $orderId || $session->payment_status !== 'paid') {
    header('Location: ../checkout.php');
    exit;
}

// Fetch the order to get its user_id (for clearing that user's cart)
$orderStmt = $conn->prepare("SELECT user_id, payment_status FROM orders WHERE id = ? LIMIT 1");
$orderStmt->bind_param('i', $orderId);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();
$orderStmt->close();

if ($order && $order['payment_status'] !== 'paid') {
    $updateStmt = $conn->prepare("
        UPDATE orders
        SET payment_status = 'paid', status = 'processing', stripe_payment_intent = ?
        WHERE id = ?
    ");
    $paymentIntentId = $session->payment_intent;
    $updateStmt->bind_param('si', $paymentIntentId, $orderId);
    $updateStmt->execute();
    $updateStmt->close();

    $clearStmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $clearStmt->bind_param('i', $order['user_id']);
    $clearStmt->execute();
    $clearStmt->close();
}

header('Location: ../order_success.php?order_id=' . $orderId);
exit;
