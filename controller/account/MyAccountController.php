<?php
namespace Account;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/ShopCategory.php';

class MyAccountController {

    private $domainURL;
    private $conn;
    private $shopCategoryModel;

    public function __construct()
    {
        $this->domainURL = getMainUrl();
        $this->conn = getDbConnection();
        $this->shopCategoryModel = new \ShopCategory($this->conn);
    }

    public function index() {

        $domainURL = $this->domainURL;
        $mainDomain = mainDomain();
        $pageid = 2;
        $pageName = "MY ACCOUNT";
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

        $category = $this->shopCategoryModel->getByAssignedUser($theUserId);
        if (empty($category)) {
            echo "No category found.";
        }

        if(isset($_SESSION["membership"]) && !empty($_SESSION["membership"]))
        {
            $dataMember = memberData($_SESSION["membership"]);
        }else{
            header("Location: ".$domainURL."secure-account");
        }

        $memberPoint = memberPoint($_SESSION["membership"]);

        require_once __DIR__ . '/../../view/shop/my-account.php';
    }

}
