<?php
// core/register.php
// Handles new user registration (POST from login.php's "Register Here" form)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connection.php'; // exposes $conn

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$name            = trim($_POST['name'] ?? '');
$email           = trim($_POST['email'] ?? '');
$password        = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$phone           = trim($_POST['phone'] ?? '');
$location        = trim($_POST['location'] ?? '');
$terms           = isset($_POST['terms']);

// ---------- Validation ----------
$errors = [];

if ($name === '') {
    $errors[] = 'Name is required.';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
if ($password === '' || strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters.';
}
if ($password !== $confirmPassword) {
    $errors[] = 'Passwords do not match.';
}
if ($phone === '') {
    $errors[] = 'Phone number is required.';
}
if (!$terms) {
    $errors[] = 'You must agree to the terms & conditions.';
}

// duplicate email check
if (empty($errors)) {
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $checkStmt->bind_param('s', $email);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows > 0) {
        $errors[] = 'An account with this email already exists.';
    }
    $checkStmt->close();
}

if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_old'] = [
        'name'     => $name,
        'email'    => $email,
        'phone'    => $phone,
        'location' => $location,
    ];
    header('Location: ../login.php');
    exit;
}

// ---------- Insert ----------
// TEMPORARY: password is stored in PLAIN TEXT, per explicit client-demo
// request. Before this goes live, switch to:
//   $password = password_hash($password, PASSWORD_DEFAULT);
// and verify with password_verify() in core/login.php.
$insertStmt = $conn->prepare("
    INSERT INTO users (name, email, password, phone, location)
    VALUES (?, ?, ?, ?, ?)
");
$insertStmt->bind_param('sssss', $name, $email, $password, $phone, $location);

if ($insertStmt->execute()) {
    $_SESSION['register_success'] = 'Account created successfully. You can log in now.';
} else {
    $_SESSION['register_errors'] = ['Something went wrong. Please try again.'];
}
$insertStmt->close();

header('Location: ../login.php');
exit;
