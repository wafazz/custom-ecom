<?php
namespace Shop;

require_once __DIR__ . '/../../config/mainConfig.php';

class ShopController {
    public function main() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
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

        // Fetch products from the database
        $sql = "SELECT * FROM category WHERE `status`='1' AND assigned_user LIKE '%[$theUserId]%'";
        $result = $conn->query($sql);

        if($userData["moq_kpi"] == 1){
            //$sqlProduct = "SELECT * FROM product WHERE `enable_kpi`='1' AND `status`='1'";
            $sqlProduct = "
                SELECT p.id AS product_id, p.sku AS product_sku, p.product_name AS product_name, p.product_image AS product_image, p.selling_price AS selling_price, c.cat_name AS category_name
                FROM product p
                JOIN category c ON p.category = c.id
                WHERE p.enable_kpi = '1' AND p.status = '1'
            ";
        }else if($userData["moq_kpi"] == 0){
            $sqlProduct = "
                SELECT p.id AS product_id, p.sku AS product_sku, p.product_name AS product_name, p.product_image AS product_image, p.selling_price AS selling_price, c.cat_name AS category_name
                FROM product p
                JOIN category c ON p.category = c.id
                WHERE p.enable_moq = '1' AND p.status = '1'
            ";
        }else{
            $sqlProduct = "
                SELECT p.id AS product_id, p.sku AS product_sku, p.product_name AS product_name, p.product_image AS product_image, p.selling_price AS selling_price, c.cat_name AS category_name
                FROM product p
                JOIN category c ON p.category = c.id
                WHERE p.enable_moq = '1' AND p.status = '1'
            ";
        }
        $resultProduct = $conn->query($sqlProduct);

        // Prepare the product data
        $category = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $category[] = $row;
            }
        } else {
            echo "No category found.";
        }

        $product = [];
        if ($resultProduct->num_rows > 0) {
            while ($rowProduct = $resultProduct->fetch_assoc()) {
                $product[] = $rowProduct;
            }
        } else {
            echo "No product found.";
        }

        // Close the connection
        $conn->close();

        // Include the view and pass data
        require_once __DIR__ . '/../../view/shop/shopMain.php';
    }

    // private function render($view, $data = []) {
    //     // Extract data to make variables available in the view
    //     extract($data);

    //     // Define the view file path
    //     $viewFile = "./view/shop/$view.php";

    //     // Check if the view file exists
    //     if (file_exists($viewFile)) {
    //         require $viewFile;
    //     } else {
    //         http_response_code(500);
    //         echo "View file $view.php not found.";
    //     }

        
    // }
}
