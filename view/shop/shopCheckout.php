<?php include "header.php"; ?>
<?php include "menu.php"; ?>
<!-- fashion section start -->
<div class="fashion_section" style="background: #fff;
    margin-top: -190px;
    padding-top: 30px;">
    <div id="main_slider" class="carousel slide" data-ride="carousel">
        <div class="container">
            
            
            <div class="row">
                
            <h4>Checkout</h4>

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
                    <div class="row">
                        <div class="col-lg-12" style="margin-bottom:10px;overflow-x:auto;">
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
                                        while($rowcart = $getCart->fetch_array()){

                                            $dataProduct = dataProduct($rowcart["product_id"]);

                                            ?>

                                            <tr>
                                                <td class="tbody0"><?= $x; ?></td>
                                                <td class="tbody1"><a href="<?= $domainURL; ?>product-details/<?= $rowcart["product_id"]; ?>"><img style="width: 75px;display: block;border-radius: 10px;border: 1px solid;" src="https://fusionkeymall.com/assets/images/product/<?= $dataProduct["product_image"]; ?>">(<?= $rowcart["product_id"]; ?>) <?= $rowcart["product_name"]; ?></a></td>
                                                <td class="tbody0"><?= number_format($rowcart["unit_price"], 2); ?></td>
                                                <td class="tbody0"><?= $rowcart["quantity"]; ?></td>
                                                <td class="tbody0"><?= number_format(($rowcart["unit_price"] * $rowcart["quantity"]), 2); ?></td>
                                                <td class="tbody0"><button class="btn btn-danger" onClick="window.location.href='<?= $domainURL; ?>cart'" data-id="<?= $rowcart["product_id"]; ?>" data-name="<?= $rowcart["product_name"]; ?>" data-qty="<?= $rowcart["quantity"]; ?>">EDIT</button></td>
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
                        </div>
                    </div>

                    <style>
                        .form-control:focus {
    border: 2px solid #007bff; /* Change to your preferred color */
    outline: none; /* Remove default browser outline */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Optional: Add glow effect */
}
                    </style>

                    <form action="<?= $domainURL; ?>checkout" method="post">
                        <h4 style="background: aliceblue;
    font-weight: bold;
    padding: 5px 10px;
    margin-top: 20px;
    margin-bottom: 5px;">Receiver Details</h4>

                        <div class="row">
                            <div class="col-lg-6 col-md-12" style="margin-bottom:10px;">
                                Fullname *
                                <input type="text" name="s_name" required class="form-control" placeholder="Fullname">
                            </div>
                            <div class="col-lg-6 col-md-12" style="margin-bottom:10px;">
                                Phone No. *
                                <input type="text" name="s_phone" required class="form-control" placeholder="Phone No.">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-12" style="margin-bottom:10px;">
                                Email (optional)
                                <input type="email" name="s_email" class="form-control" placeholder="Email (optional)">
                            </div>
                        </div>
                        <h4 style="background: aliceblue;
    font-weight: bold;
    padding: 5px 10px;
    margin-top: 20px;
    margin-bottom: 5px;">Address Details</h4>
                        <div class="row">
                            <div class="col-lg-6 col-md-12" style="margin-bottom:10px;">
                                Address Line 1 *
                                <input type="text" name="s_address_1" required class="form-control" placeholder="Address Line 1">
                            </div>
                            <div class="col-lg-6 col-md-12" style="margin-bottom:10px;">
                                Address Line 2 (optional)
                                <input type="text" name="s_address_2" class="form-control" placeholder="Address Line 2">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-12" style="margin-bottom:10px;">
                                Postcode *
                                <input type="text" name="s_postcode" required class="form-control" placeholder="Postcode">
                            </div>
                            <div class="col-lg-6 col-md-12" style="margin-bottom:10px;">
                                City *
                                <input type="text" name="s_city" required class="form-control" placeholder="City">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-12" style="margin-bottom:10px;">
                                Country *
                                <select class="form-control" required id="selectcountry" name="s_country">
                                    <option value="">select country</option>
                                    <?php
                                        while($countrys = $country->fetch_array()){
                                            ?>
                                            <option value="<?= $countrys["country_name"]; ?>" data-id="<?= $countrys["id"]; ?>"><?= $countrys["country_name"]; ?></option>
                                            <?php
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-12" style="margin-bottom:10px;">
                                State *
                                <select class="form-control" required id="selectstate" name="s_state">
                                    <option value="">select country first</option>
                                    
                                </select>
                            </div>
                            <script>
                                $( document ).ready(function() {
                                    $("#selectcountry").on("change", function(){
                                        var selectedOption = $(this).find(':selected'); // Get the selected option
                                        var cid = selectedOption.data("id");
                                        //alert(cid);
                                        $("#selectstate").load("<?= $domainURL; ?>country-select/" + cid);
                                    });

                                });
                            </script>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12" style="margin-bottom:10px;">
                                <button class="btn btn-success" style="margin-top: 20px;
    display: block;
    margin-left: auto;" type="submit">Next >></button>
                            </div>
                        </div>

                    </form>
                    
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