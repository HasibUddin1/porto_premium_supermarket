<?php

include_once "../config/config.php";
include_once "functions.php";


if ($_POST) {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Check for empty fields
    if (empty($name) || empty($email) || empty($message)) {
        echo "Please fill in all fields.";
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Please enter a valid email address.";
        exit();
    }

    if (SMTP) {
        $mailSent = sendSMTPMail($name, $email, $message);
        if ($mailSent) {
            header("Location: ../contact.php?status=true");
            exit();
        } else {
            header("Location: ../contact.php?status=false");
            exit();
        }
    } else {
        $mailSent = sendMail($name, $email, $message);
        if ($mailSent) {
            header("Location: ../contact.php?status=true");
            exit();
        } else {
            header("Location: ../contact.php?status=false");
            exit();
        }
    }
}
