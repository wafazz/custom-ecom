<!-- Footer Section Begin -->
<footer class="footer" style="background: ghostwhite;">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="footer__about">
                    <div class="footer__logo">
                        <a href="./index.html"><img src="<?= $domainURL ?>assets/images/LOGO-ROZYANA-06-2.png"
                                alt=""></a>
                    </div>
                    <p>A-G-30, SAVANNA LIFESTYLE RETAIL,<br>
                        JALAN SOUTHVILLE 2, SOUTHVILLE CITY,<br>
                        43800 DENGKIL, SELANGOR</p>
                    <p>
                        <i class="fa-solid fa-phone"></i> 603 8912 3807
                    </p>
                    <div class="footer__payment">
                        <a href="#"><img src="<?= $domainURL ?>assets/ecom/img/payment/payment-1.png" alt=""></a>
                        <a href="#"><img src="<?= $domainURL ?>assets/ecom/img/payment/payment-2.png" alt=""></a>
                        <a href="#"><img src="<?= $domainURL ?>assets/ecom/img/payment/payment-3.png" alt=""></a>
                        <a href="#"><img src="<?= $domainURL ?>assets/ecom/img/payment/payment-4.png" alt=""></a>
                        <a href="#"><img src="<?= $domainURL ?>assets/ecom/img/payment/payment-5.png" alt=""></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="footer__widget">
                    <h6>Quick links</h6>
                    <ul>
                        <li><a href="<?= $domainURL ?>about-us">About</a></li>
                        <li><a href="<?= $domainURL ?>blog-annoucement">Blogs & Announcement</a></li>
                        <li><a href="<?= $domainURL ?>contact">Contact</a></li>
                        <li><a href="<?= $domainURL ?>customer/support-ticket">Support Tickets</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="footer__widget">
                    <h6>Others</h6>
                    <ul>
                        <li><a href="<?= $domainURL ?>checkout">Checkout</a></li>
                        <li><a href="<?= $domainURL ?>track-order">Orders Tracking</a></li>
                        <li><a href="<?= $domainURL ?>policies">Policy</a></li>
                        <li><a href="<?= $domainURL ?>terms-conditions">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                <div class="footer__copyright__text">
                    <p>Copyright &copy;
                        <script>
                            document.write(new Date().getFullYear());
                        </script> All rights reserved | <a
                            href="https://rozyana.com">Rozyana.com</a>
                    </p>
                </div>
                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            </div>
        </div>
    </div>
</footer>
<!-- Footer Section End -->

<!-- Search Begin -->
<div class="search-model">
    <div class="h-100 d-flex align-items-center justify-content-center">
        <div class="search-close-switch">+</div>
        <form class="search-model-form">
            <input type="text" id="search-input" placeholder="Search here.....">
        </form>
    </div>
</div>
<!-- Search End -->




<script>
    $(document).ready(function() {
        $('.qty-cartp1').text('<?= $countCart["count"] ?>');
        $('.qty-cartp2').text('<?= $countCart["count"] ?>');
    });
</script>

<!-- Js Plugins -->
<script src="<?= $domainURL ?>assets/ecom/js/jquery-3.3.1.min.js"></script>
<script src="<?= $domainURL ?>assets/ecom/js/bootstrap.min.js"></script>
<script src="<?= $domainURL ?>assets/ecom/js/jquery.magnific-popup.min.js"></script>
<script src="<?= $domainURL ?>assets/ecom/js/jquery-ui.min.js"></script>
<script src="<?= $domainURL ?>assets/ecom/js/mixitup.min.js"></script>
<script src="<?= $domainURL ?>assets/ecom/js/jquery.countdown.min.js"></script>
<script src="<?= $domainURL ?>assets/ecom/js/jquery.slicknav.js"></script>
<script src="<?= $domainURL ?>assets/ecom/js/owl.carousel.min.js"></script>
<script src="<?= $domainURL ?>assets/ecom/js/jquery.nicescroll.min.js"></script>
<script src="<?= $domainURL ?>assets/ecom/js/main.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<script>
    $(function() {
        // Hide counter and show the "show" button
        $("#hidecounter").on("click", function() {
            $("#thecounter").hide();
            $("#showcounter").show();
            $("#showcounter10").show();
        });

        // Show counter and hide the "show" button
        $("#showcounter").on("click", function() {
            $("#thecounter").show();
            $("#showcounter").hide();
            $("#showcounter10").hide();
        });
    });
</script>
<script>
    <?php if (!empty($successTicket)): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?= addslashes($successTicket) ?>',
            confirmButtonColor: '#28a745'
        });
    <?php endif; ?>

    <?php if (!empty($errorTicket)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?= addslashes($errorTicket) ?>',
            confirmButtonColor: '#d33'
        });
    <?php endif; ?>
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        function updateLiveData() {
            fetch("/live_visitors.json?_t=" + Date.now()) // cache-busting
                .then(response => response.json())
                .then(data => {
                    document.getElementById("liveOnlibe").textContent = data.live_user ?? 0;
                    document.getElementById("liveUser").textContent = data.all_user ?? 0;
                    document.getElementById("liveToday").textContent = data.all_today ?? 0;
                })
                .catch(err => console.error("Error loading live visitor data:", err));
        }

        // Initial load
        updateLiveData();

        // Refresh every 0.5 seconds
        setInterval(updateLiveData, 500);
    });
</script>

<div id="attachPopup"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.8); backdrop-filter:blur(5px); 
            justify-content:center; align-items:center; z-index:9999;">

    <div id="attachContent"
        style="max-width:90%; max-height:90%; background:#fff; padding:10px; border-radius:6px; position:relative;">
    </div>

    <span onclick="closeAttachmentPopup()"
        style="position:absolute; top:20px; right:30px; font-size:35px; color:white; cursor:pointer;">
        ×
    </span>
</div>

<script>
    function openAttachmentPopup(url, type) {
        let content = "";

        if (type.includes("video")) {
            content = `
            <video style="width:100%; max-height:80vh;" controls autoplay>
                <source src="${url}">
            </video>
        `;
        } else if (type === "application/pdf") {
            content = `
            <iframe src="${url}" style="width:80vw; height:80vh;" frameborder="0"></iframe>
        `;
        } else {
            content = `
            <img src="${url}" style="max-width:80vw; max-height:80vh;"/>
        `;
        }

        document.getElementById("attachContent").innerHTML = content;
        document.getElementById("attachPopup").style.display = "flex";
    }

    function closeAttachmentPopup() {
        document.getElementById("attachPopup").style.display = "none";
        document.getElementById("attachContent").innerHTML = "";
    }
</script>




</body>

</html>