<?php
namespace Shop;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/ShopCategory.php';
require_once __DIR__ . '/../../model/ShopCart.php';
require_once __DIR__ . '/../../model/ShopProduct.php';

class ProductController {

    private $conn;
    private $categoryModel;
    private $cartModel;
    private $productModel;

    public function __construct()
    {
        $this->conn = getDbConnection();
        $this->categoryModel = new \ShopCategory($this->conn);
        $this->cartModel = new \ShopCart($this->conn);
        $this->productModel = new \ShopProduct($this->conn);
    }

    public function index($id) {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;
        $pageid = 2;
        $pageName = "PRODUCT";
        if(!isset($_SESSION["referby"])){
            header("Location: ".$domainURL."referby/1");
        }else{
            if(!isset($_SESSION["web_cart_id"])){
                $sessionParams = $_SESSION["referby"]."_".time()."_".rand("1000000","9999999");
                $_SESSION["web_cart_id"] = "shop_".time()."_".$_SESSION["referby"]."_".hash('sha256', $sessionParams);
            }
        }

        $userData = userData($_SESSION["referby"]);
        $theUserId = $userData["id"];

        $category = $this->categoryModel->getByAssignedUser($theUserId);
        if (empty($category)) {
            echo "No category found.";
        }

        if($userData["moq_kpi"] == 1){
            $enableField = 'enable_kpi';
        }else{
            $enableField = 'enable_moq';
        }

        $product = $this->productModel->getProductWithCategory($id, $enableField);

        if (!$product) {
            header("Location: ".$domainURL);
        }

        require_once __DIR__ . '/../../view/shop/shopProduct.php';
    }

    public function addToCart()
    {
        $timezone = "Asia/Kuala_Lumpur";
        if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
        $dates = date("Y-m-d H:i:s");
        $cartSession = $_GET["cartSession"];
        $productID = $_GET["productID"];
        $productQTY = $_GET["productQTY"];

        $cartSessions = explode("_", $cartSession);

        $userData = userData($cartSessions[2]);

        $dataProduct = dataProduct($productID);

        $existingCart = $this->cartModel->findByUserProductCart($cartSessions[2], $productID, $cartSession);

        if($existingCart){
            $newQTY = $existingCart["quantity"] + $productQTY;
            $newPrice = $newQTY * $existingCart["unit_price"];
            $totalWeight = $newQTY * $existingCart["unit_weight"];
            $totalCapital = $newQTY * $existingCart["price_capital"];

            $this->cartModel->updateByUserProductCart($cartSessions[2], $productID, $cartSession, [
                'quantity' => $newQTY,
                'total_price' => $newPrice,
                'total_retail_price' => $newPrice,
                'date_update' => $dates,
                'total_price_capital' => $totalCapital,
                'total_weight' => $totalWeight,
            ]);

        }else{
            $sellPrice = $dataProduct["selling_price"];
            $totalPrice = $productQTY * $sellPrice;
            $network = $userData["network_tree"];
            $productWeight = $dataProduct["weight"];
            $totalWeight = $productQTY * $dataProduct["weight"];
            $totalRetailPrice = $productQTY * $dataProduct["selling_price"];
            $totalCapitalPrice = $productQTY * $dataProduct["capital_price"];

            $this->cartModel->insertCartItem([
                'order_by' => $cartSessions[2],
                'product_id' => $productID,
                'product_sub_id' => '0',
                'product_name' => $dataProduct["product_name"],
                'quantity' => $productQTY,
                'unit_price' => $sellPrice,
                'total_price' => $totalPrice,
                'retail_price' => $dataProduct["selling_price"],
                'total_retail_price' => $totalRetailPrice,
                'date_added' => $dates,
                'date_update' => $dates,
                'network_tree' => $network,
                'cart_id' => $cartSession,
                'price_capital' => $dataProduct["capital_price"],
                'total_price_capital' => $totalCapitalPrice,
                'status' => '0',
                'user_type' => '2',
                'cod_add_on' => $dataProduct["cod_add_on"],
                'charge_seller' => $dataProduct["charge_back_if_not_charge_cod"],
                'unit_weight' => $productWeight,
                'total_weight' => $totalWeight,
                'combo' => '0',
            ]);

            echo "Successful updated cart.";
        }
    }

    public function dataCart()
    {
        $sessionCartID = $_SESSION["web_cart_id"];
        $userSession = $_SESSION["referby"];

        $qty = $this->cartModel->getCartQty($userSession, $sessionCartID);

        if($qty < 1){
            echo "0";
        }else{
            echo $qty;
        }
    }

}
