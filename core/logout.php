<?php
// core/logout.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session data
$_SESSION = [];

// Remove the session cookie itself (if the browser is using one)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Clear the "remember me" cookie too, if it was set
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 42000, '/');
}

session_destroy();

header('Location: ../login.php');
exit;
