<?php
session_start();
require 'cart_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['address'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $cartItems = getCartItems();

        header("Location: ../cart_checkoutSuccess");
    } else {
        header("Location: ../cart_checkoutFailed");
    }
} else {
    header("Location: ../cart_checkoutFailed");
    exit();
}
