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
<script type="text/javascript" src="js/cart.js"></script>


<!-- Homepage Category JS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var showMoreBtn = document.getElementById('showMoreCategoriesBtn');
        if (!showMoreBtn) return;

        showMoreBtn.addEventListener('click', function() {
            var extraRow = document.getElementById('extraCategoryRow');
            if (!extraRow) return;

            extraRow.style.display = 'block';

            var items = extraRow.querySelectorAll('.extra-category-item');
            items.forEach(function(item, i) {
                setTimeout(function() {
                    item.classList.add('reveal');
                }, i * 70); // slight stagger, so items fade in one after another
            });

            showMoreBtn.remove();
        });
    });
</script>


<!-- Checkout page js -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var shipCheckbox = document.getElementById('shipDifferentCheckbox');
        var shippingForm = document.getElementById('shippingForm');
        var placeOrderBtn = document.getElementById('placeOrderBtn');
        var billingForm = document.getElementById('billingForm');

        if (shipCheckbox && shippingForm) {
            shipCheckbox.addEventListener('change', function() {
                shippingForm.style.display = shipCheckbox.checked ? '' : 'none';
            });
        }

        if (placeOrderBtn) {
            placeOrderBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Build one hidden form combining billing + (optional) shipping +
                // payment method, then submit it normally to core/place_order.php.
                // This keeps the original two <form class="row"> elements
                // (and their CSS-driven styling) completely untouched.
                var mergedForm = document.createElement('form');
                mergedForm.method = 'POST';
                mergedForm.action = 'core/place_order.php';
                mergedForm.style.display = 'none';

                function copyFields(sourceForm) {
                    if (!sourceForm) return;
                    var fields = sourceForm.querySelectorAll('input, textarea, select');
                    fields.forEach(function(field) {
                        if (!field.name) return;
                        var hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = field.name;
                        hidden.value = field.value;
                        mergedForm.appendChild(hidden);
                    });
                }

                copyFields(billingForm);

                if (shipCheckbox && shipCheckbox.checked) {
                    var shipDifferentInput = document.createElement('input');
                    shipDifferentInput.type = 'hidden';
                    shipDifferentInput.name = 'ship_different';
                    shipDifferentInput.value = '1';
                    mergedForm.appendChild(shipDifferentInput);

                    copyFields(shippingForm);
                }

                var selectedPayment = document.querySelector('input[name="payment_method"]:checked');
                var paymentInput = document.createElement('input');
                paymentInput.type = 'hidden';
                paymentInput.name = 'payment_method';
                paymentInput.value = selectedPayment ? selectedPayment.value : '';
                mergedForm.appendChild(paymentInput);

                document.body.appendChild(mergedForm);
                mergedForm.submit();
            });
        }
    });
</script>