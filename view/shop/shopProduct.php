<?php include "header.php"; ?>
<?php include "menu.php"; ?>
<!-- fashion section start -->
<div class="fashion_section" style="background: #fff;
    margin-top: -190px;
    padding-top: 30px;">
    <div id="main_slider" class="carousel slide" data-ride="carousel">
        <div class="container">
            
            <!-- <div class="row">
                <div class="col-12">
                    <h3 class="product_title">
                    <?= $id; ?> : <?= $product["product_name"]; ?>
                    </h3>
                </div>
            </div> -->
            <div class="row">
                <div class="col-lg-5 col-12 product-view">
                    <img src="https://fusionkeymall.com/assets/images/product/<?= $product['product_image']; ?>">
                </div>
                <div class="col-lg-7 col-12">
                    <h3 class="product_title_underline">
                    Product ID: <?= $id; ?>
                    </h3>
                    <h3 class="product_title">
                    <?= $product["product_name"]; ?>
                    </h3>
                    <span class="line-break-tag">SKU: <span class="product-sku"><?= $product['product_sku']; ?></span>  | <i>SOLD (coming soon)</i> | <i>RATING (coming soon)</i>
                    <span class="price-tag">MYR <?= number_format($product['selling_price'], 2); ?></span>
                    <span class="membership-tag">Membership Point: <b><?= number_format($product['member_point'], 2); ?></b></span>
                    </span>

                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-12">
                            <style>
                                .input-container {
                                    display: flex;
                                    align-items: center;
                                    gap: 5px;
                                    font-family: Arial, sans-serif;
                                }

                                 .plus-minus {
                                    width: 30px;
                                    height: 30px;
                                    font-size: 18px;
                                    cursor: pointer;
                                    border: 1px solid #ccc;
                                    background-color: #f0f0f0;
                                    border-radius: 5px;
                                    line-height: 30px;
                                }

                                .plus-minuss {
                                    width: 65px;
                                    text-align: center;
                                    font-size: 16px;
                                    border: 1px solid #ccc;
                                    border-radius: 5px;
                                    font-weight:bold;
                                }
                            </style>
                        <div class="input-container">
                            <button class="plus-minus" onclick="changeValue(-1)">-</button>
                            <input class="form-control plus-minuss" type="number" id="numberInput" value="1" min="1">
                            <button class="plus-minus" onclick="changeValue(1)">+</button>
                        </div>
                        </div>
                        <script>
                            function changeValue(change) {
                                let input = document.getElementById("numberInput");
                                let newValue = parseInt(input.value) + change;

                                // Ensure the value doesn't go below the minimum
                                if (input.hasAttribute("min")) {
                                    let minValue = parseInt(input.getAttribute("min"));
                                    if (newValue < minValue) newValue = minValue;
                                }

                                input.value = newValue;
                            }
                        </script>
                    </div>

                    <div class="row" style="margin-bottom:20px;">
                        <div class="col-12">
                            <button class="btn btn-warning add-cart" data-sessioncart="<?= $_SESSION["web_cart_id"]; ?>"><i class="fa fa-shopping-cart" aria-hidden="true"></i> add to cart</button>
                        </div>
                        <script>
                            $( document ).ready(function() {
                                $(".add-cart").click(function(){
                                    var sessioncart = $(this).data("sessioncart");
                                    var amount = $(".plus-minuss").val();
                                    var productid = "<?= $id; ?>";
                                    let url = "<?= $domainURL; ?>add-to-cart?cartSession=" + sessioncart + "&productID=" + productid + "&productQTY=" + amount;

                                    fetch(url)
                                    .then(response => response.text()) // Get response as text
                                    .then(data => {
                                        $(".cart-qty").load("<?= $domainURL; ?>data-cart");
                                        $(".plus-minuss").val(1);
                                        alert("Successful updated cart.");
                                    })
                                    .catch(error => console.error("Error:", error));
                                });

                            });
                        </script>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-12 product-view">
                    <p style="font-weight: bold;
    font-size: 18px;
    color: #fff;">Product Description</p>
                </div>
                <div class="col-lg-12 col-12 content-display" style="position: relative;
    min-height: 200px;
    height: 600px;
    overflow: hidden;">
                    <?php echo nl2br($product['product_description']); ?>
                </div>
                <div class="col-lg-12 col-12 overlay-btn" style="position: relative;
    margin-top: -30px;
    background: rgba(255, 255, 255, 0.55);
    padding: 10px;">
                    <button class="btn btn-primary read-more" style="margin-left: auto;
    margin-right: auto;
    display: block;">read more</button>
                </div>
                <script>
                    $( document ).ready(function() {
                        $(".read-more").click(function(){
                            $(this).hide();
                            $(".overlay-btn").hide();
                            $(".content-display").css({
                                "height": "",
                                "overflow": ""
                            });
                        });

                    });
                </script>
            </div>
               
            
        </div>
       
    </div>
</div>
<!-- fashion section end -->
<!-- electronic section start -->

<!-- jewellery  section start -->

<!-- jewellery  section end -->
<?php include "footer.php"; ?>