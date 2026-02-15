<?php

namespace Ecom;

require_once __DIR__ . '/../../config/mainConfig.php';

class ecomController
{
    public function index()
    {
        if (isset($_COOKIE['country'])) {
            $country = intval($_COOKIE['country'] ?? 0);
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Main";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $newArrival = newProduct(8);

        require_once __DIR__ . '/../../view/ecom/e-main-keya88.php';
    }

    public function productDetails($id)
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Product Details";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $dataProduct = GetProductDetails($id);
        $dataCategory = getCategoryDetails($dataProduct["category_id"]);
        $dataBrand = getBrandDetails($dataProduct["brand_id"]);

        $pPrice = getPriceOnCountry($country, $id);

        $stock = stockBalanceIndividual($id);

        $productType = $dataProduct['type'] ?? 'simple';
        $allVariants = GetProductVariants($id);

        // Pre-compute stock for each variant (for variable products)
        $variantStocks = [];
        if ($productType === 'variable' && count($allVariants) > 1) {
            foreach ($allVariants as $v) {
                $variantStocks[$v['id']] = stockBalanceByVariant($v['id']);
            }
        }

        $newArrival = newProduct(8);

        $sold = itemSold($id);
        require_once __DIR__ . '/../../view/ecom/e-product-details-keya88.php';
    }

    public function detailsCat($id)
    {

        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Category";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $dataCategory = getCategoryDetails($id);

        $sql = "SELECT *  
            FROM `products` 
            WHERE `category_id`='$id' AND `status` = '1' AND `deleted_at` IS NULL 
            ORDER BY `created_at`";

        $query = $conn->query($sql);


        require_once __DIR__ . '/../../view/ecom/e-category-keya88.php';
    }

    public function detailsBrand($id)
    {

        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Brand";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $dataBrand = getBrandDetails($id);

        $sql = "SELECT *  
            FROM `products` 
            WHERE `brand_id`='$id' AND `status` = '1' AND `deleted_at` IS NULL 
            ORDER BY `created_at`";

        $query = $conn->query($sql);


        require_once __DIR__ . '/../../view/ecom/e-brand-keya88.php';
    }

    public function detailsPromo()
    {

        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Promo Item";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);


        $sqls = "
                SELECT 
                    p.id AS product_id,
                    p.name AS product_name,
                    p.slug,
                    cpp.market_price,
                    cpp.sale_price,
                    cpp.country_id
                FROM list_country_product_price cpp
                JOIN products p ON cpp.product_id = p.id
                WHERE 
                    cpp.country_id = '$country'
                    AND cpp.market_price > cpp.sale_price
                    AND p.status = '1'
                    AND p.deleted_at IS NULL
                ORDER BY (cpp.market_price - cpp.sale_price) DESC
                LIMIT 20
                ";

        $results = $conn->query($sqls);


        require_once __DIR__ . '/../../view/ecom/e-promo-keya88.php';
    }

    public function detailsOrder($id)
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }


        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Promo Item";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $sql = "
            SELECT 
                *
            FROM order_details 
            WHERE 
                hash_code = '$id'
        ";

        $result = $conn->query($sql);
        $row = $result->fetch_array();

        $orderID = $row["order_id"];

        $sqls = "
            SELECT 
                *
            FROM customer_orders 
            WHERE 
                id = '$orderID'
        ";

        $results = $conn->query($sqls);
        $rows = $results->fetch_array();



        require_once __DIR__ . '/../../view/ecom/e-order-details-keya88.php';
    }

    public function policies()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }


        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Promo Item";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $sql = "SELECT * FROM `policy`";

        $query = $conn->query($sql);

        $row = $query->fetch_array();

        require_once __DIR__ . '/../../view/ecom/e-policies-keya88.php';
    }

    public function contact()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }


        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Contact Us";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);
        require_once __DIR__ . '/../../view/ecom/e-contact-keya88.php';
    }

    public function terms()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }


        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Promo Item";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $sql = "SELECT * FROM `terms_conditions`";

        $query = $conn->query($sql);

        $row = $query->fetch_array();

        require_once __DIR__ . '/../../view/ecom/e-terms-keya88.php';
    }

    public function about()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }


        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "About Us";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $sql = "SELECT * FROM `about_us`";

        $query = $conn->query($sql);

        $row = $query->fetch_array();

        require_once __DIR__ . '/../../view/ecom/e-about-keya88.php';
    }

    public function blog()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }


        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Announcement & Blog";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $sql = "
            SELECT 
                id,
                post_by,
                update_by,
                title,
                contents,
                created_at,
                updated_at,
                deleted_at,
                reader
            FROM `2025_rozeyana`.news_blog
            WHERE deleted_at IS NULL
            ORDER BY created_at DESC
            LIMIT $limit OFFSET $offset
            ";

        $result = $conn->query($sql);

        require_once __DIR__ . '/../../view/ecom/e-blog-keya88.php';
    }

    public function trackOrder()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "About Us";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        require_once __DIR__ . '/../../view/ecom/e-track-keya88.php';
    }

    public function tracking()
    {
        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }

        $id1 = $_GET["id"];
        $id2 = $_GET["email"];

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();

        $sql = "SELECT * FROM customer_orders WHERE id='$id1' AND customer_email='$id2' AND `status` IN(1,2,3,4,5,6) AND deleted_at IS NULL";
        $query = $conn->query($sql);

        if ($query->num_rows < "1") {
?>
            <small>Result:</small>
            <br>
            <span class="text-danger">No order found.</span>
            <?php
        } else {
            $row = $query->fetch_array();
            $oid = $row["id"];
            $sql2 = "SELECT * FROM order_details WHERE order_id='$oid'";
            $query2 = $conn->query($sql2);

            if ($query2->num_rows < "1") {
            ?>
                <small>Result:</small>
                <br>
                <span class="text-danger">No order found.</span>
            <?php
            } else {
                $row2 = $query2->fetch_array();
            ?>
                <small>Result:</small>
                <br>
                <span class="text-secondary">Order found. <b>#<?= str_pad($oid, 8, '0', STR_PAD_LEFT); ?></b></span>
                <br>
                <a href="<?= $domainURL ?>order-details/<?= $row2["hash_code"] ?>" target="_blank">CLICK HERE</a> for detail order.
<?php
            }
        }
    }

    public function cartDelete()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();

        $nowSession = $_SESSION["session_id"];

        $deleteID = $_GET["id"];

        $validate = $conn->query("SELECT * FROM cart WHERE id='$deleteID' AND session_id='$nowSession' AND deleted_at IS NULL AND `status`='0'");

        if ($validate->num_rows != 1) {
            header("Location: /checkout");
            exit;
        } else {
            $update = $conn->query("UPDATE cart SET deleted_at='$dateNow', `status`='5' WHERE id='$deleteID'");
            header("Location: /checkout");
            exit;
        }
    }

    public function changeCountry()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        setcookie("country", "", time() - 3600, "/");

        header("Location: " . $domainURL);
        exit;
    }
}
