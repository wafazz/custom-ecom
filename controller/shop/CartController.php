<?php
namespace Shop;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/ShopCategory.php';
require_once __DIR__ . '/../../model/ShopCart.php';

class CartController {

    private $conn;
    private $categoryModel;
    private $cartModel;

    public function __construct()
    {
        $this->conn = getDbConnection();
        $this->categoryModel = new \ShopCategory($this->conn);
        $this->cartModel = new \ShopCart($this->conn);
    }

    public function index() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;
        $pageid = 2;
        $pageName = "CART";
        if(!isset($_SESSION["referby"])){
            header("Location: ".$domainURL."referby/1");
        }else{
            if(!isset($_SESSION["web_cart_id"])){
                $sessionParams = $_SESSION["referby"]."_".time()."_".rand("1000000","9999999");
                $_SESSION["web_cart_id"] = "shop_".time()."_".$_SESSION["referby"]."_".hash('sha256', $sessionParams);
            }
        }

        $cartId = $_SESSION["web_cart_id"];

        $userData = userData($_SESSION["referby"]);
        $theUserId = $userData["id"];

        $category = $this->categoryModel->getByAssignedUser($theUserId);
        if (empty($category)) {
            echo "No category found.";
        }

        $getCart = $this->cartModel->getByUserAndCart($theUserId, $cartId);

        if(!empty($getCart)){
            $bilCart = count($getCart);
        }else{
            $bilCart = "0";
            if (!empty($_SERVER['HTTP_REFERER'])) {
                if($_SERVER['HTTP_REFERER'] == $domainURL."cart"){
                    $refLink = $domainURL;
                }else if($_SERVER['HTTP_REFERER'] == $domainURL."checkout"){
                    $refLink = $domainURL."secure-account";
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

        require_once __DIR__ . '/../../view/shop/shopCart.php';
    }

    public function updateCart() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;
        $pageid = 2;
        $pageName = "CART";
        if(!isset($_SESSION["referby"])){
            header("Location: ".$domainURL."referby/1");
        }else{
            if(!isset($_SESSION["web_cart_id"])){
                $sessionParams = $_SESSION["referby"]."_".time()."_".rand("1000000","9999999");
                $_SESSION["web_cart_id"] = "shop_".time()."_".$_SESSION["referby"]."_".hash('sha256', $sessionParams);
            }
        }

        $cartId = $_SESSION["web_cart_id"];

        $userData = userData($_SESSION["referby"]);
        $theUserId = $userData["id"];

        $uu_proid = $_POST["uu_proid"];
        $uu_qty = $_POST["uu_qty"];

        if($uu_qty < "1"){
            $this->cartModel->deleteByUserProductCart($theUserId, $uu_proid, $cartId);
            header("Location: ".$domainURL."cart");
        }else{

            $timezone = "Asia/Kuala_Lumpur";
            if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
            $dates = date("Y-m-d H:i:s");

            $getCarts = $this->cartModel->findByUserProductCart($theUserId, $uu_proid, $cartId);

            $newQTY = $uu_qty;
            $newPrice = $newQTY * $getCarts["unit_price"];
            $totalWeight = $newQTY * $getCarts["unit_weight"];
            $totalCapital = $newQTY * $getCarts["price_capital"];

            $this->cartModel->updateByUserProductCart($theUserId, $uu_proid, $cartId, [
                'quantity' => $newQTY,
                'total_price' => $newPrice,
                'total_retail_price' => $newPrice,
                'date_update' => $dates,
                'total_price_capital' => $totalCapital,
                'total_weight' => $totalWeight,
            ]);
            header("Location: ".$domainURL."cart");
        }
    }
}
