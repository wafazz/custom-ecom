<?php
include "e-header-keya88.php";
include "e-menu-keya88.php";


if (isset($_GET["senangpay"]) && $_GET["senangpay"] == 'yes') {
    $_SESSION["senangpay"] = true;
} else if (isset($_GET["senangpay"]) && $_GET["senangpay"] == 'no') {
    unset($_SESSION["senangpay"]);
}

if (isset($_GET["dev"]) && !empty($_GET["dev"])) {
    $_SESSION["developer_mode"] = true;
?>
    <script>
        window.location.href = '<?= $domainURL ?>checkout';
    </script>
<?php
    exit();
} elseif (isset($_GET["remove-dev"]) && !empty($_GET["remove-dev"])) {
    unset($_SESSION["developer_mode"]);
?>
    <script>
        window.location.href = '<?= $domainURL ?>checkout';
    </script>
<?php
    exit();
}
?>
<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="<?= $domainURL ?>main"><i class="fa fa-home"></i> Home</a>
                    <span>Checkout</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->
<!-- Shop Cart Section Begin -->
<section class="shop-cart spad" style="padding-bottom: 0px !important;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="shop__cart__table">
                    <form method="post" action="<?= $domainURL ?>update-checkout">
                        <?php
                        if (isset($_SESSION["error_update"]) && !empty($_SESSION["error_update"])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION["error_update"] . '</div>';
                            unset($_SESSION["error_update"]);
                        }
                        ?>
                        <table>
                            <thead>
                                <tr>
                                    <th style="min-width:400px;text-align:center;">Product</th>
                                    <th style="min-width:200px;text-align:center;">Price</th>
                                    <th style="min-width:150px;text-align:center;">Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $subTotal = "0.00";
                                $subTotals = 0;
                                $x = 1;
                                while ($row = $query->fetch_array()) {
                                    $cartid = $row["id"];
                                    $pid = $row["p_id"];
                                    $dataProduct = GetProductDetails($pid);
                                    $pvid = $row["pv_id"];
                                    $qty = $row["quantity"];
                                    $price = $row["price"];
                                    $totalPrice = $qty * $price;
                                    $subTotals += $totalPrice;
                                    $weight = $row["weight"];
                                    $total_weight = $row["total_weight"];
                                    $currency_sign = $row["currency_sign"];
                                    $country_id = $row["country_id"];
                                    $image = "SELECT * FROM product_image WHERE product_id='$pid' ORDER BY id ASC LIMIT 1";
                                    $qimage = $conn->query($image);
                                    $rimage = $qimage->fetch_array();
                                ?>
                                    <tr>
                                        <td class="cart__product__item">
                                            <img src="<?= $domainURL ?>assets/images/products/<?= $rimage["image"] ?>" alt=""
                                                style="max-width: 80px;">
                                            <div class="cart__product__item__title">
                                                <h6>
                                                    <?= $dataProduct["name"] ?>
                                                    <?php
                                                    $variantRow = $conn->query("SELECT variant_name FROM product_variants WHERE id='$pvid'")->fetch_assoc();
                                                    if (!empty($variantRow['variant_name'])) {
                                                        echo '(' . htmlspecialchars($variantRow['variant_name']) . ')';
                                                    }
                                                    ?>
                                                </h6>
                                                <div class="rating">
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="cart__price" style="text-align:center;">
                                            <?= $currency_sign ?>
                                            <?= number_format($price, 2) ?>
                                        </td>
                                        <td class="cart__quantity" style="text-align:center;">

                                            <input type="hidden" name="productid[<?= $x; ?>]" value="<?= $pid ?>">
                                            <input type="hidden" name="cartid[<?= $x; ?>]" value="<?= $cartid ?>">
                                            <div class="pro-qty">
                                                <input type="text" name="quantity[<?= $x; ?>]" value="<?= $qty ?>">
                                            </div>
                                        </td>
                                        <td class="cart__total" style="text-align:center;">
                                            <?= $currency_sign ?>
                                            <?= number_format($totalPrice, 2) ?>
                                        </td>
                                        <td class="cart__close"><span class="icon_close" onClick="window.location.href = '<?= $domainURL ?>cart-delete?id=<?= $cartid ?>'"></span></td>
                                    </tr>
                                <?php
                                    $x++;
                                }
                                if (!isset($_POST["nextCheckout"])) {
                                ?>
                                    <tr>
                                        <td colspan="4" style="text-align:center;color:red;font-weight:bolder;">
                                        </td>

                                        <td style="text-align:center;color:red;font-weight:bolder;">
                                            <button class="btn btn-warning" type="submit" name="updateQtyCheckout">Update Cart</button>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>

                                <tr>
                                    <td class="cart__product__item" colspan="2" style="text-align: right !important"><b></b>
                                    </td>
                                    <td class="cart__product__item" style="text-align: left !important"><b>Subtotal</b></td>
                                    <td class="cart__product__item" style="font-weight:bolder;text-align:right;">
                                        <?= $currency_sign ?>
                                        <?= number_format($subTotals, 2) ?>
                                    </td>

                                    <td class="cart__close"></td>
                                </tr>

                                <tr>
                                    <td class="cart__product__item" colspan="2" style="text-align: right !important"><b></b>
                                    </td>
                                    <td class="cart__product__item" style="text-align: left !important"><b>Tax</b></td>
                                    <td class="cart__product__item" style="font-weight:bolder;text-align:right;">0.00
                                        (included)</td>

                                    <td class="cart__close"></td>
                                </tr>
                                <?php
                                if (isset($_POST["nextCheckout"])) {
                                    $postageCharge = $postage;
                                    $_SESSION["postageCharge"] = $postageCharge;
                                ?>
                                    <tr>
                                        <td class="cart__product__item" colspan="2" style="text-align: right !important"><b></b>
                                        </td>
                                        <td class="cart__product__item" style="text-align: left !important"><b>Postage Fee</b>
                                        </td>
                                        <td class="cart__product__item" style="font-weight:bolder;text-align:right;"><?= $currency_sign ?> <?= number_format($postageCharge, 2) ?></td>

                                        <td class="cart__close"></td>
                                    </tr>
                                <?php
                                } else {
                                    $postageCharge = "0.00";
                                    $_SESSION["postageCharge"] = $postageCharge;
                                }

                                $subTotal = $subTotals + $postageCharge;
                                $_SESSION["subTotal"] = $subTotal;
                                ?>



                                <tr>
                                    <td class="cart__product__item" colspan="2" style="text-align: right !important"><b></b>
                                    </td>
                                    <td class="cart__product__item" style="text-align: left !important"><b>Total to Pay</b>
                                    </td>
                                    <td class="cart__product__item" style="font-weight:bolder;text-align:right;">
                                        <?= $currency_sign ?>
                                        <?= number_format($subTotal, 2) ?>
                                    </td>

                                    <td class="cart__close"></td>
                                </tr>


                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>
<section class="checkout spad" style="padding-top: 0px !important;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
            </div>
        </div>
        <?php
        //if (isset($_SESSION["developer_mode"]) && $_SESSION["developer_mode"] == true) {

        //if (!isset($_SESSION['member_email']) && empty($_SESSION['member_email'])) {

        ?>
        <!-- <form action="<?= $domainURL ?>member-auth" method="post">
                    <div class="row">
                        <div class="col-md-5 col-12">

                            <div class="alert alert-info" role="alert">
                                <?php
                                if (isset($_SESSION['login_error'])) {
                                ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?= $_SESSION['login_error'] ?>
                                    </div>
                                <?php
                                    unset($_SESSION['login_error']);
                                }
                                ?>
                                <h4>Please Login</h4>
                                <small>Please login to continue with the checkout process.</small>
                                <label for="loginEmail" style="display:block;margin-top:10px;"></label>
                                <input id="loginEmail" class="form-control" type="email" name="loginEmail" placeholder="Enter your email">
                                <label for="loginPassword" style="display:block;margin-top:10px;"></label>
                                <input id="loginPassword" class="form-control" type="password" name="loginPassword" placeholder="Enter your password">

                                <div style="margin-top:10px;">
                                    <button class="btn btn-info" type="submit" name="loginButton">Login</button>
                                </div>
                                <div style="margin-top:10px;">
                                    Forgot your password? <a href="<?= $domainURL ?>forgot-password">Click here</a>
                                </div>



                            </div>

                        </div>
                        <div class="col-md-2 col-12">
                            <h4 style="text-align: center;margin-bottom: 15px;">or</h4>
                        </div>
                        <div class="col-md-5 col-12">
                            <div class="alert alert-primary" role="alert">
                                <?php
                                if (isset($_SESSION['register_error'])) {
                                ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?= $_SESSION['register_error'] ?>
                                    </div>
                                <?php
                                    unset($_SESSION['register_error']);
                                }

                                if (isset($_SESSION['verify_success'])) {
                                ?>
                                    <div class="alert alert-success" role="alert">
                                        <?= $_SESSION['verify_success'] ?>
                                    </div>
                                <?php
                                    unset($_SESSION['verify_success']);
                                }
                                ?>
                                <h4>Please Register</h4>
                                <small>If you don't have an account, please register to continue with the checkout process.</small>
                                <label for="registerEmail" style="display:block;margin-top:10px;"></label>
                                <input id="registerEmail" class="form-control" type="email" name="registerEmail" placeholder="Enter your email">
                                <label for="registerPassword" style="display:block;margin-top:10px;"></label>
                                <input id="registerPassword" class="form-control" type="password" name="registerPassword" placeholder="Enter your password">
                                <label for="registerPasswordConfirm" style="display:block;margin-top:10px;"></label>
                                <input id="registerPasswordConfirm" class="form-control" type="password" name="registerPasswordConfirm" placeholder="Confirm your password">

                                <div style="margin-top:10px;">
                                    <button class="btn btn-info" type="submit" name="registerButton">Register</button>
                                </div>


                            </div>
                        </div>

                    </div>
                </form> -->
        <?php


        //} else {

        if (isset($_POST["nextCheckout"])) {
        ?>
            <form id="payment-form" class="checkout__form" method="POST">
            <?php
        } else {
            ?>
                <form action="<?= $domainURL ?>checkout" class="checkout__form" method="POST"> <?php
                                                                                            }

                                                                                                ?>

                <div class="row">
                    <div class="col-lg-8">
                        <h5>Billing detail</h5>

                        <?php

                        if (isset($_SESSION['member_email']) && !empty($_SESSION['member_email'])) {
                        ?>

                            <h6 class="alert alert-info" role="alert">You are logged in as <?= htmlspecialchars($_SESSION['member_email']) ?> - [ <a href="<?= $domainURL ?>member-logout">Logout</a> ]</h6>

                        <?php
                        }
                        ?>


                        <style>
                            .site-btns {
                                margin-top: 20px;
                                width: 100%;
                                padding: 14px;
                                font-size: 16px;
                                border-radius: 10px;
                                border: none;
                                cursor: pointer;
                                background: #4f46e5;
                                color: #fff;
                                display: block;
                                text-align: center;
                                font-weight: bold;
                            }
                            .site-btns:hover {
                                color: #e5e2e2 !important;
                            }
                        </style>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="checkout__form__input">
                                    <p>First Name <span>*</span></p>
                                    <input type="text" id="fname" name="fname" value="<?= isset($_SESSION["fname"]) ? $_SESSION["fname"] : ''; ?>" <?= isset($_POST["nextCheckout"]) ? 'readonly' : 'required'; ?>>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="checkout__form__input">
                                    <p>Last Name <span>*</span></p>
                                    <input type="text" id="lname" name="lname" value="<?= isset($_SESSION["lname"]) ? $_SESSION["lname"] : ''; ?>" <?= isset($_POST["nextCheckout"]) ? 'readonly' : 'required'; ?>>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="checkout__form__input">
                                    <p>Country <span>*</span></p>
                                    <b>
                                        <?= $data["name"] ?>
                                    </b>
                                </div>
                                <div class="checkout__form__input">
                                    <p>Address <span>*</span></p>
                                    <input type="text" placeholder="Street Address" id="add_1" name="add_1" value="<?= isset($_SESSION["add_1"]) ? $_SESSION["add_1"] : ''; ?>" <?= isset($_POST["nextCheckout"]) ? 'readonly' : 'required'; ?>>
                                    <input type="text" placeholder="Apartment. suite, unite ect ( optinal )" id="add_2"
                                        name="add_2" value="<?= isset($_SESSION["add_2"]) ? $_SESSION["add_2"] : ''; ?>" <?= isset($_POST["nextCheckout"]) ? 'readonly disabled' : ''; ?>>
                                </div>
                                <div class="checkout__form__input">
                                    <p>Postcode/Zip <span>*</span></p>
                                    <input type="text" id="postcode" name="postcode" value="<?= isset($_SESSION["postcode"]) ? $_SESSION["postcode"] : ''; ?>" <?= isset($_POST["nextCheckout"]) ? 'readonly' : 'required'; ?>>
                                </div>
                                <div class="checkout__form__input">
                                    <p>Town/City <span>*</span></p>
                                    <input type="text" id="city" name="city" value="<?= isset($_SESSION["city"]) ? $_SESSION["city"] : ''; ?>" <?= isset($_POST["nextCheckout"]) ? 'readonly' : 'required'; ?>>
                                </div>
                                <div class="checkout__form__input">
                                    <p>Province/State <span>*</span></p>
                                    <?php
                                    if ($country == "1") {
                                    ?>
                                        <select id="state" name="state" style="height: 50px !important;
    margin-bottom: 25px;
    border: 1px solid #e1e1e1;
    border-radius: 2px;
    width: 100% !important;" <?= isset($_POST["nextCheckout"]) ? 'readonly' : 'required'; ?>>
                                            <option value="" <?= isset($_POST["nextCheckout"]) ? '' : 'selected'; ?> disabled>select state</option>
                                            <?php
                                            while ($rowstate = $myState->fetch_array()) {

                                                if ($_SESSION["state"] == $rowstate["name"]) {
                                            ?>
                                                    <option selected value="<?= $rowstate["name"] ?>"><?= $rowstate["name"] ?></option>
                                                <?php
                                                } else {
                                                ?>
                                                    <option value="<?= $rowstate["name"] ?>"><?= $rowstate["name"] ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <input type="text" id="state" name="state" value="<?= isset($_SESSION["state"]) ? $_SESSION["state"] : ''; ?>" <?= isset($_POST["nextCheckout"]) ? 'readonly' : 'required'; ?>>
                                    <?php
                                    }
                                    ?>

                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="checkout__form__input">
                                    <p>Phone <span>*</span></p>
                                    <input type="text" id="ophone" name="ophone" value="<?= isset($_SESSION["ophone"]) ? $_SESSION["ophone"] : ''; ?>" <?= isset($_POST["nextCheckout"]) ? 'readonly' : 'required'; ?>>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="checkout__form__input">
                                    <p>Email <span>*</span></p>
                                    <?php
                                    $emailUser = '';
                                    if (isset($_SESSION['member_email']) && !empty($_SESSION['member_email'])) {
                                        $emailUser = $_SESSION['member_email'];
                                    }
                                    ?>
                                    <input type="email" id="oemail" name="oemail" value="<?= isset($_SESSION["oemail"]) ? $_SESSION["oemail"] : (isset($emailUser) ? $emailUser : ''); ?>" <?= isset($_POST["nextCheckout"]) ? 'readonly' : 'required'; ?>>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="checkout__form__input">
                                    <p>Oder notes <span>*</span></p>
                                    <input type="text" id="remark" name="remark" value="<?= isset($_SESSION["remark"]) ? $_SESSION["remark"] : ''; ?>"
                                        placeholder="Note about your order, e.g, special noe for delivery" <?= isset($_POST["nextCheckout"]) ? 'readonly' : ''; ?>>
                                </div>
                            </div>
                        </div>



                    </div>
                    <div class="col-lg-4">
                        <div class="checkout__order">
                            <?php
                            if (isset($_POST["nextCheckout"])) {
                                if (isset($_SESSION["senangpay"]) && $_SESSION["senangpay"] == true) {
                            ?>

                                <?php
                                }
                                ?>

                                <!-- <span onclick="window.location.href='<?= $domainURL ?>proceed-payment'" class="btn btn-info"><img src="https://app.senangpay.my/public/images/UPD-btn-pay-solid.png"></span> -->
                                <p>By clicking "<b>Pay Now</b>" button below, you're agree to our "PURCHASE" terms and conditions.</p>
                                <a href="<?= $domainURL ?>proceed-payment" class="site-btns">Pay Now</a>

                                <!-- <p style="font-weight:bold;color:red;">Note: For end user please dont use SenangPay above for payment. Now we in the middle of running/conducting testing issues. End user, Please use payment below for make payment.</p> -->
                                <!-- <p style="font-weight:bold;color:green;">Note: For FPX Payment & E-Wallet (T&G, Boost, GrabPay, ShopeePay) please use SenangPay above.</p>
                                <p style="font-weight:bold;color:green;">For Credit/Debit Card payment please use the payment method below.</p> -->
                                <div>
                                    <!-- <h5 style="font-weight:bold;margin-bottom:20px;">Please select one of the payment methods below to proceed with placing your order.</h5> -->
                                    <?php if ($country == "1" && isset($_SESSION["test"])): ?>
                                        <label for="fpxBillplz" style="display:block;">
                                            <input type="radio" id="fpxBillplz" name="pay" value="fpxBillplz"> Online Banking (FPX) - BillPlz
                                        </label>
                                    <?php endif; ?>
                                    <?php if ($country == "1"): ?>
                                        <!-- <label for="fpx" style="display:block;">
                                            <input type="radio" id="fpx" name="pay" value="fpx"> Online Banking (FPX) - Stripe
                                        </label> -->
                                    <?php endif; ?>
                                    <label for="cc" style="display:block;">
                                        <!-- <input type="hidden" id="cc" name="pay" value="cc"> -->
                                    </label>
                                </div>
                                <!-- <button type="submit" class="site-btn">Place Order (
                                    <?= $currency_sign ?>
                                    <?= number_format($subTotal, 2) ?>) via Credit/Debit Card<br>(STRIPE CHANNEL)
                                </button> -->
                            <?php


                            } else {
                            ?>
                                <p>Please click "<b>Next >></b>" button below to proceed for shipping fee calculation.</p>
                                <button type="submit" class="site-btns" name="nextCheckout">Next >></button>
                            <?php
                            }
                            ?>

                        </div>
                    </div>
                </div>
                </form>

                <?php
                //}

                ?>



                <?php
                if (isset($_POST["nextCheckout"])) {
                ?>
                    <script>
                        const stripe = Stripe("<?= $stripesRow["publish_key"] ?>"); // Replace with your real publishable key

                        document.getElementById('payment-form').addEventListener('submit', function(e) {
                            e.preventDefault();

                            const payMethod = document.querySelector('input[name="pay"]:checked');
                            if (!payMethod) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Missing Payment Method',
                                    text: 'Please select a payment method.'
                                });
                                return;
                            }

                            const selectedMethod = payMethod.value; // 'fpx' or 'cc'
                            const stripeMethod = selectedMethod === 'cc' ? 'card' : 'fpx';

                            fetch('create-fpx-session.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        //amount: <?= $subTotal * 100 ?>, // convert to cents
                                        fname: document.getElementById('fname').value,
                                        lname: document.getElementById('lname').value,
                                        add_1: document.getElementById('add_1').value,
                                        add_2: document.getElementById('add_2').value,
                                        city: document.getElementById('city').value,
                                        state: document.getElementById('state').value,
                                        postcode: document.getElementById('postcode').value,
                                        ophone: document.getElementById('ophone').value,
                                        oemail: document.getElementById('oemail').value,
                                        remark: document.getElementById('remark').value,
                                        method: stripeMethod
                                    })
                                })
                                .then(res => res.json())
                                .then(session => {
                                    if (session.id) {
                                        return stripe.redirectToCheckout({
                                            sessionId: session.id
                                        });
                                    } else {
                                        alert("Failed to start payment: " + session.error);
                                    }
                                })
                                .catch(error => {
                                    console.error("Stripe error:", error);
                                    alert("Connection to payment gateway failed.");
                                });
                        });
                    </script>
                <?php
                } else {
                ?>
                    <script>
                        $(document).ready(function() {
                            $('#postcode').autocomplete({
                                source: function(request, response) {
                                    $.ajax({
                                        url: 'autocomplete-postcode-city.php',
                                        dataType: 'json',
                                        data: {
                                            q: request.term
                                        },
                                        success: function(data) {
                                            response(data);
                                        }
                                    });
                                },
                                minLength: 2,
                                select: function(event, ui) {
                                    $('#postcode').val(ui.item.postcode);
                                    $('#city').val(ui.item.city);
                                    return false;
                                }
                            }).autocomplete("instance")._renderItem = function(ul, item) {
                                return $("<li>")
                                    .append("<div><strong>" + item.postcode + "</strong> - " + item.city + "</div>")
                                    .appendTo(ul);
                            };
                        });
                    </script>
                <?php
                }
                ?>



    </div>
</section>
<!-- Shop Cart Section End -->

<?php
include "e-footer-keya88.php";
?>