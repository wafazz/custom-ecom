<?php
namespace Shop;

require_once __DIR__ . '/../../config/mainConfig.php';

class AuthController {
    public function index() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $pageid = 2;
        $pageName = "MEMBERSHIP";
        if(!isset($_SESSION["referby"])){
            header("Location: ".$domainURL."referby/1");
        }else{
            if(!isset($_SESSION["web_cart_id"])){
                $sessionParams = $_SESSION["referby"]."_".time()."_".rand("1000000","9999999");
                $_SESSION["web_cart_id"] = "shop_".time()."_".$_SESSION["referby"]."_".hash('sha256', $sessionParams);
            }
        }

        $timezone = "Asia/Kuala_Lumpur";
        if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
        $dates = date("Y-m-d H:i:s");

        $cartId = $_SESSION["web_cart_id"];

        
        $userData = userData($_SESSION["referby"]);

        $theUserId = $userData["id"];

        $sql = "SELECT * FROM category WHERE `status`='1' AND assigned_user LIKE '%[$theUserId]%'";
        $result = $conn->query($sql);
        $category = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $category[] = $row;
            }
        } else {
            echo "No category found.";
        }

        if(isset($_SESSION["membership"]) && !empty($_SESSION["membership"]))
        {
            $dataMember = memberData($_SESSION["membership"]);
        }

        if(isset($_GET["source"]) && $_GET["source"] == "cart"){
            $nextUrl = $domainURL."cart";
        }else{
            $nextUrl = $domainURL."my-account";
        }

        $_SESSION["nexturl"] = $nextUrl;

        if(isset($_POST["submitRegister"])){
            $fullname = $conn->real_escape_string($_POST["full-name"]);
            $ccode = $conn->real_escape_string($_POST["ccode"]);
            $phone = $conn->real_escape_string($_POST["phone"]);
            $email = $conn->real_escape_string($_POST["email"]);
            $password = $conn->real_escape_string($_POST["password"]);
            $passwords = hash('sha256', $password);
            $cpassword = $conn->real_escape_string($_POST["cpassword"]);
            $cpasswords = hash('sha256', $cpassword);

            if($ccode == "60"){
                $country = "Malaysia";
            }else if($ccode == "65"){
                $country = "Singapore";
            }

            $errorReg = "";
            $verifyEmail = $conn->query("SELECT * FROM membership WHERE email='$email'");
            if($verifyEmail->num_rows >= "1"){
                $errorReg .= "<li class='error'>Email already been used.</li>";
            }else{
                $verifyEmailM = $conn->query("SELECT * FROM member WHERE email='$email'");
                if($verifyEmailM->num_rows >= "1"){
                    $errorReg .= "<li class='error'>Email already been used.</li>";
                }
            }

            $verifyPhone = $conn->query("SELECT * FROM membership WHERE phone_c_code='$ccode' AND phone_no='$phone'");
            if($verifyPhone->num_rows >= "1"){
                $errorReg .= "<li class='error'>Phone no. already been used.</li>";
            }else{
                $phoneF = $ccode.$phone;
                $verifyPhoneM = $conn->query("SELECT * FROM member WHERE m_phone LIKE '%$phoneF%'");
                if($verifyPhoneM->num_rows >= "1"){
                    $errorReg .= "<li class='error'>Phone no. already been used.</li>";
                }
            }

            if($passwords != $cpasswords && empty($password) && empty($cpassword)){
                $errorReg .= "<li class='error'>Password cannot be blank. Password & Confirm Password must same.</li>";
            }

            if(!empty($errorReg)){
                function generateRandomString($length = 10) {
                    return substr(bin2hex(random_bytes($length)), 0, $length);
                }

                $regHex = generateRandomString(12);
                $addMembership = $conn->query("INSERT INTO `membership`(`id`, `reg_hex`, `name`, `email`, `password`, `phone_c_code`, `phone_no`, `address_1`, `address_2`, `city`, `postcode`, `state`, `country`, `date_added`, `date_update`, `date_delete`, `status`, `phone_verify`, `email_verify`, `membership_stage`, `referral`) VALUES (NULL,'$regHex','$fullname','$email','$passwords','$ccode','$phone','','','','','','$country','$dates','$dates','','0','0','0','0','$theUserId')");

                if($addMembership){
                    $getMembership = $conn->query("SELECT * FROM membership WHERE reg_hex='$regHex'");
                    $getMemberships = $getMembership->fetch_array();
                    $_SESSSION["membership"] = $getMemberships["id"];

                    ?>
                    <script>
                        alert("Registration successful.");
                        window.location.href = "<?= $_SESSION["nexturl"]; ?>";
                    </script>
                    <?php
                }
            }
        }

        
        //echo $nextUrl;

        // Close the connection
        $conn->close();

        //echo $bilCart;

        //echo "Product Page for id: ".$id;
        require_once __DIR__ . '/../../view/shop/shopAuth.php';
    }

    public function index2() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $pageid = 2;
        $pageName = "MEMBERSHIP";
        if(!isset($_SESSION["referby"])){
            header("Location: ".$domainURL."referby/1");
        }else{
            if(!isset($_SESSION["web_cart_id"])){
                $sessionParams = $_SESSION["referby"]."_".time()."_".rand("1000000","9999999");
                $_SESSION["web_cart_id"] = "shop_".time()."_".$_SESSION["referby"]."_".hash('sha256', $sessionParams);
            }
        }

        $timezone = "Asia/Kuala_Lumpur";
        if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
        $dates = date("Y-m-d H:i:s");

        $cartId = $_SESSION["web_cart_id"];

        
        $userData = userData($_SESSION["referby"]);

        $theUserId = $userData["id"];

        

        
            $fullname = $conn->real_escape_string($_POST["full-name"]);
            //echo $fullname;
            $ccode = $conn->real_escape_string($_POST["ccode"]);
            $phone = $conn->real_escape_string($_POST["phone"]);
            $email = $conn->real_escape_string($_POST["email"]);
            $password = $conn->real_escape_string($_POST["password"]);
            $passwords = hash('sha256', $password);
            $cpassword = $conn->real_escape_string($_POST["cpassword"]);
            $cpasswords = hash('sha256', $cpassword);

            if($ccode == "60"){
                $country = "Malaysia";
            }else if($ccode == "65"){
                $country = "Singapore";
            }

            $errorReg = "";
            $verifyEmail = $conn->query("SELECT * FROM membership WHERE email='$email'");
            if($verifyEmail->num_rows >= "1"){
                $errorReg .= "<li class='error'>Email already been used.</li>";
            }else{
                $verifyEmailM = $conn->query("SELECT * FROM member WHERE email='$email'");
                if($verifyEmailM->num_rows >= "1"){
                    $errorReg .= "<li class='error'>Email already been used.</li>";
                }
            }

            $verifyPhone = $conn->query("SELECT * FROM membership WHERE phone_c_code='$ccode' AND phone_no='$phone'");
            if($verifyPhone->num_rows >= "1"){
                $errorReg .= "<li class='error'>Phone no. already been used.</li>";
            }else{
                $phoneF = $ccode.$phone;
                $verifyPhoneM = $conn->query("SELECT * FROM member WHERE m_phone LIKE '%$phoneF%'");
                if($verifyPhoneM->num_rows >= "1"){
                    $errorReg .= "<li class='error'>Phone no. already been used.</li>";
                }
            }

            if($passwords != $cpasswords && empty($password) && empty($cpassword)){
                $errorReg .= "<li class='error'>Password cannot be blank. Password & Confirm Password must same.</li>";
            }

            if(empty($errorReg)){
                function generateRandomString($length = 10) {
                    return substr(bin2hex(random_bytes($length)), 0, $length);
                }

                if(isset($_SESSION["sponsorMember"]) && !empty($_SESSION["sponsorMember"])){
                    $sponsorMember = $_SESSION["sponsorMember"];
                }else{
                    $sponsorMember = "0";
                }

                $regHex = generateRandomString(12);
                $addMembership = $conn->query("INSERT INTO `membership`(`id`, `reg_hex`, `name`, `email`, `password`, `phone_c_code`, `phone_no`, `address_1`, `address_2`, `city`, `postcode`, `state`, `country`, `date_added`, `date_update`, `date_delete`, `status`, `phone_verify`, `email_verify`, `membership_stage`, `referral`, `referral_membership`) VALUES (NULL,'$regHex','$fullname','$email','$passwords','$ccode','$phone','','','','','','$country','$dates','$dates',NULL,'0','0','0','0','$theUserId','$sponsorMember')");

                if($addMembership){
                    $getMembership = $conn->query("SELECT * FROM membership WHERE reg_hex='$regHex'");
                    $getMemberships = $getMembership->fetch_array();

                    if($getMemberships["phone_verify"] == "0"){
                        ?>
                        <script>
                            alert("Registration successful. You will redirect to login page.");
                            window.location.href = "<?= $domainURL; ?>verify_phone";
                        </script>
                        <?php
                    }else{
                        $_SESSION["membership"] = $getMemberships["id"];
                        ?>
                        <script>
                            alert("Registration successful. You will redirect to login page.");
                            window.location.href = "<?= $domainURL; ?>verify_phone";
                        </script>
                        <?php
                    }

                    
                }
            }else{
                $_SESSION["errorReg"] = $errorReg;
                header("Location: ".$domainURL."secure-account");
            }
        

        
        //echo $nextUrl;

        // Close the connection
        $conn->close();

        //echo $bilCart;

        //echo "Product Page for id: ".$id;
        //require_once __DIR__ . '/../../view/shop/shopAuth.php';
    }

    public function index3() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $pageid = 2;
        $pageName = "MEMBERSHIP";
        if(!isset($_SESSION["referby"])){
            header("Location: ".$domainURL."referby/1");
        }else{
            if(!isset($_SESSION["web_cart_id"])){
                $sessionParams = $_SESSION["referby"]."_".time()."_".rand("1000000","9999999");
                $_SESSION["web_cart_id"] = "shop_".time()."_".$_SESSION["referby"]."_".hash('sha256', $sessionParams);
            }
        }

        $timezone = "Asia/Kuala_Lumpur";
        if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
        $dates = date("Y-m-d H:i:s");

        $cartId = $_SESSION["web_cart_id"];

        
        $userData = userData($_SESSION["referby"]);

        $theUserId = $userData["id"];

        

        
            $email = $conn->real_escape_string($_POST["email"]);
            $password = $conn->real_escape_string($_POST["password"]);
            $passwords = hash('sha256', $password);

            

            $errorReg = "";
            $verifyEmail = $conn->query("SELECT * FROM membership WHERE email='$email' AND `password`='$passwords'");
            if($verifyEmail->num_rows != "1"){
                $errorReg .= "<li class='error'>Invalid email/password</li>";
            }

            //echo $errorReg;

            

            if(empty($errorReg)){
                //echo 1;
                function generateRandomString($length = 10) {
                    return substr(bin2hex(random_bytes($length)), 0, $length);
                }

                

                if($verifyEmail){
                    $getMemberships = $verifyEmail->fetch_array();

                    if($getMemberships["phone_verify"] == "0"){
                        unset($_SESSION["referby"]);
                        $_SESSION["referby"] = $getMemberships["referral"];
                        $first_char = substr($getMemberships["phone_no"], 0, 1);
                        if($first_char == "0"){
                            $theNo = substr($getMemberships["phone_no"], 1);
                        }else{
                            $theNo = $getMemberships["phone_no"];
                        }
                        $phone_number = $getMemberships["phone_c_code"].$theNo;
                        $normalized_number = $phone_number;

                        $memid = $getMemberships["id"];

                        $tac = rand("100000","999999");

                        $_SESSION["tac"] = $tac;
                        $_SESSION["usertac"] = $getMemberships["id"];

                        $new_date = date('Y-m-d H:i:s', strtotime($dates . ' +10 minutes'));
                        $_SESSION["expired"] = $new_date;
                        

                        $expDate = $new_date;

                        $isexistPhone = $conn->query("SELECT * FROM phone_verify_code WHERE member='$memid' AND phone_no='$normalized_number'");

                        if($isexistPhone->num_rows < "1"){
                            $addNewRecord = $conn->query("INSERT INTO `phone_verify_code`(`mem_id`, `member`, `phone_no`, `wasap_code`, `status`, `date_send`, `date_update`, `code_expired_on`) VALUES (NULL,'$memid','$normalized_number','".$_SESSION["tac"]."','0','$dates','$dates','$expDate')");
                        }else{
                            $addNewRecord = $conn->query("UPDATE `phone_verify_code` SET wasap_code='".$_SESSION["tac"]."', `status`='0', `date_send`='$dates', `date_update`='$dates', `code_expired_on`='$expDate' WHERE member='$memid' AND phone_no='$normalized_number'");
                        }

                        $curl = curl_init();

                        $data = [
                            'phone_number' => $normalized_number,
                            'message' => "RM0.00 *FusionKeyMall.com*. ".$_SESSION["tac"]." is your phone verification key and will expired on ".$expDate.".",
                        ];

                        curl_setopt_array($curl, [
                            CURLOPT_URL => 'https://onsend.io/api/v1/send',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data),
                            CURLOPT_HTTPHEADER => [
                                'Accept: application/json',
                                'Authorization: Bearer 8dc50b1c64ac2b3e8bbba876489492b6a3ab3a7e8e69d31ef700618746b81152',
                                'Content-Type: application/json',
                            ],
                        ]);

                        $response = curl_exec($curl);
                        $err = curl_error($curl);

                        curl_close($curl);

                        ?>
                        <script>
                            alert("Please verify your phone number.");
                            window.location.href = "<?= $domainURL; ?>verify_phone";
                        </script>
                        <?php
                    }else{
                        unset($_SESSION["referby"]);
                        $_SESSION["membership"] = $getMemberships["id"];
                        $_SESSION["referby"] = $getMemberships["referral"];
                        ?>
                        <script>
                            alert("Login successful.");
                            window.location.href = "<?= $_SESSION["nexturl"]; ?>";
                        </script>
                        <?php
                    }

                    
                }
            }else{
                //echo 2;
                $_SESSION["errorLogin"] = $errorReg;
                header("Location: ".$domainURL."secure-account");
            }
        

        
        //echo $nextUrl;

        // Close the connection
        $conn->close();

        //echo $bilCart;

        //echo "Product Page for id: ".$id;
        //require_once __DIR__ . '/../../view/shop/shopAuth.php';
    }

    public function verifyPhone()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $pageid = 2;
        $pageName = "MEMBERSHIP";
        if(!isset($_SESSION["referby"])){
            header("Location: ".$domainURL."referby/1");
        }else{
            if(!isset($_SESSION["web_cart_id"])){
                $sessionParams = $_SESSION["referby"]."_".time()."_".rand("1000000","9999999");
                $_SESSION["web_cart_id"] = "shop_".time()."_".$_SESSION["referby"]."_".hash('sha256', $sessionParams);
            }
        }

        $timezone = "Asia/Kuala_Lumpur";
        if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
        $dates = date("Y-m-d H:i:s");

        $cartId = $_SESSION["web_cart_id"];

        
        $userData = userData($_SESSION["referby"]);

        $theUserId = $userData["id"];

        $sql = "SELECT * FROM category WHERE `status`='1' AND assigned_user LIKE '%[$theUserId]%'";
        $result = $conn->query($sql);
        $category = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $category[] = $row;
            }
        } else {
            echo "No category found.";
        }

        if(!isset($_SESSION["usertac"]) && !isset($_SESSION["tac"])){
            header('Location: '.$domainURL."secure-account");
        }else if($_SESSION["expired"] < $dates){
            header('Location: '.$domainURL."secure-account");
        }

        $suser = $_SESSION["usertac"];
        $stac = $_SESSION["tac"];

        $tacDetail = $conn->query("SELECT * FROM `phone_verify_code` WHERE `member`='$suser'");
        
        $tacDetails = $tacDetail->fetch_array();

        //var_dump($tacDetails);
        //die();
        
        $conn->close();

        //echo $bilCart;

        //echo "Product Page for id: ".$id;
        require_once __DIR__ . '/../../view/shop/verifyPhone.php';
    }

    public function submitPhone()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $s_name = $_POST["s_name"] ?? "";

        $pins = "";
        foreach($_POST["pin"] as $pin){
            $pins .= $pin;
        }

        $errorTac = "";
        if($pins != $_SESSION["tac"]){
            $errorTac = "<li>Invalid TAC code.</li>";
        }

        if(!empty($errorTac)){
            $_SESSION["errorTac"] = $errorTac;

            header("Location: ".$domainURL."verify_phone");
        }else{
            $member = $_SESSION["usertac"];
            $updatePhone = $conn->query("UPDATE phone_verify_code SET `status`='1' WHERE member='$member' AND wasap_code='$pins'");
            $updateMember = $conn->query("UPDATE membership SET phone_verify='1' WHERE id='$member'");

            echo $member;

            $_SESSION["membership"] = $member;
                        ?>
                        <script>
                            alert("Phone number successful verified.");
                            window.location.href = "<?= $domainURL; ?>my-account";
                        </script>
                        <?php
        }
    }

    public function logout()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        unset($_SESSION["web_cart_id"]);
        unset($_SESSION["nexturl"]);
        unset($_SESSION["membership"]);
        unset($_SESSION["errorReg"]);
        unset($_SESSION["errorLogin"]);

        header("Location: ".$domainURL."secure-account");
    }
}