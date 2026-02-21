<?php
namespace Shop;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/ShopCategory.php';
require_once __DIR__ . '/../../model/Membership.php';
require_once __DIR__ . '/../../model/Member.php';
require_once __DIR__ . '/../../model/PhoneVerifyCode.php';

class AuthController {

    private $conn;
    private $categoryModel;
    private $membershipModel;
    private $memberModel;
    private $phoneVerifyModel;

    public function __construct()
    {
        $this->conn = getDbConnection();
        $this->categoryModel = new \ShopCategory($this->conn);
        $this->membershipModel = new \Membership($this->conn);
        $this->memberModel = new \Member($this->conn);
        $this->phoneVerifyModel = new \PhoneVerifyCode($this->conn);
    }

    public function index() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;
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

        $category = $this->categoryModel->getByAssignedUser($theUserId);
        if (empty($category)) {
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
            if($this->membershipModel->checkEmailExists($email)){
                $errorReg .= "<li class='error'>Email already been used.</li>";
            }else{
                if($this->memberModel->checkEmailExists($email)){
                    $errorReg .= "<li class='error'>Email already been used.</li>";
                }
            }

            if($this->membershipModel->checkPhoneExists($ccode, $phone)){
                $errorReg .= "<li class='error'>Phone no. already been used.</li>";
            }else{
                $phoneF = $ccode.$phone;
                if($this->memberModel->checkPhoneExists($phoneF)){
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
                $this->membershipModel->registerMember([
                    'reg_hex' => $regHex,
                    'name' => $fullname,
                    'email' => $email,
                    'password' => $passwords,
                    'phone_c_code' => $ccode,
                    'phone_no' => $phone,
                    'country' => $country,
                    'date_added' => $dates,
                    'date_update' => $dates,
                    'referral' => $theUserId,
                    'referral_membership' => '0',
                ]);

                $getMemberships = $this->membershipModel->findByRegHex($regHex);

                if($getMemberships){
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

        require_once __DIR__ . '/../../view/shop/shopAuth.php';
    }

    public function index2() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;
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
            if($this->membershipModel->checkEmailExists($email)){
                $errorReg .= "<li class='error'>Email already been used.</li>";
            }else{
                if($this->memberModel->checkEmailExists($email)){
                    $errorReg .= "<li class='error'>Email already been used.</li>";
                }
            }

            if($this->membershipModel->checkPhoneExists($ccode, $phone)){
                $errorReg .= "<li class='error'>Phone no. already been used.</li>";
            }else{
                $phoneF = $ccode.$phone;
                if($this->memberModel->checkPhoneExists($phoneF)){
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
                $this->membershipModel->registerMember([
                    'reg_hex' => $regHex,
                    'name' => $fullname,
                    'email' => $email,
                    'password' => $passwords,
                    'phone_c_code' => $ccode,
                    'phone_no' => $phone,
                    'country' => $country,
                    'date_added' => $dates,
                    'date_update' => $dates,
                    'referral' => $theUserId,
                    'referral_membership' => $sponsorMember,
                ]);

                $getMemberships = $this->membershipModel->findByRegHex($regHex);

                if($getMemberships){
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
    }

    public function index3() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;
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
            $getMemberships = $this->membershipModel->findByEmailAndPassword($email, $passwords);
            if(!$getMemberships){
                $errorReg .= "<li class='error'>Invalid email/password</li>";
            }

            if(empty($errorReg)){
                function generateRandomString($length = 10) {
                    return substr(bin2hex(random_bytes($length)), 0, $length);
                }

                if($getMemberships){

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

                        $isexistPhone = $this->phoneVerifyModel->findByMemberAndPhone($memid, $normalized_number);

                        if(!$isexistPhone){
                            $this->phoneVerifyModel->createRecord([
                                'member' => $memid,
                                'phone_no' => $normalized_number,
                                'wasap_code' => $_SESSION["tac"],
                                'date_send' => $dates,
                                'date_update' => $dates,
                                'code_expired_on' => $expDate,
                            ]);
                        }else{
                            $this->phoneVerifyModel->updateCode($memid, $normalized_number, [
                                'wasap_code' => $_SESSION["tac"],
                                'date_send' => $dates,
                                'date_update' => $dates,
                                'code_expired_on' => $expDate,
                            ]);
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
                $_SESSION["errorLogin"] = $errorReg;
                header("Location: ".$domainURL."secure-account");
            }
    }

    public function verifyPhone()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;
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

        $category = $this->categoryModel->getByAssignedUser($theUserId);
        if (empty($category)) {
            echo "No category found.";
        }

        if(!isset($_SESSION["usertac"]) && !isset($_SESSION["tac"])){
            header('Location: '.$domainURL."secure-account");
        }else if($_SESSION["expired"] < $dates){
            header('Location: '.$domainURL."secure-account");
        }

        $suser = $_SESSION["usertac"];
        $stac = $_SESSION["tac"];

        $tacDetails = $this->phoneVerifyModel->findByMember($suser);

        require_once __DIR__ . '/../../view/shop/verifyPhone.php';
    }

    public function submitPhone()
    {
        $domainURL = getMainUrl();

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
            $this->phoneVerifyModel->verifyCode($member, $pins);
            $this->membershipModel->updatePhoneVerify($member);

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
