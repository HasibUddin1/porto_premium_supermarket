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