<?php

include_once __DIR__ . "/../core/db_connection.php";


// Random Categories for Featured Products Section
$featuredCategories = mysqli_query($conn, "
    SELECT id, name
    FROM categories
    ORDER BY RAND()
    LIMIT 4
");


// Products Fetch
$productQuery = mysqli_query($conn, "
SELECT
    products.*,
    categories.name AS category_name
FROM products
LEFT JOIN categories
ON categories.id = products.category_id
ORDER BY products.id DESC
");

// Product Ratings Fetch
$productQuery = mysqli_query($conn, "
SELECT
    products.*,
    categories.name AS category_name,
    IFNULL(AVG(product_reviews.rating),0) AS avg_rating
FROM products

LEFT JOIN categories
ON categories.id = products.category_id

LEFT JOIN product_reviews
ON products.id = product_reviews.product_id

GROUP BY products.id

ORDER BY products.id DESC
");

?>

<!--feature Section-->
<section class="featured-product" id="featured_products">
    <div class="container">
        <div class="theme_title center">
            <h3>FEATURED PRODUCTS</h3>
        </div>

        <!--Filter-->
        <div class="filters text-center">
            <ul class="filter-tabs filter-btns clearfix">
                <li class="filter active" data-role="button" data-filter="all">
                    <span class="txt">All Products</span>
                </li>
                <?php while ($category = mysqli_fetch_assoc($featuredCategories)): ?>

                    <li
                        class="filter"
                        data-role="button"
                        data-filter=".category-<?php echo $category['id']; ?>">
                        <span class="txt">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </span>
                    </li>

                <?php endwhile; ?>
            </ul>
        </div>

        <div class="row filter-list clearfix" id="MixItUp717B05">

            <?php while ($product = mysqli_fetch_assoc($productQuery)): ?>
                <!--Default Item-->
                <div
                    class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mix mix_all default-item all category-<?php echo $product['category_id']; ?>"
                    style="display: inline-block">
                    <div class="inner-box">
                        <div class="single-item center">

                            <figure class="image-box">
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">

                                <?php if (!empty($product['status'])): ?>
                                    <div class="product-model <?php echo strtolower($product['status']); ?>">
                                        <?php echo htmlspecialchars($product['status']); ?>
                                    </div>
                                <?php endif; ?>
                            </figure>

                            <div class="content">

                                <h3>
                                    <a href="#">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                </h3>

                                <div class="rating">
                                    <?php
                                    $rating = round($product['avg_rating']);

                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '<span class="fa fa-star"></span>';
                                        } else {
                                            echo '<span class="fa fa-star-o"></span>';
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="price">
                                    $<?php echo number_format($product['price'], 2); ?>
                                </div>

                            </div>

                            <div class="overlay-box">
                                <div class="inner">

                                    <div class="top-content">
                                        <ul>
                                            <li>
                                                <a href="product.php?id=<?php echo $product['id']; ?>"><span class="fa fa-eye"></span></a>
                                            </li>

                                            <li class="tultip-op">
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
                                            <a href="#">Description:</a>
                                        </h4>

                                        <p>
                                            <?php echo htmlspecialchars(substr($product['description'], 0, 80)); ?>...
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
</section>
<!-- End of section -->