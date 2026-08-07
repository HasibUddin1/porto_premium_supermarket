<?php

include_once __DIR__ . "/../core/db_connection.php";

// ---------- Overall total (used for the "All Products" tab's View More) ----------
$totalProducts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"))['total'];

// ---------- Pick 4 random categories that actually have products ----------
$featuredCategoriesResult = mysqli_query($conn, "
    SELECT c.id, c.name, COUNT(p.id) AS total_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    GROUP BY c.id
    HAVING total_count > 0
    ORDER BY RAND()
    LIMIT 4
");
$featuredCategories = mysqli_fetch_all($featuredCategoriesResult, MYSQLI_ASSOC);

// ---------- For each of those categories, fetch up to 8 of its products ----------
$displayProducts = [];

foreach ($featuredCategories as $cat) {
    $stmt = mysqli_prepare($conn, "
        SELECT
            products.*,
            IFNULL(AVG(product_reviews.rating), 0) AS avg_rating
        FROM products
        LEFT JOIN product_reviews ON products.id = product_reviews.product_id
        WHERE products.category_id = ?
        GROUP BY products.id
        ORDER BY products.id DESC
        LIMIT 8
    ");
    mysqli_stmt_bind_param($stmt, 'i', $cat['id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($product = mysqli_fetch_assoc($result)) {
        $displayProducts[] = $product;
    }
    mysqli_stmt_close($stmt);
}

// If everything currently in the DB is already shown above, there's nothing
// more to see on the "All Products" tab, so don't offer a View More there.
$showAllViewMore = $totalProducts > count($displayProducts);

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
                <li
                    class="filter active"
                    data-role="button"
                    data-filter="all"
                    data-shop-link="shop"
                    data-show-more="<?php echo $showAllViewMore ? '1' : '0'; ?>">
                    <span class="txt">All Products</span>
                </li>
                <?php foreach ($featuredCategories as $cat): ?>

                    <li
                        class="filter"
                        data-role="button"
                        data-filter=".category-<?php echo (int) $cat['id']; ?>"
                        data-shop-link="shop?category=<?php echo (int) $cat['id']; ?>"
                        data-show-more="<?php echo $cat['total_count'] > 8 ? '1' : '0'; ?>">
                        <span class="txt">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </span>
                    </li>

                <?php endforeach; ?>
            </ul>
        </div>

        <div class="row filter-list clearfix" id="MixItUp717B05">

            <?php foreach ($displayProducts as $product): ?>

                <!--Default Item-->
                <div
                    class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mix mix_all default-item all category-<?php echo (int) $product['category_id']; ?>"
                    style="display: inline-block">
                    <div class="inner-box">
                        <div class="single-item center">

                            <figure class="image-box">
                                <img height="184px" width="184px" src="products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">

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

                                            <li class="tultip-op" data-product-id="<?php echo (int) $product['id']; ?>">
                                                <span class="tultip">
                                                    <i class="fa fa-sort-desc"></i>
                                                    ADD TO CART
                                                </span>

                                                <a href="#">
                                                    <span class="icon-icon-32846"></span>
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
            <?php endforeach; ?>

        </div>

        <div class="text-center" id="featuredViewMoreWrap" style="margin-top: 30px; <?php echo $showAllViewMore ? '' : 'display: none;'; ?>">
            <a href="shop" class="thm-btn btn-style-one" id="featuredViewMoreBtn">
                View More
            </a>
        </div>

    </div>
</section>
<!-- End of section -->

<script>
    // Swap the "View More" button's target + visibility to match whichever
    // filter tab is active, since each category can have more than the 8
    // products shown here.
    document.addEventListener('DOMContentLoaded', function() {
        var tabs = document.querySelectorAll('#featured_products .filter-tabs .filter');
        var viewMoreWrap = document.getElementById('featuredViewMoreWrap');
        var viewMoreBtn = document.getElementById('featuredViewMoreBtn');

        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                var showMore = tab.getAttribute('data-show-more') === '1';
                var shopLink = tab.getAttribute('data-shop-link');

                viewMoreBtn.setAttribute('href', shopLink);
                viewMoreWrap.style.display = showMore ? '' : 'none';
            });
        });
    });
</script>