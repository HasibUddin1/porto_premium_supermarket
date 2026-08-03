<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!doctype html>
<html lang="en">

<head>


    <?php
    $pageInfo = [
        "title" => "Porto Premium Supermarket - Account",
    ];
    ?>

    <?php include_once "includes/head.php"; ?>

</head>

<body>


    <div class="main_page">
        <?php include_once "includes/nav.php"; ?>


        <?php include_once "includes/footer.php"; ?>


        <?php include_once "includes/scripts.php"; ?>
    </div>

</body>

</html>