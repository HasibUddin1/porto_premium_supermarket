<?php
// core/admin_order_delivered.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connection.php';

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard/admin_orders.php');
    exit;
}

$orderId = (int) ($_POST['order_id'] ?? 0);

if ($orderId > 0) {
    $stmt = $conn->prepare("UPDATE orders SET delivered_at = NOW(), status = 'completed' WHERE id = ?");
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
    $stmt->close();

    $_SESSION['admin_orders_success'] = 'Order #' . $orderId . ' marked as delivered.';
}

header('Location: ../dashboard/admin_orders.php');
exit;
