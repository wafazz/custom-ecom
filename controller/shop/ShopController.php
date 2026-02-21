<?php
namespace Shop;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/ShopCategory.php';
require_once __DIR__ . '/../../model/ShopProduct.php';

class ShopController {

    private $domainURL;
    private $conn;
    private $shopCategoryModel;
    private $shopProductModel;

    public function __construct()
    {
        $this->domainURL = getMainUrl();
        $this->conn = getDbConnection();
        $this->shopCategoryModel = new \ShopCategory($this->conn);
        $this->shopProductModel  = new \ShopProduct($this->conn);
    }

    public function main() {

        $domainURL = $this->domainURL;
        $mainDomain = mainDomain();
        $pageid = 1;
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

        $category = $this->shopCategoryModel->getByAssignedUser($theUserId);
        if (empty($category)) {
            echo "No category found.";
        }

        $enableField = ($userData["moq_kpi"] == 1) ? 'enable_kpi' : 'enable_moq';
        $product = $this->shopProductModel->getAllWithCategory($enableField);
        if (empty($product)) {
            echo "No product found.";
        }

        require_once __DIR__ . '/../../view/shop/shopMain.php';
    }
}
