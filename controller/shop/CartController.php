<?php
namespace Shop;

require_once __DIR__ . '/../../config/mainConfig.php';

class CartController {
    public function index() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
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

        $getCart = $conn->query("SELECT * FROM cart WHERE order_by='$theUserId' AND cart_id='$cartId' AND `status`='0'");
        

        if($getCart->num_rows >= "1"){
            $bilCart = $getCart->num_rows;
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


        // Close the connection
        $conn->close();

        //echo $bilCart;

        //echo "Product Page for id: ".$id;
        require_once __DIR__ . '/../../view/shop/shopCart.php';
    }

    public function updateCart() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
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
            $deleteCart = $conn->query("DELETE FROM cart WHERE order_by='$theUserId' AND product_id='$uu_proid' AND cart_id='$cartId'");
            header("Location: ".$domainURL."cart");
        }else{

            $timezone = "Asia/Kuala_Lumpur";
            if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
            $dates = date("Y-m-d H:i:s");
            $getCart = $conn->query("SELECT * FROM cart WHERE order_by='$theUserId' AND product_id='$uu_proid' AND cart_id='$cartId'");
            $getCarts = $getCart->fetch_array();
            $newQTY = $uu_qty;
            $newPrice = $newQTY * $getCarts["unit_price"];
            $totalWeight = $newQTY * $getCarts["unit_weight"];
            $totalCapital = $newQTY * $getCarts["price_capital"];

            $updateCart = $conn->query("UPDATE cart SET quantity='$newQTY', total_price='$newPrice', total_retail_price='$newPrice', date_update='$dates', total_price_capital='$totalCapital', total_weight='$totalWeight' WHERE order_by='$theUserId' AND product_id='$uu_proid' AND cart_id='$cartId'");
            header("Location: ".$domainURL."cart");
        }



        // Close the connection
        $conn->close();
    }


}