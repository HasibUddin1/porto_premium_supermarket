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
| Category filter
|--------------------------------------------------------------------------
*/

$categoryId = isset($_GET['category']) ? (int) $_GET['category'] : 0;


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

$perPage = 9;
$page    = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

$whereSql = " WHERE products.price BETWEEN $minPrice AND $maxPrice ";
if ($categoryId > 0) {
    $whereSql .= " AND products.category_id = $categoryId ";
}

$countSql = "SELECT COUNT(*) AS total FROM products $whereSql";
$totalProducts = mysqli_fetch_assoc(mysqli_query($conn, $countSql))['total'];
$totalPages    = max(1, (int) ceil($totalProducts / $perPage));
$page          = min($page, $totalPages);
$offset        = ($page - 1) * $perPage;


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
$whereSql
ORDER BY products.id DESC
LIMIT $perPage OFFSET $offset
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

// Builds a pagination link that preserves category + price filters, only changing page
function build_shop_page_link($pageNum, $categoryId, $minPrice, $maxPrice, $dbMinPrice, $dbMaxPrice)
{
    $params = ['page' => $pageNum];
    if ($categoryId > 0) {
        $params['category'] = $categoryId;
    }
    if ($minPrice != $dbMinPrice || $maxPrice != $dbMaxPrice) {
        $params['min_price'] = $minPrice;
        $params['max_price'] = $maxPrice;
    }
    return 'shop?' . http_build_query($params);
}

?>

<!-- Shop Page Content************************ -->
<div class="shop_page featured-product">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-8 col-sm-12 col-sx-12">

                <div class="row">

                    <!--Default Item-->
                    <?php if (mysqli_num_rows($result) === 0): ?>

                        <div class="col-12">
                            <p style="padding: 20px 0;">No products found for this filter.</p>
                        </div>

                    <?php endif; ?>

                    <?php while ($product = mysqli_fetch_assoc($result)): ?>

                        <div class="col-md-4 col-sm-6 col-xs-12 default-item">

                            <div class="inner-box">

                                <div class="single-item center">


                                    <figure class="image-box">

                                        <img
                                            width="184px"
                                            height="184px"
                                            src="products/<?php echo htmlspecialchars($product['image']); ?>"
                                            alt="<?php echo htmlspecialchars($product['name']); ?>">

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
                                            <a href="product.php?id=<?php echo (int) $product['id']; ?>">
                                                <?php echo htmlspecialchars($product['name']); ?>
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

                                            $<?php echo htmlspecialchars($product['price']); ?>

                                        </div>


                                    </div>



                                    <div class="overlay-box">

                                        <div class="inner">


                                            <div class="top-content">

                                                <ul>

                                                    <li>
                                                        <a href="product.php?id=<?php echo (int) $product['id']; ?>">
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


                                                </ul>

                                            </div>



                                            <div class="bottom-content">

                                                <h4>
                                                    <a href="#">
                                                        It Contains:
                                                    </a>
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

                <?php if ($totalPages > 1): ?>
                    <div class="pagination_area" style="margin-top: 25px; text-align: center;">
                        <ul style="list-style: none; display: inline-flex; gap: 6px; padding: 0;">
                            <?php if ($page > 1): ?>
                                <li><a href="<?php echo build_shop_page_link($page - 1, $categoryId, $minPrice, $maxPrice, $dbMinPrice, $dbMaxPrice); ?>" class="tran3s" style="display: inline-block; padding: 8px 14px; border: 1px solid #eee;">&laquo; Prev</a></li>
                            <?php endif; ?>

                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <li>
                                    <a
                                        href="<?php echo build_shop_page_link($p, $categoryId, $minPrice, $maxPrice, $dbMinPrice, $dbMaxPrice); ?>"
                                        class="tran3s <?php echo $p === $page ? 'color1_bg' : ''; ?>"
                                        style="display: inline-block; padding: 8px 14px; border: 1px solid #eee; <?php echo $p === $page ? 'color: #fff;' : ''; ?>">
                                        <?php echo $p; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li><a href="<?php echo build_shop_page_link($page + 1, $categoryId, $minPrice, $maxPrice, $dbMinPrice, $dbMaxPrice); ?>" class="tran3s" style="display: inline-block; padding: 8px 14px; border: 1px solid #eee;">Next &raquo;</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>

            </div>

            <!-- _______________________ SIDEBAR ____________________ -->
            <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 sidebar_styleTwo">
                <div class="wrapper">

                    <div class="all_products_btn" style="margin-bottom: 20px;">
                        <a href="shop" class="thm-btn color1_bg" style="display: block; text-align: center; padding: 10px 0;">All Products</a>
                    </div>

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
                                    <a
                                        href="shop?category=<?php echo (int) $category['id']; ?>"
                                        class="tran3s<?php echo ($categoryId === (int) $category['id']) ? ' active_category' : ''; ?>">

                                        <?php echo htmlspecialchars($category['name']); ?>

                                        (<?php echo (int) $category['total_products']; ?>)

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

                                <?php if ($categoryId > 0): ?>
                                    <input type="hidden" name="category" value="<?php echo (int) $categoryId; ?>">
                                <?php endif; ?>

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