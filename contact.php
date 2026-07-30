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
        "title" => "Contact",
    ];
    ?>

    <?php include_once "includes/head.php"; ?>

</head>

<body>

    <div class="main_page">
        <?php include_once "includes/nav.php"; ?>

        <?php include_once "includes/contact_breadcrumb.php"; ?>

        <?php include_once "includes/contactForm.php"; ?>

        <?php include_once "includes/footer.php"; ?>

        <?php include_once "includes/scripts.php"; ?>


        <!-- To Remove Query Paramter on Page refresh -->
        <script>
            function removeQueryParams() {
                var newUrl = window.location.pathname;
                history.replaceState({}, '', newUrl);
            }
            window.onload = removeQueryParams;
        </script>
    </div>
</body>

</html>