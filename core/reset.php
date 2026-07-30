<?php

session_start();
include_once "db_connection.php";

if (isset($_POST['reset'])) {

    $email = $_POST['email'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);


    if (mysqli_num_rows($result) > 0) {

        $row = mysqli_fetch_assoc($result);
        $id = $row["id"];
        $email = $row["email"];
        $password = $row["password"];

        // SEND EMAIL
        $from = SUPPORT_EMAIL;
        $to = $email;
        $subject = "Password reset";
        $message = "Hello, $email. Your password is $password";
        $headers = "From:" . $from;
        mail($to, $subject, $message, $headers);

        $_SESSION["reset_success"] = "Password sent to your email";
        header("Location: ../reset");
    } else {

        $_SESSION["reset_error"] = "Invalid email";
        $_SESSION["reset_email"] = $email;

        header("Location: ../reset");
    }
}
