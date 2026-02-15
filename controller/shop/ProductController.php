<?php
namespace Shop;

require_once __DIR__ . '/../../config/mainConfig.php';

class ProductController {
    public function index($id) {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
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

        $sql = "SELECT * FROM category WHERE `status`='1' AND assigned_user LIKE '%[$theUserId]%'";
        $result = $conn->query($sql);

        if($userData["moq_kpi"] == 1){
            //$sqlProduct = "SELECT * FROM product WHERE `enable_kpi`='1' AND `status`='1'";
            $sqlProduct = "
                SELECT p.member_point AS member_point, p.product_description AS product_description, p.sku AS product_sku, p.product_name AS product_name, p.product_image AS product_image, p.selling_price AS selling_price, c.cat_name AS category_name
                FROM product p
                JOIN category c ON p.category = c.id
                WHERE p.id = $id AND p.enable_kpi = '1' AND p.status = '1'
            ";
        }else if($userData["moq_kpi"] == 0){
            $sqlProduct = "
                SELECT p.member_point AS member_point, p.product_description AS product_description, p.sku AS product_sku, p.product_name AS product_name, p.product_image AS product_image, p.selling_price AS selling_price, c.cat_name AS category_name
                FROM product p
                JOIN category c ON p.category = c.id
                WHERE p.id = $id AND p.enable_moq = '1' AND p.status = '1'
            ";
        }else{
            $sqlProduct = "
                SELECT p.member_point AS member_point, p.product_description AS product_description, p.sku AS product_sku, p.product_name AS product_name, p.product_image AS product_image, p.selling_price AS selling_price, c.cat_name AS category_name
                FROM product p
                JOIN category c ON p.category = c.id
                WHERE p.id = $id AND p.enable_moq = '1' AND p.status = '1'
            ";
        }
        $resultProduct = $conn->query($sqlProduct);

        $category = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $category[] = $row;
            }
        } else {
            echo "No category found.";
        }

        //$product = $resultProduct->fetch_assoc();
        if ($resultProduct->num_rows > 0) {
            $product = $resultProduct->fetch_assoc();
        } else {
            header("Location: ".$domainURL);
        }

        

        // Close the connection
        $conn->close();

        //echo "Product Page for id: ".$id;
        require_once __DIR__ . '/../../view/shop/shopProduct.php';
    }

    public function addToCart()
    {

        $conn = getDbConnection();
        $timezone = "Asia/Kuala_Lumpur";
        if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
        $dates = date("Y-m-d H:i:s");
        $cartSession = $_GET["cartSession"];
        $productID = $_GET["productID"];
        $productQTY = $_GET["productQTY"];

        $cartSessions = explode("_", $cartSession);

        $userData = userData($cartSessions[2]);

        $dataProduct = dataProduct($productID);


        $getCart = $conn->query("SELECT * FROM cart WHERE order_by='$cartSessions[2]' AND product_id='$productID' AND cart_id='$cartSession'");

        if($getCart->num_rows >= "1"){
            $dataCart = $getCart->fetch_array();

            $newQTY = $dataCart["quantity"] + $productQTY;
            $newPrice = $newQTY * $dataCart["unit_price"];
            $totalWeight = $newQTY * $dataCart["unit_weight"];
            $totalCapital = $newQTY * $dataCart["price_capital"];

            $updateCart = $conn->query("UPDATE cart SET quantity='$newQTY', total_price='$newPrice', total_retail_price='$newPrice', date_update='$dates', total_price_capital='$totalCapital', total_weight='$totalWeight' WHERE order_by='$cartSessions[2]' AND product_id='$productID' AND cart_id='$cartSession'");

        }else{
            if ($userData["moq_kpi"] == "0") {
                if ($userData["role"] == "4") {
                    $thePrice =  $dataProduct["role_4"];

                } else if ($userData["role"] == "5") {
                    $thePrice =  $dataProduct["role_5"];

                } else if ($userData["role"] == "6") {
                    $thePrice =  $dataProduct["role_6"];

                } else if ($userData["role"] == "7") {
                    $thePrice =  $dataProduct["role_7"];

                } else if ($userData["role"] == "8") {
                    $thePrice =  $dataProduct["role_8"];

                } else if ($userData["role"] == "9") {
                    $thePrice =  $dataProduct["role_9"];

                } else if ($userData["role"] == "10") {
                    $thePrice =  $dataProduct["role_10"];

                }

                $sellPrice = $dataProduct["selling_price"];
                $totalPrice = $productQTY * $sellPrice;

                $network = $userData["network_tree"];

                $productWeight = $dataProduct["weight"];
                $totalWeight = $productQTY * $dataProduct["weight"];
                
                
                $totalRetailPrice = $productQTY *  $dataProduct["selling_price"];
                $totalCapitalPrice = $productQTY *  $dataProduct["capital_price"];
                $addNewCart = $conn->query("INSERT INTO `cart`(`id`, `order_by`, `product_id`, `product_sub_id`, `product_name`, `quantity`, `unit_price`, `total_price`, `retail_price`, `total_retail_price`, `date_added`, `date_update`, `network_tree`, `cart_id`, `price_capital`, `total_price_capital`, `status`, `user_type`, `cod_add_on`, `charge_seller`, `unit_weight`, `total_weight`, `combo`) VALUES (NULL,'$cartSessions[2]','$productID','0','". $dataProduct["product_name"]."','$productQTY','".$sellPrice."','$totalPrice','". $dataProduct["selling_price"]."','$totalRetailPrice','$dates','$dates','$network','$cartSession','". $dataProduct["capital_price"]."','$totalCapitalPrice','0','2','". $dataProduct["cod_add_on"]."','". $dataProduct["charge_back_if_not_charge_cod"]."','$productWeight','$totalWeight','0')");

                //echo "1<br>";

            } else if ($userData["moq_kpi"] == "1") {
                if ($userData["role"] == "4") {
                    $thePrice =  $dataProduct["kpi_4"];

                } else if ($userData["role"] == "5") {
                    $thePrice =  $dataProduct["kpi_5"];

                } else if ($userData["role"] == "6") {
                    $thePrice =  $dataProduct["kpi_6"];

                } else if ($userData["role"] == "7") {
                    $thePrice =  $dataProduct["kpi_7"];

                } else if ($userData["role"] == "8") {
                    $thePrice =  $dataProduct["kpi_8"];

                } else if ($userData["role"] == "9") {
                    $thePrice =  $dataProduct["kpi_9"];

                } else if ($userData["role"] == "10") {
                    $thePrice =  $dataProduct["kpi_10"];

                }

                $sellPrice = $dataProduct["selling_price"];
                $totalPrice = $productQTY * $sellPrice;

                $network = $userData["network_tree"];

                $productWeight = $dataProduct["weight"];
                $totalWeight = $productQTY * $dataProduct["weight"];
                
                
                $totalRetailPrice = $productQTY *  $dataProduct["selling_price"];
                $totalCapitalPrice = $productQTY *  $dataProduct["capital_price"];
                $addNewCart = $conn->query("INSERT INTO `cart`(`id`, `order_by`, `product_id`, `product_sub_id`, `product_name`, `quantity`, `unit_price`, `total_price`, `retail_price`, `total_retail_price`, `date_added`, `date_update`, `network_tree`, `cart_id`, `price_capital`, `total_price_capital`, `status`, `user_type`, `cod_add_on`, `charge_seller`, `unit_weight`, `total_weight`, `combo`) VALUES (NULL,'$cartSessions[2]','$productID','0','". $dataProduct["product_name"]."','$productQTY','".$sellPrice."','$totalPrice','". $dataProduct["selling_price"]."','$totalRetailPrice','$dates','$dates','$network','$cartSession','". $dataProduct["capital_price"]."','$totalCapitalPrice','0','2','". $dataProduct["cod_add_on"]."','". $dataProduct["charge_back_if_not_charge_cod"]."','$productWeight','$totalWeight','0')");

                //echo "2<br>";

            }else{
                if ($userData["role"] == "4") {
                    $thePrice =  $dataProduct["role_4"];

                } else if ($userData["role"] == "5") {
                    $thePrice =  $dataProduct["role_5"];

                } else if ($userData["role"] == "6") {
                    $thePrice =  $dataProduct["role_6"];

                } else if ($userData["role"] == "7") {
                    $thePrice =  $dataProduct["role_7"];

                } else if ($userData["role"] == "8") {
                    $thePrice =  $dataProduct["role_8"];

                } else if ($userData["role"] == "9") {
                    $thePrice =  $dataProduct["role_9"];

                } else if ($userData["role"] == "10") {
                    $thePrice =  $dataProduct["role_10"];

                }

                $sellPrice = $dataProduct["selling_price"];
                $totalPrice = $productQTY * $sellPrice;

                $network = $userData["network_tree"];

                $productWeight = $dataProduct["weight"];
                $totalWeight = $productQTY * $dataProduct["weight"];
                
                
                $totalRetailPrice = $productQTY *  $dataProduct["selling_price"];
                $totalCapitalPrice = $productQTY *  $dataProduct["capital_price"];
                $addNewCart = $conn->query("INSERT INTO `cart`(`id`, `order_by`, `product_id`, `product_sub_id`, `product_name`, `quantity`, `unit_price`, `total_price`, `retail_price`, `total_retail_price`, `date_added`, `date_update`, `network_tree`, `cart_id`, `price_capital`, `total_price_capital`, `status`, `user_type`, `cod_add_on`, `charge_seller`, `unit_weight`, `total_weight`, `combo`) VALUES (NULL,'$cartSessions[2]','$productID','0','". $dataProduct["product_name"]."','$productQTY','".$sellPrice."','$totalPrice','". $dataProduct["selling_price"]."','$totalRetailPrice','$dates','$dates','$network','$cartSession','". $dataProduct["capital_price"]."','$totalCapitalPrice','0','2','". $dataProduct["cod_add_on"]."','". $dataProduct["charge_back_if_not_charge_cod"]."','$productWeight','$totalWeight','0')");

                //echo "3<br>";
            }

            echo "Successful updated cart.";
        }
    }

    public function dataCart()
    {
        $conn = getDbConnection();
        $sessionCartID = $_SESSION["web_cart_id"];
        $userSession = $_SESSION["referby"];

        $getCart = $conn->query("SELECT SUM(quantity) AS qty FROM cart WHERE order_by='$userSession' AND cart_id='$sessionCartID' AND `status`='0'");
        $getCarts = $getCart->fetch_assoc();

        if($getCarts["qty"] < "1"){
            echo "0";
        }else{
            echo $getCarts["qty"];
        }
        
    }

}