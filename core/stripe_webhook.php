<?php
// core/stripe_webhook.php
// Stripe's servers POST here directly (NOT through the customer's browser),
// so there is no PHP session available — everything needed must come from
// the database, keyed by the order_id stored in the session's metadata.
//
// Register this URL in Stripe Dashboard → Developers → Webhooks:
//   https://yourdomain.com/core/stripe_webhook.php
// Listening for event: checkout.session.completed

require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

$payload    = @file_get_contents('php://input');
$sigHeader  = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, STRIPE_WEBHOOK_SECRET);
} catch (\Exception $e) {
    http_response_code(400);
    exit;
}

if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    $orderId = (int) ($session->metadata->order_id ?? 0);

    if ($orderId > 0 && $session->payment_status === 'paid') {
        $orderStmt = $conn->prepare("SELECT user_id, payment_status FROM orders WHERE id = ? LIMIT 1");
        $orderStmt->bind_param('i', $orderId);
        $orderStmt->execute();
        $order = $orderStmt->get_result()->fetch_assoc();
        $orderStmt->close();

        // Idempotency check — a webhook can be delivered more than once
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
    }
}

http_response_code(200);
