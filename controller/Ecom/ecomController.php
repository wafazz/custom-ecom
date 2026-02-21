<?php

namespace Ecom;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/Product.php';
require_once __DIR__ . '/../../model/PageContent.php';
require_once __DIR__ . '/../../model/NewsBlog.php';
require_once __DIR__ . '/../../model/OrderDetail.php';
require_once __DIR__ . '/../../model/Order.php';
require_once __DIR__ . '/../../model/Cart.php';

class ecomController
{
    private $conn;
    private $productModel;
    private $pageContentModel;
    private $newsBlogModel;
    private $orderDetailModel;
    private $orderModel;
    private $cartModel;

    public function __construct()
    {
        $this->conn = getDbConnection();
        $this->productModel = new \Product($this->conn);
        $this->pageContentModel = new \PageContent($this->conn);
        $this->newsBlogModel = new \NewsBlog($this->conn);
        $this->orderDetailModel = new \OrderDetail($this->conn);
        $this->orderModel = new \Order($this->conn);
        $this->cartModel = new \Cart($this->conn);
    }

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
        $conn = $this->conn;
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
        $conn = $this->conn;
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
        $conn = $this->conn;
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Category";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $dataCategory = getCategoryDetails($id);

        $query = $this->productModel->getByCategoryId($id);

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
        $conn = $this->conn;
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Brand";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $dataBrand = getBrandDetails($id);

        $query = $this->productModel->getByBrandId($id);

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
        $conn = $this->conn;
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Promo Item";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $results = $this->productModel->getPromoItems($country);

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
        $conn = $this->conn;
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Promo Item";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $row = $this->orderDetailModel->findByHashCode($id);

        $orderID = $row["order_id"];

        $rows = $this->orderModel->getOrderDetails($orderID);

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
        $conn = $this->conn;
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Promo Item";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $row = $this->pageContentModel->getContent('policy');

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
        $conn = $this->conn;
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
        $conn = $this->conn;
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Promo Item";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $row = $this->pageContentModel->getContent('terms_conditions');

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
        $conn = $this->conn;
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "About Us";

        $data = dataCountry($country);

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);
        $categories3 = getListCategoryBrand2(2);

        $row = $this->pageContentModel->getContent('about_us');

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
        $conn = $this->conn;
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

        $result = $this->newsBlogModel->getPaginated($limit, $offset);

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
        $conn = $this->conn;
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
        $conn = $this->conn;
        $currentYear = currentYear();
        $dateNow = dateNow();

        $row = $this->orderModel->findByIdAndEmail($id1, $id2);

        if (!$row) {
?>
            <small>Result:</small>
            <br>
            <span class="text-danger">No order found.</span>
            <?php
        } else {
            $oid = $row["id"];
            $row2 = $this->orderDetailModel->findByOrderId($oid);

            if (!$row2) {
            ?>
                <small>Result:</small>
                <br>
                <span class="text-danger">No order found.</span>
            <?php
            } else {
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
        $dateNow = dateNow();

        $nowSession = $_SESSION["session_id"];
        $deleteID = $_GET["id"];

        $validate = $this->cartModel->findActiveByIdAndSession($deleteID, $nowSession);

        if (!$validate) {
            header("Location: /checkout");
            exit;
        } else {
            $this->cartModel->softDeleteWithStatus($deleteID, $dateNow, '5');
            header("Location: /checkout");
            exit;
        }
    }

    public function changeCountry()
    {
        $domainURL = getMainUrl();
        setcookie("country", "", time() - 3600, "/");

        header("Location: " . $domainURL);
        exit;
    }
}
