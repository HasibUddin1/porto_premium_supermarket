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
        "title" => "Porto Premium Supermarket - Our Store",
    ];
    ?>

    <?php include_once "includes/head.php"; ?>

</head>

<body>

    <div class="main_page">
        <?php include_once "includes/nav.php"; ?>

        <?php include_once "includes/shop_page.php"; ?>

        <?php include_once "includes/footer.php"; ?>


        <?php include_once "includes/scripts.php"; ?>

        <!-- Custom Scripts -->
        <!-- Product Price Filter -->
        <script>
            $(function() {

                let minDefault = <?php echo $dbMinPrice; ?>;
                let maxDefault = <?php echo $dbMaxPrice; ?>;


                $("#slider-range").slider({

                    range: true,

                    min: minDefault,

                    max: maxDefault,

                    values: [
                        <?php echo $minPrice; ?>,
                        <?php echo $maxPrice; ?>
                    ],

                    slide: function(event, ui) {

                        $(".min").val("$" + ui.values[0]);

                        $(".max").val("$" + ui.values[1]);

                        $("#min_price").val(ui.values[0]);

                        $("#max_price").val(ui.values[1]);

                    }

                });


                $(".min").val("$" + $("#slider-range").slider("values", 0));

                $(".max").val("$" + $("#slider-range").slider("values", 1));


                // Submit filter
                $(".price-ranger form").on("submit", function() {

                    $("#min_price").val(
                        $("#slider-range").slider("values", 0)
                    );

                    $("#max_price").val(
                        $("#slider-range").slider("values", 1)
                    );

                });


                // Reset Filter
                $("#reset-filter").on("click", function() {


                    // Slider default e niye jabe
                    $("#slider-range").slider("values", [
                        minDefault,
                        maxDefault
                    ]);


                    // Text update
                    $(".min").val("$" + minDefault);
                    $(".max").val("$" + maxDefault);


                    // Hidden input clear
                    $("#min_price").val("");
                    $("#max_price").val("");


                    // URL theke filter remove kore reload
                    window.location.href = window.location.pathname;


                });


            });
        </script>

    </div>

</body>

</html>