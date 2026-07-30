<?php
session_start();
// Path: core\register.php

include_once "db_connection.php";
include_once "functions.php";

if (isset($_POST["register"])) {

    $email = $_POST["email"];
    $password = $_POST["password"];


    // check if email already exists

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);



    if (mysqli_num_rows($result) > 0) {

        $_SESSION["register_error"] = "Email already exists";
        $_SESSION["register_email"] = $email;

        header("Location: ../register");
    } else {
        $sql = "INSERT INTO users (email, password) VALUES ('$email', '$password')";

        if (mysqli_query($conn, $sql)) {
            $_SESSION["user_id"] = $conn->insert_id;
            $_SESSION["user_email"] = $email;




            // SEND EMAIL
            $to = $email;
            $subject = "Welcome to our website";
            $message = "Hello, $email. Welcome to our website";

            sendMail($to, $subject, $message);
           

            header("Location: ../dashboard");
        } else {
            $_SESSION["register_error"] = "Something went wrong " . mysqli_error($conn);
            header("Location: ../register");
        }
    }
}
