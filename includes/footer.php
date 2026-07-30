<!-- Footer************************* -->
<footer>
    <div class="main_footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 footer_logo">
                    <a href="index.html"><img src="images/logo/porto_logo_dark2.webp" alt="Logo" /></a>
                    <p>
                        Denouncing pleasures and praising pain was born and I will
                        give you a complete account of the system.
                    </p>
                    <p>
                        Expound that actual teachings the great explorer of the truth,
                        the master-builder of human happiness no one rejects, likes,
                        or avoids pleasure itself rationally.
                    </p>

                    <a href="shop" class="tran3s">Shop Now</a>
                </div>
                <!-- End of .footer_logo -->

                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 footer_news">
                    <h5>recent post</h5>

                    <div class="recent-posts">
                        <div class="post">
                            <div class="post-thumb">
                                <a href="#"><img src="images/blog/p1.jpg" alt="" /></a>
                            </div>
                            <h4>
                                <a href="#">Control your cholestrol & fat in 2 weeks</a>
                            </h4>
                            <div class="post-info">
                                <i class="fa fa-clock-o"></i>08th Sep, 2015
                            </div>
                        </div>
                        <div class="post">
                            <div class="post-thumb">
                                <a href="#"><img src="images/blog/p2.jpg" alt="" /></a>
                            </div>
                            <h4>
                                <a href="#">Control your cholestrol & fat in 2 weeks</a>
                            </h4>
                            <div class="post-info">
                                <i class="fa fa-clock-o"></i>08th Sep, 2015
                            </div>
                        </div>
                        <div class="post">
                            <div class="post-thumb">
                                <a href="#"><img src="images/blog/p3.jpg" alt="" /></a>
                            </div>
                            <h4>
                                <a href="#">Control your cholestrol & fat in 2 weeks</a>
                            </h4>
                            <div class="post-info">
                                <i class="fa fa-clock-o"></i>08th Sep, 2015
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of .footer_news -->

                <div
                    class="col-lg-3 col-md-6 col-sm-6 col-xs-12 footer_subscribe">
                    <h5>categoreis</h5>
                    <ul class="list catagories">
                        <li>
                            <a href="#"><i class="fa fa-angle-right"></i>Arable & Postoral
                                Farmers</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-angle-right"></i>Become a Member</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-angle-right"></i>Shop Our Prodcuts</a>
                        </li>
                    </ul>
                    <div class="widget popular-tags">
                        <div class="sidebar-title">
                            <h5>tags</h5>
                        </div>

                        <a >Fruits</a>
                        <a >Vegetables</a>
                        <a >Fishes</a>
                        <a >Seafoods</a>
                        <a >Household Items</a>
                        <a >Snacks & Biscuits</a>
                    </div>
                </div>
                <!-- End of .footer_subscribe -->

                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 footer_contact">
                    <h5>Get In Touch</h5>
                    <ul class="list catagories">
                        <li>
                            <a href="mailto:sabedoria.porto@gmail.com"><i class="fa fa-envelope"></i>sabedoria.porto@gmail.com</a>
                        </li>
                        <li>
                            <a href="tel:+351920526147"><i class="fa fa-phone"></i>+351 920 526 147</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-home"></i>No 271, Red Cross Building,
                                Modern Street, Newyork City, USA.</a>
                        </li>
                    </ul>

                    <h5>Business Hours</h5>
                    <div class="list Business">
                        <p>
                            Monday - Friday: 09.00am to 07.00pm <br />Saturday: 10.00am
                            to 05.00pm <br />Sunday: <span>Closed</span>
                        </p>
                    </div>
                </div>
                <!-- End of .footer_contact -->
            </div>
        </div>
    </div>
    <!-- End of .main_footer -->

    <div class="bottom_footer clear_fix">
        <div class="container">
            <h6 class="pull-left">
                Copyrights © 2026 All Rights Reserved by<a
                    href="#">Porto Premium Supermarket</a>
            </h6>
            <ul class="social_icon pull-right">
                <li>
                    <a href="#" class="tran3s"><i class="fa fa-cc-visa" aria-hidden="true"></i></a>
                </li>
                <li>
                    <a href="#" class="tran3s"><i class="fa fa-cc-mastercard" aria-hidden="true"></i></a>
                </li>
                <li>
                    <a href="#" class="tran3s"><i class="fa fa-paypal" aria-hidden="true"></i></a>
                </li>
                <li>
                    <a href="#" class="tran3s"><i class="fa fa-credit-card-alt" aria-hidden="true"></i></a>
                </li>
                <li>
                    <a href="#" class="tran3s"><i class="fa fa-cc-discover" aria-hidden="true"></i></a>
                </li>
            </ul>
        </div>
    </div>
</footer>

<!-- Scroll Top Button -->
<button class="scroll-top tran3s color2_bg">
    <span class="fa fa-angle-up"></span>
</button>
<!-- pre loader  -->
<div id="loader-wrapper">
    <div id="loader"></div>
</div>



<?php
$html = ob_get_clean();
$html = preg_replace('/<!--(.|\s)*?-->/', '', $html); // Remove HTML comments
$html = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $html); // Remove CSS comments
echo $html;
?>