<?php

include_once __DIR__ . "/../core/db_connection.php";

/*
|--------------------------------------------------------------------------
| Price Range
|--------------------------------------------------------------------------
*/

$priceRangeQuery = mysqli_query($conn, "
SELECT
MIN(price) AS min_price,
MAX(price) AS max_price
FROM products
");

$priceRange = mysqli_fetch_assoc($priceRangeQuery);

$dbMinPrice = floor($priceRange['min_price']);
$dbMaxPrice = ceil($priceRange['max_price']);

$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : $dbMinPrice;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : $dbMaxPrice;


/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/

$sql = "
SELECT
products.*,
categories.name AS category_name
FROM products
LEFT JOIN categories
ON products.category_id = categories.id
WHERE products.price BETWEEN $minPrice AND $maxPrice
ORDER BY products.id DESC
";

$result = mysqli_query($conn, $sql);


/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
*/

$categoryQuery = "
SELECT
categories.id,
categories.name,
COUNT(products.id) AS total_products
FROM categories
LEFT JOIN products
ON categories.id = products.category_id
GROUP BY categories.id
ORDER BY categories.name ASC
";

$categoryResult = mysqli_query($conn, $categoryQuery);

?>

<!-- Shop Page Content************************ -->
<div class="shop_page featured-product">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-8 col-sm-12 col-sx-12">

                <div class="row">

                    <!--Default Item-->
                    <?php while ($product = mysqli_fetch_assoc($result)): ?>

                        <div class="col-md-4 col-sm-6 col-xs-12 default-item">

                            <div class="inner-box">

                                <div class="single-item center">


                                    <figure class="image-box">

                                        <img
                                            src="<?php echo $product['image']; ?>"
                                            alt="<?php echo $product['name']; ?>">

                                        <?php if ($product['status'] == "New"): ?>

                                            <div class="product-model new">
                                                New
                                            </div>

                                        <?php elseif ($product['status'] == "Hot"): ?>

                                            <div class="product-model hot">
                                                Hot
                                            </div>

                                        <?php endif; ?>


                                    </figure>


                                    <div class="content">

                                        <h3>
                                            <a href="product.php?id=<?php echo $product['id']; ?>">
                                                <?php echo $product['name']; ?>
                                            </a>
                                        </h3>


                                        <div class="rating">

                                            <span class="fa fa-star"></span>
                                            <span class="fa fa-star"></span>
                                            <span class="fa fa-star"></span>
                                            <span class="fa fa-star"></span>
                                            <span class="fa fa-star"></span>

                                        </div>


                                        <div class="price">

                                            $<?php echo $product['price']; ?>

                                        </div>


                                    </div>



                                    <div class="overlay-box">

                                        <div class="inner">


                                            <div class="top-content">

                                                <ul>

                                                    <li>
                                                        <a href="product.php?id=<?php echo $product['id']; ?>">
                                                            <span class="fa fa-eye"></span>
                                                        </a>
                                                    </li>


                                                    <li class="tultip-op" data-product-id="<?php echo (int) $product['id']; ?>">
                                                        <span class="tultip">
                                                            <i class="fa fa-sort-desc"></i>
                                                            ADD TO CART
                                                        </span>

                                                        <a href="#">
                                                            <span class="icon-icon-32846"></span>
                                                        </a>
                                                    </li>


                                                    <li>
                                                        <a href="#">
                                                            <span class="fa fa-heart-o"></span>
                                                        </a>
                                                    </li>


                                                </ul>

                                            </div>



                                            <div class="bottom-content">

                                                <h4>
                                                    <a href="#">
                                                        It Contains:
                                                    </a>
                                                </h4>


                                                <p>
                                                    <?php echo $product['description']; ?>
                                                </p>


                                            </div>


                                        </div>

                                    </div>



                                </div>

                            </div>

                        </div>


                    <?php endwhile; ?>

                </div>
            </div>

            <!-- _______________________ SIDEBAR ____________________ -->
            <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 sidebar_styleTwo">
                <div class="wrapper">
                    <div class="sidebar_search">
                        <form action="#">
                            <input type="text">
                            <button class="tran3s color1_bg"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </form>
                    </div> <!-- End of .sidebar_styleOne -->

                    <div class="sidebar_categories">

                        <div class="theme_inner_title">
                            <h4>Categories</h4>
                        </div>


                        <ul>

                            <?php while ($category = mysqli_fetch_assoc($categoryResult)): ?>

                                <li>
                                    <a href="shop?category=<?php echo $category['id']; ?>" class="tran3s">

                                        <?php echo $category['name']; ?>

                                        (<?php echo $category['total_products']; ?>)

                                    </a>
                                </li>

                            <?php endwhile; ?>


                        </ul>

                    </div> <!-- End of .sidebar_categories -->

                    <!-- Price Filter -->
                    <div class="price_filter wow fadeInUp">
                        <div class="theme_inner_title">
                            <h4>Filter By Price</h4>
                        </div>

                        <div class="single-sidebar price-ranger">

                            <form action="" method="GET">

                                <div id="slider-range"></div>

                                <div class="ranger-min-max-block">

                                    <input type="hidden" name="min_price" id="min_price">

                                    <input type="hidden" name="max_price" id="max_price">

                                    <div class="d-block">
                                        <span>Price:</span>

                                        <input type="text" readonly class="min">

                                        <span>-</span>

                                        <input type="text" readonly class="max">
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <input type="submit" value="Filter">

                                        <button type="button" id="reset-filter" class="inline">
                                            Reset
                                        </button>
                                    </div>



                                </div>

                            </form>

                        </div>

                    </div>
                    <!-- /price_filter -->



                </div> <!-- End of .wrapper -->
            </div> <!-- End of .sidebar_styleTwo -->
        </div> <!-- End of .row -->
    </div> <!-- End of .container -->
</div> <!-- End of .shop_page -->