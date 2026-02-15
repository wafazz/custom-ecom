<?php include "header.php"; ?>
<?php include "menu.php"; ?>
<!-- fashion section start -->
<div class="fashion_section" style="background: #fff;
    margin-top: -190px;
    padding-top: 30px;">
    <div id="main_slider" class="carousel slide" data-ride="carousel">
        <div class="container">
            
            
            <div class="row">
                <style>
                    .login-form{
                        display: block;
                        max-width: 500px;
                        width: calc(100% - 40px);
                        margin-left: auto;
                        margin-right: auto;
                        padding: 10px 20px;
                        border: 1px solid;
                        border-radius: 5px;
                    }

                    .login-form ul li{
                        display: inline-block;
                        float: left;
                        background: #f39b07;
                        padding: 5px 10px;
                        cursor: pointer;
                        color: #fff;
                        border-left: 1px solid #f39b07;
                        border-right: 1px solid #f39b07;
                        border-bottom: 1px solid #f39b07;
                    }
                    .activee{
                        background: #fff !important;
                        color:#f39b07 !important;
                        font-weight: bold !important;;
                        cursor: no-drop !important;;
                    }
                </style>
                <div class="col-12">
                    
                    <div class="login-form">
                    <div class="row" style="margin-bottom:20px;">
                        <div class="col-12">
                        <ul>
                            <li class="lgn activee">TAC</li>
                        </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                        <?php
                        if(isset($_SESSION["errorTac"])){
                            $loginShow = "";
                            $regShow = 'style="display:none;"';
                            ?>
                            <style>
                                .errorSign li{
                                    background: red !important;
    display: block !important;
    width: 100% !important;
    float:none;
                                }
                                .errorSign{
                                    margin-bottom:10px !important;
                                }
                            </style>
                            <ul class="errorSign">
                                <?= $_SESSION["errorTac"]; ?>
                            </ul>
                            <?php
                            unset($_SESSION["errorTac"]);
                        }else if(isset($_SESSION["errorReg"])){
                            $regShow = "";
                            $loginShow = 'style="display:none;"';
                            ?>
                            <style>
                                .errorSign li{
                                    background: red !important;
    display: block !important;
    width: 100% !important;
    float:none;
                                }
                                .errorSign{
                                    margin-bottom:10px !important;
                                }
                            </style>
                            <ul class="errorSign">
                                <?= $_SESSION["errorReg"]; ?>
                            </ul>
                            <?php
                            unset($_SESSION["errorReg"]);
                        }else{

                            $loginShow = "";
                            $regShow = 'style="display:none;"';
                        }
                        ?>
                        <style>
                            #pin-input {
                                display: flex;
                                justify-content: center;
                                align-items: center;
                            }
                            .pin-input-container {
                                display: flex;
                                gap: 10px;
                            }
                            .pin-input {
                                width: 25px;
                                height: 25px;
                                font-size: 18px;
                                text-align: center;
                                border: 2px solid #333;
                                border-radius: 5px;
                            }
                            /* Remove number input arrows */
                            input[type="number"]::-webkit-inner-spin-button,
                            input[type="number"]::-webkit-outer-spin-button {
                                -webkit-appearance: none;
                                margin: 0;
                            }

                            input[type="number"] {
                                -moz-appearance: textfield;
                            }
                        </style>
                        <form action="<?= $domainURL; ?>verify_phone" method="post" class="form-login"  <?= $loginShow; ?>>
                        <p>Please check your whatsapp for the TAC number. We only sent the TAC verification code to whatsapp.</p>
                        <p>Your TAC will expired in <span id="countdown" style="font-weight:bold;color:red;">10:00</span></p>
                        <div id="pin-input">
                            <div class="pin-input-container">
                                <input type="number" name="pin[]" class="pin-input pin-input1" min="0" max="9" step="1" maxlength="1">
                                <input type="number" name="pin[]" class="pin-input pin-input2"  min="0" max="9" step="1" maxlength="1">
                                <input type="number" name="pin[]" class="pin-input pin-input3"  min="0" max="9" step="1" maxlength="1">
                                <input type="number" name="pin[]" class="pin-input pin-input4"  min="0" max="9" step="1" maxlength="1">
                                <input type="number" name="pin[]" class="pin-input pin-input5"  min="0" max="9" step="1" maxlength="1">
                                <input type="number" name="pin[]" class="pin-input pin-input6"  min="0" max="9" step="1" maxlength="1">
                            </div>
                        </div>
                        

                        <button type="submit" name="submitTAC" class="btn btn-warning submit-button" style="display: block;
    margin-left: auto;
    margin-right: auto;
    margin-top: 20px;">Verify Now</button>

                        <script>
                            $(document).ready(function () {
                                let expirationTime = new Date(<?=  date("Y, m, d, h, i, s", strtotime($_SESSION["expired"])); ?>).getTime(); 
                                let redirectURL = "<?= $domainURL; ?>secure-account"; // Change to your desired URL


                                function updateCountdown() {
                                    let currentTime = new Date().getTime();
                                    let timeLeft = expirationTime - currentTime;

                                    let minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                                    let seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                                    if (minutes <= 0 && seconds <= 0) {
                                        window.location.href = redirectURL;
                                        clearInterval(countdownTimer);
                                        return;
                                    }

                                    $("#countdown").text((minutes < 10 ? "0" : "") + minutes + " Min and " + (seconds < 10 ? "0" : "") + seconds + " Sec");
                                }


                                // Initial call to set countdown immediately
                                updateCountdown();
                                // Update countdown every second
                                let countdownTimer = setInterval(updateCountdown, 1000);
                                $(".pin-input").on("input", function () {
                                    let $this = $(this);
                                    let value = $this.val();


                                    if (value.length === 1) {
                                        let nextInput = $this.next(".pin-input");
                                        if (nextInput.length) {
                                            nextInput.focus();
                                        } else {
                                            $(".submit-button").focus(); // Move focus to submit button on last input
                                        }
                                    }
                                });

                                $(".pin-input").on("keydown", function (e) {
                                    let $this = $(this);

                                    if (e.key === "Backspace" && $this.val() === "") {
                                        $this.prev(".pin-input").focus();
                                    }
                                });
                            });
                        </script>

                        </form>
                        </div>
                    </div>
                    <script>
                        $( document ).ready(function() {
                            $(".lgn").click(function(){
                                $(this).addClass( "activee");
                                $(".reg").removeClass( "activee");
                                $(".form-login").show();
                                $(".form-register").hide();
                            });
                            $(".reg").click(function(){
                                $(this).addClass( "activee");
                                $(".lgn").removeClass( "activee");
                                $(".form-login").hide();
                                $(".form-register").show();
                            });


                        });
                    </script>
                    
                    </div>
                    
                    
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