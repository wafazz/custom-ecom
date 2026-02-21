<?php
namespace Shop;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/ShopCategory.php';
require_once __DIR__ . '/../../model/ShopCart.php';
require_once __DIR__ . '/../../model/ShopState.php';
require_once __DIR__ . '/../../model/ShopPostageCost.php';
require_once __DIR__ . '/../../model/ShopOrder.php';
require_once __DIR__ . '/../../model/Membership.php';

class CheckoutController {

    private $conn;
    private $categoryModel;
    private $cartModel;
    private $stateModel;
    private $postageCostModel;
    private $orderModel;
    private $membershipModel;

    public function __construct()
    {
        $this->conn = getDbConnection();
        $this->categoryModel = new \ShopCategory($this->conn);
        $this->cartModel = new \ShopCart($this->conn);
        $this->stateModel = new \ShopState($this->conn);
        $this->postageCostModel = new \ShopPostageCost($this->conn);
        $this->orderModel = new \ShopOrder($this->conn);
        $this->membershipModel = new \Membership($this->conn);
    }

    public function index() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;
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

        $category = $this->categoryModel->getByAssignedUser($theUserId);

        if (empty($category)) {
            echo "No category found.";
        }

        $country = $this->conn->query("SELECT * FROM country WHERE id='1' ORDER BY id ASC");

        $cartItems = $this->cartModel->getByUserAndCart($theUserId, $cartId);
        $getCart = $cartItems;

        if(!empty($cartItems)){
            $bilCart = count($cartItems);
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

        require_once __DIR__ . '/../../view/shop/shopCheckout.php';
    }

    public function postChechout()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;
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

        $category = $this->categoryModel->getByAssignedUser($theUserId);

        if (empty($category)) {
            echo "No category found.";
        }

        $country = $this->conn->query("SELECT * FROM country WHERE id='1' ORDER BY id ASC");

        $cartItems = $this->cartModel->getByUserAndCart($theUserId, $cartId);
        $getCart = $cartItems;
        $totalWeight = $this->cartModel->getTotalWeight($theUserId, $cartId);

        if(!empty($cartItems)){
            $bilCart = count($cartItems);
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

        $getStateIds = $this->stateModel->findByName($s_state);
        $tsid = $getStateIds["id"];

        $postage_costs = $this->postageCostModel->findByStateId($tsid);

        if(!$postage_costs){
            $postageCost = "0.00";
        }else{
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

        require_once __DIR__ . '/../../view/shop/shopCheckout2.php';
    }

    public function payChechout()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;

        $myTime = myTime();

        $membership = $_SESSION["membership"];
        $memberDatas = $this->membershipModel->findById($membership);
        $orderBy = $memberDatas["referral"];

        $dataUser = userData($orderBy);

        $verifyID = "shop_".$_SESSION["membership"]."_".time()."_".rand("10000000", "99999999");

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

        $listItems = $this->cartModel->getActiveByCartId($cartId);

        $x=1;
        $products = "";
        foreach($listItems as $listItem){
            if($x == "1"){
                $products = $listItem["quantity"]."x ".$listItem["product_name"]."|";
            }else{
                $products = $listItem["quantity"]."x ".$listItem["product_name"];
            }
            $x++;
        }

        $tqty = $this->cartModel->getTotalQtyByCartId($cartId);

        $orderData = [
            'verify_id'           => $verifyID,
            'order_by'            => $orderBy,
            'order_to'            => $orderTo,
            'network_tree'        => $networkTree,
            'cart_id'             => $cartId,
            'date_submit'         => $myTime,
            'date_approve'        => $myTime,
            'date_update'         => $myTime,
            'name'                => $s_name,
            'phone'               => $s_phone,
            'email'               => $s_email,
            'first_address'       => $s_address_1,
            'second_address'      => $s_address_2,
            'city'                => $s_city,
            'postcode'            => $s_postcode,
            'state'               => $s_state,
            'country'             => $s_country,
            'order_item'          => $products,
            'quantity'            => $tqty,
            'order_type'          => '1',
            'postage_cost'        => $postageCost,
            'cod_cost'            => '0.00',
            'order_amount'        => $total,
            'add_on_charge'       => '0.00',
            'final_amount_actual' => $topay,
            'final_amount_sales'  => $topay,
            'courier_service'     => '',
            'tracking_no'         => '',
            'tracking_url'        => '',
            'status'              => '0',
            'discount'            => '0.00',
            'note'                => '',
            'rate'                => '1.0',
            'payment_code'        => '',
            'payment_url'         => '',
            'platform'            => '1',
            'platform_price'      => $total,
            'awb'                 => '',
        ];

        $addNewOrder = $this->orderModel->createOrder($orderData);

        if($addNewOrder){
            $getorder = getOrder (2, $verifyID);
            $orderID = $getorder["id"];

            $listItem2 = $this->cartModel->getActiveByCartId($cartId);
            foreach($listItem2 as $listItem2s){

                $productIds = $listItem2s["product_id"];

                $productData = dataProduct($productIds);

                $memberPoint = $productData["member_point"] * $listItem2s["quantity"];
                $purchaseAMT = $listItem2s["total_retail_price"];

                if($memberPoint >= 1){
                    $referedby = $_SESSION["referby"];
                    $this->membershipModel->addPointHistory([
                        'membership_id'   => $membership,
                        'referral'        => $referedby,
                        'order_id'        => $orderID,
                        'purchase_amount' => $purchaseAMT,
                        'point_amount'    => $memberPoint,
                        'date_purchase'   => $myTime,
                    ]);
                }
            }

            $billplz = billPlzzOrder ($getorder["id"], $s_name, $s_email, $s_phone, $domainURL);

            if(!empty($billplz["pay_code"]))
            {
                $bpCode = $billplz["pay_code"];
                $bpUrl = $billplz["pay_url"];
                $this->orderModel->updatePayment($verifyID, $bpCode, $bpUrl);

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
        $states = $this->stateModel->getByCountryId($id);
        ?>
            <option value="">select state</option>
        <?php
        foreach($states as $row){
            ?>
            <option value="<?= $row["name"]; ?>"><?= $row["name"]; ?></option>
            <?php
        }
    }
}
