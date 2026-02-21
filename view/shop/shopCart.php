<?php include "header.php"; ?>
<?php include "menu.php"; ?>
<!-- fashion section start -->
<div class="fashion_section" style="background: #fff;
    margin-top: -190px;
    padding-top: 30px;">
    <div id="main_slider" class="carousel slide" data-ride="carousel">
        <div class="container">
            
            <div class="update-cart" style="display:none;position: fixed;
    width: 100%;
    height: 100%;
    top: 0px;
    left: 0px;
    z-index: 9999;
    background: rgba(255, 255, 255, 0.70);">

                <div class="inside-update-cart" style="position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    max-width: 500px;
    min-height: 100px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 10px 20px;
    width: calc(100% - 40px);">
                    <h4>Update Cart</h4>
                    <form action="" method="post" style="position:relative;">
                        <p>Product ID: <b><span class="u_proid"></span></b></p>
                        <p>Product Name: <b><span class="u_proname"></span></b></p>
                        <input type="hidden" name="uu_proid" class="uu_proid">
                        <p style="margin-bottom:0px;">Set New Quantity:</p>
                        <input type="number" name="uu_qty" class="form-control uu_qty" min="0" step="1" value="" style="display: block;
    width: 100px;
    margin-left: 20px;">
    <span style="color:red"><small>To delete cart item, simply put 0 in above quantity and click update button below.</small></span>
                        <button class="btn btn-success" type="submit" name="update-cart" style="margin-top: 10px;
    margin-left: 20px;">UPDATE CART</button>
                        <span class="btn btn-danger close-update" style="position: absolute;
    top: -56px;
    right: -10px;font-weight:bold;">X</span>
                    </form>
                </div>

            </div>
            <div class="row">
                
            <h4>Cart</h4>

                <div class="col-12">
                    <style>
                        .thead{
                            padding: 5px 10px;
                            color: #fff;
                            font-weight: bold;
                            text-align: center;
                            border: 1px solid #ccc;
                        }
                        .tbody0{
                            padding: 5px 10px;
                            color: #000;
                            font-weight: bold;
                            text-align: center;
                            border: 1px solid #ccc;
                        }
                        .tbody1{
                            padding: 5px 10px;
                            color: #000;
                            font-weight: bold;
                            text-align: left;
                            border: 1px solid #ccc;
                        }
                        .tbody2{
                            padding: 5px 10px;
                            color: #000;
                            font-weight: bold;
                            text-align: right;
                            border: 1px solid #ccc;
                        }
                        .btn{
                            margin-bottom: 10px;
                        }
                    </style>
                    <table style="width: 100%;
    border: 1px solid #ccc;">
                        <thead style="background: #d6ac32;">
                            <tr>
                                <td class="thead">#</td>
                                <td class="thead">Product</td>
                                <td class="thead">Unit Price (RM)</td>
                                <td class="thead">Quantity</td>
                                <td class="thead">Total Price (RM)</td>
                                <td class="thead"></td>
                            </tr>
                        </thead>
                        <tbody style="background: #fff;">
                            <?php
                            if($bilCart < "1"){
                                ?>
                                <tr>
                                    <td class="tbody0" colspan="6">no data</td>
                                </tr>
                                <?php
                            }else{
                                $x=1;
                                $tqty = "0";
                                $tprice = "0";
                                foreach($getCart as $rowcart){

                                    $dataProduct = dataProduct($rowcart["product_id"]);

                                    ?>

                                    <tr>
                                        <td class="tbody0"><?= $x; ?></td>
                                        <td class="tbody1"><a href="<?= $domainURL; ?>product-details/<?= $rowcart["product_id"]; ?>"><img style="width: 75px;display: block;border-radius: 10px;border: 1px solid;" src="https://fusionkeymall.com/assets/images/product/<?= $dataProduct["product_image"]; ?>">(<?= $rowcart["product_id"]; ?>) <?= $rowcart["product_name"]; ?></a></td>
                                        <td class="tbody0"><?= number_format($rowcart["unit_price"], 2); ?></td>
                                        <td class="tbody0"><?= $rowcart["quantity"]; ?></td>
                                        <td class="tbody0"><?= number_format(($rowcart["unit_price"] * $rowcart["quantity"]), 2); ?></td>
                                        <td class="tbody0"><button class="btn btn-info set-update" data-id="<?= $rowcart["product_id"]; ?>" data-name="<?= $rowcart["product_name"]; ?>" data-qty="<?= $rowcart["quantity"]; ?>">UPDATE</button></td>
                                    </tr>
                                    <?php
                                    $tqty += $rowcart["quantity"];
                                    $tprice += $rowcart["unit_price"] * $rowcart["quantity"];
                                    $x++;
                                }
                                ?>
                                    <tr>
                                        <td class="tbody2" colspan="3">Grand Total</td>
                                        <td class="tbody0"><?= $tqty; ?></td>
                                        <td class="tbody0"><?= number_format($tprice, 2); ?></td>
                                        <td class="tbody0"></td>
                                    </tr>
                                <?php
                                
                            }
                            ?>
                            
                        </tbody>
                    </table>
                    <?php
                        if(isset($_SESSION["membership"]) && !empty($_SESSION["membership"]))
                        {
                            ?>
                            <a href="<?= $domainURL."checkout"; ?>" class="btn btn-warning" style="display: block;
    width: fit-content;
    margin-top: 10px;
    margin-left: auto;">Checkout >></a>
                            <?php
                        }else{
                            ?>
                            <a href="<?= $domainURL."secure-account"; ?>?source=cart" class="btn btn-danger" style="display: block;
    width: fit-content;
    margin-top: 10px;
    margin-left: auto;">Login/Register >></a>
                            <?php
                        }
                        ?>
                    <script>
                        $( document ).ready(function() {
                            $(".set-update").click(function(){
                                var u_proid = $(this).data("id");
                                var u_proname = $(this).data("name");
                                var uu_qty = $(this).data("qty");

                                $(".u_proid").text(u_proid);
                                $(".uu_proid").val(u_proid);
                                $(".u_proname").text(u_proname);
                                $(".uu_qty").val(uu_qty);
                                $(".update-cart").show();
                                $('body').css('overflow', 'hidden');
                            });

                            $(".close-update").click(function(){
                                $('body').css('overflow', 'auto');
                                $(".u_proid").text("");
                                $(".uu_proid").val("");
                                $(".u_proname").text("");
                                $(".uu_qty").val("");
                                $(".update-cart").hide();
                            });

                        });
                    </script>
                </div>
            
            </div>
            
               
            
        </div>
       
    </div>
</div>
<!-- fashion section end -->
<!-- electronic section start -->

<!-- jewellery  section start -->

<!-- jewellery  section end -->
<?php include "footer.php"; ?>