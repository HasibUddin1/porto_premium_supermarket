<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

function addToCart($id, $name, $price, $billing)
{
    $_SESSION['cart'] = array();
    $_SESSION['cart']['id'] = $id;
    $_SESSION['cart']['name'] = $name;
    $_SESSION['cart']['price'] = $price;
    $_SESSION['cart']['billing'] = $billing;
}

function removeFromCart()
{
    $_SESSION['cart'] = array();
}

function getCartItems()
{
    return $_SESSION['cart'];
}

function emptyCart()
{
    $_SESSION['cart'] = array();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $cart_action = $_POST['action'];
    $cart_id = $_POST['id'];
    $cart_name = $_POST['name'];
    $cart_price = $_POST['price'];
    $cart_billing = $_POST['billing'];

    if ($cart_action == 'add') {
        addToCart($cart_id, $cart_name, $cart_price, $cart_billing);
    } elseif ($cart_action == 'remove') {
        removeFromCart();
    }

    header("Location: ../cart.php");
    exit();
}
