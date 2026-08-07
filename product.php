<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!doctype html>
<html lang="en">

<head>

    <!-- TODO: need to make the page name dynamic -->
    <?php
    $pageInfo = [
        "title" => "Porto Premium Supermarket",
    ];
    ?>

    <?php include_once "includes/head.php"; ?>

</head>

<body>

    <div class="main_page">
        <?php include_once "includes/nav.php"; ?>

        <section class="breadcrumb-area" style="background-image:url(images/background/2.jpg);">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="breadcrumbs text-center">
                            <h1>single product</h1>
                            <h4>Welcome to certified online organic products suppliersr</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="breadcrumb-bottom-area">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-md-5 col-sm-5">
                            <ul>
                                <li><a href="#">Home</a></li>
                                <li><a href="#"><i class="fa fa-angle-right"></i></a></li>
                                <li><a href="#">Gallery</a></li>
                                <li><a href="#"><i class="fa fa-angle-right"></i></a></li>
                                <li>single product</li>
                            </ul>
                        </div>
                        <div class="col-lg-4 col-md-7 col-sm-7">
                            <p>We provide <span>100% organic</span> products</p>
                        </div>
                    </div>
                </div>
            </div>

        </section>


        <!-- Single Product content ________________ -->

        <?php
        // includes/product.php
        // Single product page — dynamic version driven by the `products` table
        // Uses mysqli ($conn) from config/config.php

        require_once __DIR__ . '/core/db_connection.php'; // exposes the mysqli connection as $conn

        // ---------- 1. Validate & fetch the requested product ----------
        $productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($productId <= 0) {
            header('Location: shop.php');
            exit;
        }

        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$product) {
            header('Location: shop.php');
            exit;
        }

        // tags stored as comma-separated string -> array
        $tags = !empty($product['tags'])
            ? array_map('trim', explode(',', $product['tags']))
            : [];

        // ---------- 2. Sidebar: categories with product counts ----------
        $categories = [];
        $catResult = $conn->query("
    SELECT c.id, c.name, COUNT(p.id) AS product_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    GROUP BY c.id, c.name
    ORDER BY c.name
");
        if ($catResult) {
            while ($row = $catResult->fetch_assoc()) {
                $categories[] = $row;
            }
        }

        // ---------- 3. Sidebar: price range (for the filter slider) ----------
        $priceRange = ['min_price' => 0, 'max_price' => 0];
        $priceResult = $conn->query("SELECT MIN(price) AS min_price, MAX(price) AS max_price FROM products");
        if ($priceResult) {
            $priceRange = $priceResult->fetch_assoc();
        }

        // ---------- 4. Sidebar: popular products (best sellers) ----------
        $popularProducts = [];
        $popStmt = $conn->prepare("
    SELECT id, name, image, price
    FROM products
    WHERE id != ?
    ORDER BY total_sales DESC
    LIMIT 3
");
        $popStmt->bind_param('i', $productId);
        $popStmt->execute();
        $popResult = $popStmt->get_result();
        while ($row = $popResult->fetch_assoc()) {
            $popularProducts[] = $row;
        }
        $popStmt->close();

        // ---------- 5. Sidebar: tag cloud (distinct tags across all products) ----------
        $tagCloud = [];
        $tagResult = $conn->query("SELECT tags FROM products WHERE tags IS NOT NULL AND tags != ''");
        if ($tagResult) {
            $seen = [];
            while ($row = $tagResult->fetch_assoc()) {
                foreach (explode(',', $row['tags']) as $t) {
                    $t = trim($t);
                    if ($t !== '' && !isset($seen[$t])) {
                        $seen[$t] = true;
                        $tagCloud[] = $t;
                    }
                }
            }
        }

        // ---------- 6. Related products (same category, excluding current) ----------
        $relatedProducts = [];
        $relStmt = $conn->prepare("
    SELECT id, name, image, price, status
    FROM products
    WHERE category_id = ? AND id != ?
    ORDER BY created_at DESC
    LIMIT 3
");
        $categoryId = (int) $product['category_id'];
        $relStmt->bind_param('ii', $categoryId, $productId);
        $relStmt->execute();
        $relResult = $relStmt->get_result();
        while ($row = $relResult->fetch_assoc()) {
            $relatedProducts[] = $row;
        }
        $relStmt->close();

        // ---------- 7. Reviews for this product ----------
        $reviews = [];
        $revStmt = $conn->prepare("
    SELECT customer_name, rating, review, created_at
    FROM product_reviews
    WHERE product_id = ?
    ORDER BY created_at DESC
");
        $revStmt->bind_param('i', $productId);
        $revStmt->execute();
        $revResult = $revStmt->get_result();
        while ($row = $revResult->fetch_assoc()) {
            $reviews[] = $row;
        }
        $revStmt->close();

        // average rating (for the star display near the product title)
        $avgRating = 0;
        if (!empty($reviews)) {
            $sum = 0;
            foreach ($reviews as $r) {
                $sum += (int) $r['rating'];
            }
            $avgRating = round($sum / count($reviews));
        }

        // helper: escape output safely
        function e($value)
        {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        }
        ?>

        <div class="shop_single_page">
            <div class="container">
                <div class="row">

                    <!-- _______________________ SIDEBAR ____________________ -->
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 sidebar_styleTwo">
                        <div class="wrapper">
                            <div class="sidebar_search">
                                <form action="shop.php" method="get">
                                    <input type="text" name="search" placeholder="Search products">
                                    <button class="tran3s color1_bg"><i class="fa fa-search" aria-hidden="true"></i></button>
                                </form>
                            </div> <!-- End of .sidebar_styleOne -->

                            <div class="sidebar_categories">
                                <div class="theme_inner_title">
                                    <h4>Categories</h4>
                                </div>
                                <ul>
                                    <?php foreach ($categories as $cat): ?>
                                        <li>
                                            <a href="shop.php?category=<?php echo (int) $cat['id']; ?>" class="tran3s">
                                                <?php echo e($cat['name']); ?> (<?php echo (int) $cat['product_count']; ?>)
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div> <!-- End of .sidebar_categories -->



                            <div class="best_sellers clear_fix wow fadeInUp">
                                <div class="theme_inner_title">
                                    <h4>popular products</h4>
                                </div>

                                <?php foreach ($popularProducts as $i => $pop): ?>
                                    <div class="best_selling_item clear_fix<?php echo $i < count($popularProducts) - 1 ? ' border' : ''; ?>">
                                        <div class="img_holder float_left">
                                            <img width="70" src="<?php echo e($pop['image']); ?>" alt="<?php echo e($pop['name']); ?>">
                                        </div> <!-- End of .img_holder -->

                                        <div class="text float_left">
                                            <a href="product.php?id=<?php echo (int) $pop['id']; ?>">
                                                <h6><?php echo e($pop['name']); ?></h6>
                                            </a>
                                            <ul>
                                                <?php for ($s = 0; $s < 5; $s++): ?>
                                                    <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                <?php endfor; ?>
                                            </ul>
                                            <span>$ <?php echo e(number_format((float) $pop['price'], 2)); ?></span>
                                        </div> <!-- End of .text -->
                                    </div> <!-- End of .best_selling_item -->
                                <?php endforeach; ?>
                            </div> <!-- End of .best_sellers -->

                            <div class="sidebar_tags wow fadeInUp">
                                <div class="theme_inner_title">
                                    <h4>product Tags</h4>
                                </div>

                                <ul>
                                    <?php foreach ($tagCloud as $tag): ?>
                                        <li><a href="shop.php?tag=<?php echo urlencode($tag); ?>" class="tran3s"><?php echo e($tag); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div> <!-- End of .sidebar_tags -->

                        </div> <!-- End of .wrapper -->
                    </div> <!-- End of .sidebar_styleTwo -->

                    <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12 product_details">
                        <div class="wrapper">
                            <div class="product_top_section clear_fix">
                                <div class="img_holder float_left">
                                    <img width="100" src="products/<?php echo e($product['image']); ?>" alt="<?php echo e($product['name']); ?>" class="img-responsive">
                                </div> <!-- End of .img_holder -->
                                <div class="item_description float_left">
                                    <h4><?php echo e($product['name']); ?></h4>
                                    <ul>
                                        <?php for ($s = 0; $s < 5; $s++): ?>
                                            <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                        <?php endfor; ?>
                                        <li>(<?php echo count($reviews) ? (int) count($reviews) : 0; ?> Customers Review)</li>
                                    </ul>
                                    <span class="item_price">$ <?php echo e(number_format((float) $product['price'], 2)); ?></span>
                                    <p><?php echo nl2br(e($product['description'])); ?></p>

                                    <?php if (!empty($product['status'])): ?>
                                        <span class="product-status badge"><?php echo e($product['status']); ?></span>
                                    <?php endif; ?>

                                    <span class="check_location">Check Delivery Option at Your Location:</span>
                                    <div class="clear_fix">
                                        <input type="text" class="float_left" placeholder="Pincode" id="pincode">
                                        <button class="float_left tran3s" id="checkDelivery">Check</button>
                                        <span class="float_left color1">*Expected Delivery in 4-10 Days</span>
                                    </div>

                                    <form action="core/cart_add.php" method="post">
                                        <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                        <input class="product_quantity" type="number" name="quantity" value="1" min="1">
                                        <button type="submit" class="d-inline tran3s color1_bg add_to_cart_btn">Add to Cart</button>
                                    </form>
                                </div> <!-- End of .item_description -->
                            </div> <!-- End of .product_top_section -->

                            <!-- __________________ Product review ___________________ -->
                            <div class="product-review-tab">
                                <ul class="nav nav-pills">
                                    <li><a data-toggle="pill" href="#tab1">Description</a></li>
                                    <li class="active"><a data-toggle="pill" href="#tab2">Reviews(<?php echo count($reviews); ?>)</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div id="tab1" class="tab-pane fade">
                                        <p><?php echo nl2br(e($product['description'])); ?></p>
                                    </div> <!-- End of #tab1 -->

                                    <div id="tab2" class="tab-pane fade in active">

                                        <?php if (empty($reviews)): ?>
                                            <p>No reviews yet. Be the first to review this product.</p>
                                        <?php else: ?>
                                            <?php foreach ($reviews as $rev): ?>
                                                <!-- Single Review -->
                                                <div class="item_review_content clear_fix">
                                                    <div class="text float_left">
                                                        <div class="sec_up clear_fix">
                                                            <h6 class="float_left"><?php echo e($rev['customer_name'] ?: 'Anonymous'); ?></h6>
                                                            <div class="float_right">
                                                                <span class="p_color"><?php echo e(date('d/m/Y \a\t H.i', strtotime($rev['created_at']))); ?></span>
                                                                <ul>
                                                                    <?php
                                                                    $rating = (int) $rev['rating'];
                                                                    for ($s = 1; $s <= 5; $s++):
                                                                    ?>
                                                                        <li><i class="fa <?php echo $s <= $rating ? 'fa-star' : 'fa-star-o'; ?>" aria-hidden="true"></i></li>
                                                                    <?php endfor; ?>
                                                                </ul>
                                                            </div>
                                                        </div> <!-- End of .sec_up -->
                                                        <p><?php echo nl2br(e($rev['review'])); ?></p>
                                                    </div> <!-- End of .text -->
                                                </div> <!-- End of .item_review_content -->
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                        <div class="add_your_review">
                                            <div class="theme_inner_title">
                                                <h4>Add Your Review</h4>
                                            </div>

                                            <form action="review_add.php" method="post">
                                                <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                        <input type="text" name="customer_name" placeholder="Name*" required>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                        <select name="rating" required>
                                                            <option value="">Rating*</option>
                                                            <option value="5">5 - Excellent</option>
                                                            <option value="4">4 - Good</option>
                                                            <option value="3">3 - Average</option>
                                                            <option value="2">2 - Fair</option>
                                                            <option value="1">1 - Poor</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <textarea name="review" placeholder="Your Review..." required></textarea>
                                                    </div>
                                                </div>
                                                <button class="color1_bg tran3s" type="submit">Add A Review</button>
                                            </form>
                                        </div> <!-- End of .add_your_review -->
                                    </div> <!-- End of #tab2 -->
                                </div> <!-- End of .tab-content -->
                            </div> <!-- End of .product-review-tab -->

                            <?php if (!empty($relatedProducts)): ?>
                                <div class="related_product">
                                    <div class="theme_title">
                                        <h3>Related Products</h3>
                                    </div>

                                    <div class="shop_page featured-product">
                                        <div class="row">
                                            <?php foreach ($relatedProducts as $rel): ?>
                                                <div class="col-md-4 col-sm-6 col-xs-12 default-item" style="display: inline-block;">
                                                    <div class="inner-box">
                                                        <div class="single-item center">
                                                            <figure class="image-box">
                                                                <img src="<?php echo e($rel['image']); ?>" alt="<?php echo e($rel['name']); ?>">
                                                                <?php if ($rel['status'] === 'New'): ?>
                                                                    <div class="product-model new">New</div>
                                                                <?php elseif ($rel['status'] === 'Hot'): ?>
                                                                    <div class="product-model hot">Hot</div>
                                                                <?php endif; ?>
                                                            </figure>
                                                            <div class="content">
                                                                <h3><a href="product.php?id=<?php echo (int) $rel['id']; ?>"><?php echo e($rel['name']); ?></a></h3>
                                                                <div class="rating">
                                                                    <?php for ($s = 0; $s < 5; $s++): ?>
                                                                        <span class="fa fa-star"></span>
                                                                    <?php endfor; ?>
                                                                </div>
                                                                <div class="price">$<?php echo e(number_format((float) $rel['price'], 2)); ?></div>
                                                            </div>
                                                            <div class="overlay-box">
                                                                <div class="inner">
                                                                    <div class="top-content">
                                                                        <ul>
                                                                            <li><a href="product.php?id=<?php echo (int) $rel['id']; ?>"><span class="fa fa-eye"></span></a></li>
                                                                            <li class="tultip-op">
                                                                                <span class="tultip"><i class="fa fa-sort-desc"></i>ADD TO CART</span>
                                                                                <a href="cart_add.php?product_id=<?php echo (int) $rel['id']; ?>"><span class="icon-icon-32846"></span></a>
                                                                            </li>
                                                                            
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div> <!-- End of .row -->
                                    </div> <!-- End of .shop_page -->
                                </div> <!-- End of .related_product -->
                            <?php endif; ?>

                        </div> <!-- End of .wrapper -->
                    </div> <!-- End of .col -->

                </div> <!-- End of .row -->
            </div> <!-- End of .container -->
        </div> <!-- End of .shop_single_page -->


        <?php include_once "includes/footer.php"; ?>


        <?php include_once "includes/scripts.php"; ?>




    </div>

</body>

</html>