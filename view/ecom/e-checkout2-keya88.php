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
                    <style>
                        * {
                            box-sizing: border-box;
                            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                        }

                        .checkout-card {
                            background: #fff;
                            border-radius: 16px;
                            padding: 26px;
                            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.07);
                            margin-bottom: 40px;
                        }

                        .checkout-title {
                            font-size: 22px;
                            font-weight: 600;
                            margin-bottom: 20px;
                            color: #111;
                        }

                        .table-responsive {
                            overflow-x: auto;
                        }

                        .checkout-table {
                            width: 100%;
                            min-width: 900px;
                            border-collapse: collapse;
                        }

                        .checkout-table thead th {
                            padding: 14px;
                            font-size: 13px;
                            text-transform: uppercase;
                            letter-spacing: .5px;
                            color: #666;
                            border-bottom: 1px solid #eee;
                            text-align: center;
                        }

                        .checkout-table tbody td {
                            padding: 18px 14px;
                            border-bottom: 1px solid #f2f2f2;
                            vertical-align: middle;
                            font-size: 15px;
                        }

                        .cart__product__item {
                            display: flex;
                            align-items: center;
                            gap: 14px;
                        }

                        .cart__product__item img {
                            width: 72px;
                            height: 72px;
                            border-radius: 12px;
                            object-fit: cover;
                            background: #f5f5f5;
                        }

                        .cart__product__item__title h6 {
                            font-size: 15px;
                            margin: 0 0 6px;
                            font-weight: 600;
                            color: #222;
                        }

                        .cart__product__item__title .rating i {
                            font-size: 12px;
                            color: #f5b50a;
                        }

                        .cart__price,
                        .cart__total {
                            font-weight: 500;
                            color: #222;
                        }

                        .pro-qty input {
                            width: 70px;
                            height: 40px;
                            border-radius: 10px;
                            border: 1px solid #ddd;
                            text-align: center;
                            font-size: 14px;
                        }

                        .cart__close span {
                            font-size: 18px;
                            cursor: pointer;
                            color: #999;
                            transition: .2s;
                        }

                        .cart__close span:hover {
                            color: #e11d48;
                        }

                        .summary-row td {
                            border-top: 1px solid #eee;
                            font-weight: 600;
                            font-size: 16px;
                        }

                        .total-row td {
                            font-size: 18px;
                            font-weight: 700;
                        }

                        .btn-update {
                            background: #f59e0b;
                            color: #fff;
                            border: none;
                            padding: 10px 20px;
                            border-radius: 10px;
                            cursor: pointer;
                            font-size: 14px;
                        }

                        .btn-update:hover {
                            background: #d97706;
                        }

                        .alert {
                            padding: 14px;
                            border-radius: 10px;
                            margin-bottom: 18px;
                        }

                        .alert-danger {
                            background: #fee2e2;
                            color: #991b1b;
                        }
                    </style>


                    <div class="checkout-card">
                        <div class="checkout-title">Order Summary</div>

                        <form method="post" action="<?= $domainURL ?>update-checkout">

                            <?php
                            if (isset($_SESSION["error_update"]) && !empty($_SESSION["error_update"])) {
                                echo '<div class="alert alert-danger">' . $_SESSION["error_update"] . '</div>';
                                unset($_SESSION["error_update"]);
                            }
                            ?>

                            <div class="table-responsive">
                                <table class="checkout-table">
                                    <thead>
                                        <tr>
                                            <th style="text-align:left">Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $subTotals = 0;
                                        $x = 1;

                                        while ($row = $query->fetch_array()) {
                                            $cartid = $row["id"];
                                            $pid = $row["p_id"];
                                            $pvid = $row["pv_id"];
                                            $qty = $row["quantity"];
                                            $price = $row["price"];
                                            $totalPrice = $qty * $price;
                                            $subTotals += $totalPrice;
                                            $currency_sign = $row["currency_sign"];

                                            $dataProduct = GetProductDetails($pid);
                                            $qimage = $conn->query("SELECT * FROM product_image WHERE product_id='$pid' ORDER BY id ASC LIMIT 1");
                                            $rimage = $qimage->fetch_array();
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="cart__product__item">
                                                        <img src="<?= $domainURL ?>assets/images/products/<?= $rimage["image"] ?>">
                                                        <div class="cart__product__item__title">
                                                            <h6><?= $dataProduct["name"] ?><?php
                                                            $variantRow = $conn->query("SELECT variant_name FROM product_variants WHERE id='$pvid'")->fetch_assoc();
                                                            if (!empty($variantRow['variant_name'])) {
                                                                echo ' (' . htmlspecialchars($variantRow['variant_name']) . ')';
                                                            }
                                                            ?></h6>
                                                            <div class="rating">
                                                                <i class="fa fa-star"></i>
                                                                <i class="fa fa-star"></i>
                                                                <i class="fa fa-star"></i>
                                                                <i class="fa fa-star"></i>
                                                                <i class="fa fa-star"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td style="text-align:center;">
                                                    <?= $currency_sign ?> <?= number_format($price, 2) ?>
                                                </td>

                                                <td style="text-align:center;">
                                                    <input type="hidden" name="productid[<?= $x ?>]" value="<?= $pid ?>">
                                                    <input type="hidden" name="cartid[<?= $x ?>]" value="<?= $cartid ?>">
                                                    <div class="pro-qty">
                                                        <input type="text" name="quantity[<?= $x ?>]" value="<?= $qty ?>">
                                                    </div>
                                                </td>

                                                <td style="text-align:center;">
                                                    <?= $currency_sign ?> <?= number_format($totalPrice, 2) ?>
                                                </td>

                                                <td class="cart__close">
                                                    <span class="icon_close"
                                                        onclick="window.location.href='<?= $domainURL ?>cart-delete?id=<?= $cartid ?>'"></span>
                                                </td>
                                            </tr>
                                        <?php
                                            $x++;
                                        }
                                        ?>

                                        <tr class="summary-row">
                                            <td colspan="3" style="text-align:right;">Subtotal</td>
                                            <td style="text-align:right;">
                                                <?= $currency_sign ?> <?= number_format($subTotals, 2) ?>
                                            </td>
                                            <td></td>
                                        </tr>

                                        <tr class="summary-row">
                                            <td colspan="3" style="text-align:right;">Tax</td>
                                            <td style="text-align:right;">0.00 (included)</td>
                                            <td></td>
                                        </tr>

                                        <?php
                                        if (isset($_POST["nextCheckout"])) {
                                            $postageCharge = $postage;
                                        } else {
                                            $postageCharge = 0;
                                        }
                                        $_SESSION["postageCharge"] = $postageCharge;
                                        $grandTotal = $subTotals + $postageCharge;
                                        $_SESSION["subTotal"] = $grandTotal;
                                        ?>

                                        <tr class="summary-row">
                                            <td colspan="3" style="text-align:right;">Postage Fee</td>
                                            <td style="text-align:right;">
                                                <?= $currency_sign ?> <?= number_format($postageCharge, 2) ?>
                                            </td>
                                            <td></td>
                                        </tr>

                                        <tr class="summary-row total-row">
                                            <td colspan="3" style="text-align:right;">Total to Pay</td>
                                            <td style="text-align:right;">
                                                <?= $currency_sign ?> <?= number_format($grandTotal, 2) ?>
                                            </td>
                                            <td></td>
                                        </tr>

                                        <?php if (!isset($_POST["nextCheckout"])) { ?>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td style="text-align:right;">
                                                    <button class="btn-update" type="submit" name="updateQtyCheckout">
                                                        Update Cart
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>

                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
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
                    }
                </style>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="checkout__order">
                            <h5>Billing detail</h5>

                            <?php

                            if (isset($_SESSION['member_email']) && !empty($_SESSION['member_email'])) {
                            ?>

                                <h6 class="alert alert-info" role="alert">You are logged in as <?= htmlspecialchars($_SESSION['member_email']) ?> - [ <a href="<?= $domainURL ?>member-logout">Logout</a> ]</h6>

                            <?php
                            }
                            ?>



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

                            </div>

                        </div>



                    </div>
                    <div class="col-lg-6">
                        <div class="checkout__order">
                            <div class="row">
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
                            <?php
                            if (isset($_POST["nextCheckout"])) {
                                if (isset($_SESSION["senangpay"]) && $_SESSION["senangpay"] == true) {
                            ?>

                                <?php
                                }
                                ?>

                                <span onclick="window.location.href='<?= $domainURL ?>proceed-payment'" class="btn btn-info"><img src="https://app.senangpay.my/public/images/UPD-btn-pay-solid.png"></span>
                                <a href="<?= $domainURL ?>proceed-payment" class="btn btn-info" style="margin-bottom: 30px;
    margin-top: 20px;
    display: block;
    font-size: 20px;
    font-weight: bold;">Pay Now (FPX & E-Wallet Payment) via SenangPay</a>
                                <!-- <p style="font-weight:bold;color:red;">Note: For end user please dont use SenangPay above for payment. Now we in the middle of running/conducting testing issues. End user, Please use payment below for make payment.</p> -->
                                <p style="font-weight:bold;color:green;">Note: For FPX Payment & E-Wallet (T&G, Boost, GrabPay, ShopeePay) please use SenangPay above.</p>
                                <p style="font-weight:bold;color:green;">For Credit/Debit Card payment please use the payment method below.</p>
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
                                        <input type="hidden" id="cc" name="pay" value="cc">
                                    </label>
                                </div>
                                <button type="submit" class="site-btn">Place Order (
                                    <?= $currency_sign ?>
                                    <?= number_format($subTotal, 2) ?>) via Credit/Debit Card<br>(STRIPE CHANNEL)
                                </button>
                            <?php


                            } else {
                            ?>
                                <button type="submit" class="site-btns" name="nextCheckout">Next & Calculate Shipping Cost</button>
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