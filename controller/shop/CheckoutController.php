<?php
namespace Shop;

require_once __DIR__ . '/../../config/mainConfig.php';

class CheckoutController {
    public function index() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $pageid = 2;
        $pageName = "CHECKOUT";
        if(!isset($_SESSION["referby"])){
            header("Location: ".$domainURL."referby/1");
        }else{
            if(!isset($_SESSION["web_cart_id"])){
                $sessionParams = $_SESSION["referby"]."_".time()."_".rand("1000000","9999999");
                $_SESSION["web_cart_id"] = "shop_".time()."_".$_SESSION["referby"]."_".hash('sha256', $sessionParams);
            }
        }

        if(!isset($_SESSION["membership"])){
            ?>
            <script>
                alert("Please login to checkout.");
                window.location.href = "<?= $domainURL; ?>cart";
            </script>
            <?php
        }

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

        $country = $conn->query("SELECT * FROM country WHERE id='1' ORDER BY id ASC");

        $getCart = $conn->query("SELECT * FROM cart WHERE order_by='$theUserId' AND cart_id='$cartId' AND `status`='0'");
        

        if($getCart->num_rows >= "1"){
            $bilCart = $getCart->num_rows;
        }else{
            $bilCart = "0";
            if (!empty($_SERVER['HTTP_REFERER'])) {
                if($_SERVER['HTTP_REFERER'] == $domainURL."cart"){
                    $refLink = $domainURL;
                }else{
                    $refLink = $_SERVER['HTTP_REFERER'];
                }
                
            } else {
                $refLink = $domainURL;
            }

            ?>
            <script>
                alert("Cart empty! You will redirect to previous page.");
                window.location.href = "<?= $refLink; ?>";
            </script>
            <?php
        }


        // Close the connection
        $conn->close();

        //echo "checkout";

        //echo "Product Page for id: ".$id;
        require_once __DIR__ . '/../../view/shop/shopCheckout.php';
    }

    public function postChechout()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $pageid = 2;
        $pageName = "CHECKOUT";
        if(!isset($_SESSION["referby"])){
            header("Location: ".$domainURL."referby/1");
        }else{
            if(!isset($_SESSION["web_cart_id"])){
                $sessionParams = $_SESSION["referby"]."_".time()."_".rand("1000000","9999999");
                $_SESSION["web_cart_id"] = "shop_".time()."_".$_SESSION["referby"]."_".hash('sha256', $sessionParams);
            }
        }

        if(!isset($_SESSION["membership"])){
            ?>
            <script>
                alert("Please login to checkout.");
                window.location.href = "<?= $domainURL; ?>cart";
            </script>
            <?php
        }

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

        $country = $conn->query("SELECT * FROM country WHERE id='1' ORDER BY id ASC");

        $getCart = $conn->query("SELECT * FROM cart WHERE order_by='$theUserId' AND cart_id='$cartId' AND `status`='0'");
        $getWeight = $conn->query("SELECT SUM(total_weight) AS tweight FROM cart WHERE order_by='$theUserId' AND cart_id='$cartId'");
        $getWeights = $getWeight->fetch_assoc();
        $totalWeight = $getWeights["tweight"];
        

        if($getCart->num_rows >= "1"){
            $bilCart = $getCart->num_rows;
        }else{
            $bilCart = "0";
            if (!empty($_SERVER['HTTP_REFERER'])) {
                if($_SERVER['HTTP_REFERER'] == $domainURL."cart"){
                    $refLink = $domainURL;
                }else{
                    $refLink = $_SERVER['HTTP_REFERER'];
                }
                
            } else {
                $refLink = $domainURL;
            }

            ?>
            <script>
                alert("Cart empty! You will redirect to previous page.");
                window.location.href = "<?= $refLink; ?>";
            </script>
            <?php
        }

        $s_name = $_POST["s_name"] ?? "";
        $s_phone = $_POST["s_phone"] ?? "";
        $s_email = $_POST["s_email"] ?? "";
        $s_address_1 = $_POST["s_address_1"] ?? "";
        $s_address_2 = $_POST["s_address_2"] ?? "";
        $s_postcode = $_POST["s_postcode"] ?? "";
        $s_city = $_POST["s_city"] ?? "";
        $s_country = $_POST["s_country"] ?? "";
        $s_state = $_POST["s_state"] ?? "";

        $_SESSION["s_name"] = $_POST["s_name"] ?? "";
        $_SESSION["s_phone"] = $_POST["s_phone"] ?? "";
        $_SESSION["s_email"] = $_POST["s_email"] ?? "";
        $_SESSION["s_address_1"] = $_POST["s_address_1"] ?? "";
        $_SESSION["s_address_2"] = $_POST["s_address_2"] ?? "";
        $_SESSION["s_postcode"] = $_POST["s_postcode"] ?? "";
        $_SESSION["s_city"] = $_POST["s_city"] ?? "";
        $_SESSION["s_country"] = $_POST["s_country"] ?? "";
        $_SESSION["s_state"] = $_POST["s_state"] ?? "";

        $getStateId = $conn->query("SELECT * FROM so_states WHERE name='$s_state'");
        $getStateIds = $getStateId->fetch_array();
        $tsid = $getStateIds["id"];

        $postage_cost = $conn->query("SELECT * FROM postage_cost WHERE countrys LIKE '%[$tsid]%' AND `type`='1'");

        if($postage_cost->num_rows != "1"){
            $postageCost = "0.00";
        }else{
            $postage_costs = $postage_cost->fetch_array();
            $first = $postage_costs["first_kilos"];
            $next = $postage_costs["next_kilos"];

            if($totalWeight <= "1000"){
                $postageCost = $first;
            }else{
                $cfirst = $first;

                $totalWeightss = ceil(($totalWeight - 1000) / 1000);
                $cnext = $totalWeightss * $next;

                $postageCost = $cfirst + $cnext;

            }
        }

        $_SESSION["postageCost"] = $postageCost;




        // Close the connection
        $conn->close();

        require_once __DIR__ . '/../../view/shop/shopCheckout2.php';
    }

    public function payChechout()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $myTime = myTime();

        $membership = $_SESSION["membership"];
        $memberData = $conn->query("SELECT * FROM membership WHERE id='$membership'");
        $memberDatas = $memberData->fetch_array();
        $orderBy = $memberDatas["referral"];

        $dataUser = userData($orderBy);

        $verifyID = "shop_".$_SESSION["membership"]."_".time()."_".rand("10000000", "99999999");

        //echo $dataUser["email"]." ".$verifyID;

        if($dataUser["moq_kpi"] == "0"){
            $orderBy = "0";
            $orderTo = $memberDatas["referral"];
        }else if($dataUser["moq_kpi"] == "1"){
            $orderBy = $memberDatas["referral"];
            $orderTo = "1";
        }else if(is_null($dataUser["moq_kpi"])){
            $orderBy = "0";
            $orderTo = $memberDatas["referral"];
        }
        $networkTree = $dataUser["network_tree"];
        $cartId = $_SESSION["web_cart_id"];

        $s_name = $conn->real_escape_string($_SESSION["s_name"]);
        $s_phone = $_SESSION["s_phone"];
        $s_email = $_SESSION["s_email"];
        $s_address_1 = $conn->real_escape_string($_SESSION["s_address_1"]);
        $s_address_2 = $conn->real_escape_string($_SESSION["s_address_2"]);
        $s_postcode = $_SESSION["s_postcode"];
        $s_city = $_SESSION["s_city"];
        $s_country = $_SESSION["s_country"];
        $s_state = $_SESSION["s_state"];

        $postageCost = $_SESSION["postageCost"];
        $total = $_SESSION["total"];
        $topay = $_SESSION["topay"];

        $listItem = $conn->query("SELECT * FROM cart WHERE cart_id='$cartId' AND `status` IN(0,1)");

        $x=1;
        //$qty="";
        while($listItems = $listItem->fetch_array()){

            

            

            if($x == "1"){
                $products = $listItems["quantity"]."x ".$listItems["product_name"]."|";
            }else{
                $products = $listItems["quantity"]."x ".$listItems["product_name"];
            }
            //$qty += (int) $listItems["quantity"];
            $x++;
        }

        $listItemQTY = $conn->query("SELECT SUM(quantity) AS qty FROM cart WHERE cart_id='$cartId' AND `status` IN(0,1)");
        $listItemQTYs = $listItemQTY->fetch_assoc();
        $tqty = $listItemQTYs["qty"];

        

        $addNewOrder = $conn->query("INSERT INTO `customer_order`(`id`, `verify_id`, `order_by`, `order_to`, `network_tree`, `cart_id`, `date_submit`, `date_approve`, `date_update`, `name`, `phone`, `email`, `first_address`, `second_address`, `city`, `postcode`, `state`, `country`, `order_item`, `quantity`, `order_type`, `postage_cost`, `cod_cost`, `order_amount`, `add_on_charge`, `final_amount_actual`, `final_amount_sales`, `courier_service`, `tracking_no`, `tracking_url`, `status`, `discount`, `note`, `rate`, `payment_code`, `payment_url`, `platform`, `platform_price`, `awb`) VALUES (NULL,'$verifyID','$orderBy','$orderTo','$networkTree','$cartId','$myTime','$myTime','$myTime','$s_name','$s_phone','$s_email','$s_address_1','$s_address_2','$s_city','$s_postcode','$s_state','$s_country','$products','$tqty','1','$postageCost','0.00','$total','0.00','$topay','$topay','','','','0','0.00','','1.0','','','1','$total','')");
        // INSERT INTO `customer_order`(`id`, `verify_id`, `order_by`, `order_to`, `network_tree`, `cart_id`, `date_submit`, `date_approve`, `date_update`, `name`, `phone`, `email`, `first_address`, `second_address`, `city`, `postcode`, `state`, `country`, `order_item`, `quantity`, `order_type`, `postage_cost`, `cod_cost`, `order_amount`, `add_on_charge`, `final_amount_actual`, `final_amount_sales`, `courier_service`, `tracking_no`, `tracking_url`, `status`, `discount`, `note`, `rate`, `payment_code`, `payment_url`, `platform`, `platform_price`, `awb`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]','[value-6]','[value-7]','[value-8]','[value-9]','[value-10]','[value-11]','[value-12]','[value-13]','[value-14]','[value-15]','[value-16]','[value-17]','[value-18]','[value-19]','[value-20]','[value-21]','[value-22]','[value-23]','[value-24]','[value-25]','[value-26]','[value-27]','[value-28]','[value-29]','[value-30]','[value-31]','[value-32]','[value-33]','[value-34]','[value-35]','[value-36]','[value-37]','[value-38]','[value-39]')

        if($addNewOrder){
            $getorder = getOrder (2, $verifyID);
            $orderID = $getorder["id"];

            $listItem2 = $conn->query("SELECT * FROM cart WHERE cart_id='$cartId' AND `status` IN(0,1)");
            while($listItem2s = $listItem2->fetch_array()){

                $productIds = $listItem2s["product_id"];
    
                $products = dataProduct($$productIds);
    
                $memberPoint = $products["member_point"] * $listItem2s["quantity"];
                $purchaseAMT = $listItem2s["total_retail_price"];
    
                if($memberPoint >= 1){
                    $referedby = $_SESSION["referby"];
                    $addPoint = $conn->query("INSERT INTO `membership_point_history`(`id`, `membership_id`, `referral`, `order_id`, `purchase_amount`, `point_amount`, `date_purchase`, `date_expired_point`, `point_status`) VALUES (NULL,'$membership','$referedby','$orderID','$purchaseAMT','$memberPoint','$myTime','','0')");
                }
    
                
            }
            $billplz = billPlzzOrder ($getorder["id"], $s_name, $s_email, $s_phone, $domainURL);

            if(!empty($billplz["pay_code"]))
            {
                $bpCode = $billplz["pay_code"];
                $bpUrl = $billplz["pay_url"];
                $updateOrder = $conn->query("UPDATE customer_order SET `payment_code`='$bpCode', `payment_url`='$bpUrl' WHERE verify_id='$verifyID'");

                unset($_SESSION["s_name"]);
                unset($_SESSION["s_phone"]);
                unset($_SESSION["s_email"]);
                unset($_SESSION["s_address_1"]);
                unset($_SESSION["s_address_2"]);
                unset($_SESSION["s_postcode"]);
                unset($_SESSION["s_city"]);
                unset($_SESSION["s_country"]);
                unset($_SESSION["s_state"]);
                unset($_SESSION["total"]);
                unset($_SESSION["topay"]);
                unset($_SESSION["web_cart_id"]);

                header("Location: ".$bpUrl);
            }
        }
        
    }
    public function country($id)
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $getState = $conn->query("SELECT * FROM so_states WHERE country_id='$id'");
        ?>
            <option value="">select state</option>
        <?php
        while($row = $getState->fetch_array()){
            ?>
            <option value="<?= $row["name"]; ?>"><?= $row["name"]; ?></option>
            <?php
        }
    }
}