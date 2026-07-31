<!-- Js File_________________________________ -->

<!-- j Query -->
<script type="text/javascript" src="js/jquery-2.1.4.js"></script>
<!-- Bootstrap JS -->
<script type="text/javascript" src="js/bootstrap.min.js"></script>

<!-- Vendor js _________ -->
<!-- Google map js -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRvBPo3-t31YFk588DpMYS6EqKf-oGBSI"></script>
<!-- Gmap Helper -->
<script src="js/gmap.js"></script>
<!-- owl.carousel -->
<script type="text/javascript" src="js/owl.carousel.min.js"></script>
<!-- ui js -->
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<!-- Responsive menu-->
<script type="text/javascript" src="js/menuzord.js"></script>
<!-- revolution -->
<script src="vendor/revolution/jquery.themepunch.tools.min.js"></script>
<script src="vendor/revolution/jquery.themepunch.revolution.min.js"></script>
<script
    type="text/javascript"
    src="vendor/revolution/revolution.extension.slideanims.min.js"></script>
<script
    type="text/javascript"
    src="vendor/revolution/revolution.extension.layeranimation.min.js"></script>
<script
    type="text/javascript"
    src="vendor/revolution/revolution.extension.navigation.min.js"></script>
<script
    type="text/javascript"
    src="vendor/revolution/revolution.extension.kenburn.min.js"></script>
<script
    type="text/javascript"
    src="vendor/revolution/revolution.extension.actions.min.js"></script>
<script
    type="text/javascript"
    src="vendor/revolution/revolution.extension.parallax.min.js"></script>
<script
    type="text/javascript"
    src="vendor/revolution/revolution.extension.migration.min.js"></script>

<!-- landguage switcher js -->
<script
    type="text/javascript"
    src="js/jquery.polyglot.language.switcher.js"></script>
<!-- Fancybox js -->
<script type="text/javascript" src="js/jquery.fancybox.pack.js"></script>
<!-- js count to -->
<script type="text/javascript" src="js/jquery.appear.js"></script>
<script type="text/javascript" src="js/jquery.countTo.js"></script>
<!-- WOW js -->
<script type="text/javascript" src="js/wow.min.js"></script>

<script type="text/javascript" src="js/SmoothScroll.js"></script>

<script src="js/bootstrap-select.min.js"></script>
<script src="js/jquery.mixitup.min.js"></script>
<!-- Theme js -->
<script type="text/javascript" src="js/theme.js"></script>
<script type="text/javascript" src="js/google-map.js"></script>



<!-- Cart JS -->
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {

        // ---------- Product page: quantity form (product.php) ----------
        var productForm = document.querySelector('form[action*="cart_add.php"]');
        if (productForm) {
            productForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(productForm);
                addToCart(formData);
            });
        }

        // ---------- Listing pages: icon "Add to Cart" links (index.php, includes/products.php) ----------
        // Expects the wrapping <li class="tultip-op"> to carry data-product-id="<id>"
        document.querySelectorAll('.tultip-op[data-product-id]').forEach(function(item) {
            var link = item.querySelector('a');
            if (!link) return;

            link.addEventListener('click', function(e) {
                e.preventDefault();
                var productId = item.getAttribute('data-product-id');
                if (!productId) return;

                var formData = new FormData();
                formData.append('product_id', productId);
                formData.append('quantity', 1);
                addToCart(formData);
            });
        });

        // ---------- Header cart dropdown: remove item ----------
        // Uses the .fa-times-circle icon that's already in includes/nav_cart_section.php
        // (NOT the one theme.js's cartItemDismiss() appends — remove that call, see note below)
        document.querySelectorAll('.cart_list .cart_remove_icon').forEach(function(icon) {
            icon.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var li = icon.closest('li[data-product-id]');
                if (!li) return;

                var productId = li.getAttribute('data-product-id');
                var formData = new FormData();
                formData.append('product_id', productId);

                fetch('core/cart_remove.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(function(res) {
                        return res.json();
                    })
                    .then(function(data) {
                        if (data.success) {
                            li.style.transition = 'opacity 0.3s ease';
                            li.style.opacity = '0';
                            setTimeout(function() {
                                li.remove();
                            }, 300);

                            updateCartCount(data.cart_count);

                            document.querySelectorAll('.cart_total_amount').forEach(function(el) {
                                el.textContent = '$' + data.cart_total;
                            });
                            document.querySelectorAll('.cart_total_text').forEach(function(el) {
                                el.textContent = 'Total - $' + data.cart_total;
                            });

                            showToast(data.message || 'Item removed.', false);
                        } else {
                            showToast(data.message || 'Could not remove item.', true);
                        }
                    })
                    .catch(function() {
                        showToast('Something went wrong. Please try again.', true);
                    });
            });
        });

        // ---------- Shared AJAX call ----------
        function addToCart(formData) {
            fetch('core/cart_add.php', {
                    method: 'POST',
                    body: formData
                })
                .then(function(res) {
                    return res.json();
                })
                .then(function(data) {
                    if (data.success) {
                        showToast(data.message || 'Added to cart!', false);
                        updateCartCount(data.cart_count);
                    } else {
                        showToast(data.message || 'Could not add to cart.', true);
                    }
                })
                .catch(function() {
                    showToast('Something went wrong. Please try again.', true);
                });
        }

        function updateCartCount(count) {
            // Add class="cart_count" to whichever element in your header shows the cart badge
            document.querySelectorAll('.cart_count').forEach(function(el) {
                el.textContent = count;
            });
        }

        function showToast(message, isError) {
            var toast = document.createElement('div');
            toast.className = 'cart_toast' + (isError ? ' cart_toast_error' : '');
            toast.textContent = message;
            document.body.appendChild(toast);

            requestAnimationFrame(function() {
                toast.classList.add('cart_toast_visible');
            });

            setTimeout(function() {
                toast.classList.remove('cart_toast_visible');
                setTimeout(function() {
                    toast.remove();
                }, 300);
            }, 2000);
        }

    });
</script>