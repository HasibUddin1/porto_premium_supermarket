<?php 

// Path: core\login.php
session_start();
include_once "db_connection.php";

if(isset($_POST["login"])){
    $email=$_POST["email"];
    $password=$_POST["password"];

    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        $_SESSION["user_id"]= $row["id"];
        $_SESSION["user_email"]= $row["email"];

        header("Location: ../dashboard/index");
    }else{
        $_SESSION["login_error"] = "Invalid email or password";
        $_SESSION["login_email"] = $email;
        header("Location: ../login");
    }
}
