<?php
namespace Product;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/Category.php';
require_once __DIR__ . '/../../model/Brand.php';
require_once __DIR__ . '/../../model/ProductVariant.php';
require_once __DIR__ . '/../../model/ProductImage.php';
require_once __DIR__ . '/../../model/CountryPrice.php';
require_once __DIR__ . '/../../model/StockControl.php';

class ProductController {

    private $domainURL;
    private $mainDomain;
    private $conn;
    private $currentYear;
    private $dateNow;

    private $categoryModel;
    private $brandModel;
    private $variantModel;
    private $imageModel;
    private $countryPriceModel;
    private $stockControlModel;

    public function __construct()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $this->domainURL   = getMainUrl();
        $this->mainDomain  = mainDomain();
        $this->conn        = getDbConnection();
        $this->currentYear = currentYear();
        $this->dateNow     = dateNow();

        $this->categoryModel     = new \Category($this->conn);
        $this->brandModel        = new \Brand($this->conn);
        $this->variantModel      = new \ProductVariant($this->conn);
        $this->imageModel        = new \ProductImage($this->conn);
        $this->countryPriceModel = new \CountryPrice($this->conn);
        $this->stockControlModel = new \StockControl($this->conn);
    }

    private function checkAccess($segment = null)
    {
        if ($segment === null) {
            $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
            $segments = explode('/', $currentPaths);
            $segment = $segments[0];
        }
        if (roleVerify($segment, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            exit;
        }
    }

    public function newProduct() {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $options     = getSelectOptions();
        $country     = allSaleCountry();
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;

        $pageName = "New Product";

        if (isset($_POST['mp']) && isset($_POST['sp'])) {

            $pname = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $description = $_POST['description'] ?? '';
            $type = $_POST['type'] ?? '';
            $category_id = $_POST['category'] ?? null;
            $brand_id = $_POST['brand'] ?? null;
            $sku = $_POST['sku'] ?? null;
            $maxP = $_POST['maxP'] ?? null;
            $weight = $_POST['weight'] ?? null;
            $length = $_POST['length'] ?? null;
            $width = $_POST['width'] ?? null;
            $height = $_POST['height'] ?? null;
            $capPrice = $_POST['capPrice'] ?? null;
            $status = 1;

            $uploadDir = 'assets/images/products/'.$this->currentYear.'/';
            $uploadedFiles = [];
            $errors = [];
            $maxSize = 10 * 1024 * 1024;

            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['upload_error'] = 'Failed to create upload directory.';
                    header("Location: new-product");
                    exit;
                }
            }

            if (!empty($_FILES['files']['name'][0])) {
                foreach ($_FILES['files']['name'] as $key => $name) {
                    $tmpName = $_FILES['files']['tmp_name'][$key];
                    $error = $_FILES['files']['error'][$key];
                    $size = $_FILES['files']['size'][$key];
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if ($error !== UPLOAD_ERR_OK) {
                        $errors[] = "Error uploading: " . htmlspecialchars($name);
                        continue;
                    }
                    if ($size > $maxSize) {
                        $errors[] = "File too large (max 10MB): " . htmlspecialchars($name);
                        continue;
                    }
                    if (!in_array($ext, $allowed)) {
                        $errors[] = "Invalid file type: " . htmlspecialchars($name);
                        continue;
                    }

                    $uniqueName = uniqid('img_', true) . '.' . $ext;
                    $destination = $uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $uploadedFiles[] = $uniqueName;
                    } else {
                        $errors[] = "Failed to save: " . htmlspecialchars($name);
                    }
                }

                if (empty($errors)) {
                    $stmt = $this->conn->prepare("INSERT INTO `products` (`name`, `slug`, `description`, `type`, `category_id`, `brand_id`, `price_capital`, `status`, `weight`, `length`, `width`, `height`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssssssissssss", $pname, $slug, $description, $type, $category_id, $brand_id, $capPrice, $status, $weight, $length, $width, $height, $this->dateNow, $this->dateNow);
                    $stmt->execute();
                    $productId = $stmt->insert_id;
                    $stmt->close();

                    if ($type === 'variable' && !empty($_POST['variants'])) {
                        foreach ($_POST['variants'] as $variant) {
                            $vSku = $variant['sku'] ?? '';
                            if (!$this->variantModel->checkSkuUnique($vSku)) {
                                $_SESSION['upload_error'] = 'SKU must be UNIQUE. Please use another.';
                                header("Location: new-product");
                                exit;
                            }
                        }
                        foreach ($_POST['variants'] as $variant) {
                            $this->variantModel->createVariant([
                                'product_id'   => $productId,
                                'variant_name' => $variant['name'] ?? '',
                                'sku'          => $variant['sku'] ?? '',
                                'max_purchase' => intval($variant['maxP'] ?? 1),
                                'created_at'   => $this->dateNow,
                                'updated_at'   => $this->dateNow,
                            ]);
                        }
                    } else {
                        $this->variantModel->createVariant([
                            'product_id'   => $productId,
                            'variant_name' => null,
                            'sku'          => $sku,
                            'max_purchase' => $maxP,
                            'created_at'   => $this->dateNow,
                            'updated_at'   => $this->dateNow,
                        ]);
                    }

                    foreach ($uploadedFiles as $file) {
                        $dirFile = $this->currentYear . "/" . $file;
                        $this->imageModel->addImage($productId, $dirFile, $this->dateNow);
                    }

                    foreach ($_POST['mp'] as $countryId => $marketPrice) {
                        $salePrice = $_POST['sp'][$countryId];
                        $dataCountry = getCountry($countryId);
                        if ($dataCountry && $dataCountry->num_rows > 0) {
                            $row = $dataCountry->fetch_assoc();
                        }
                        $marketPrice = number_format($marketPrice, 2, '.', '');
                        $salePrice = number_format($salePrice, 2, '.', '');
                        $this->countryPriceModel->insertPrice($countryId, $productId, $marketPrice, $salePrice, $this->dateNow);
                    }

                    $_SESSION['upload_success'] = "Product and images uploaded successfully.";
                    header("Location: new-product");
                    exit;
                } else {
                    foreach ($uploadedFiles as $file) {
                        @unlink($uploadDir . $file);
                    }
                    $_SESSION['upload_error'] = implode('<br>', $errors);
                    header("Location: new-product");
                    exit;
                }
            } else {
                $_SESSION['upload_error'] = "No files selected.";
                header("Location: new-product");
                exit;
            }
        } else {
            require_once __DIR__ . '/../../view/Admin/new-product.php';
        }
    }

    public function stockControl() {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $dateNow     = $this->dateNow;

        $pageName = "Stock Control";
        $productImageDIR = $this->domainURL . "assets/images/products/";

        $stockRows = $this->stockControlModel->getStockSummary();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $var_id = $_POST["product_id"];
            $type = $_POST["type"];
            $qty = $_POST["qty"];

            $dRow = $this->variantModel->getById($var_id);
            $pid = $dRow["product_id"];
            $vid = $var_id;

            $updater = $_SESSION['user']->id . " : " . $_SESSION['user']->f_name;

            if ($type == "1") {
                $this->stockControlModel->addStock($pid, $vid, $qty, "Updated stock (ADDED) by ($updater)", $this->dateNow);
                $_SESSION['upload_success'] = 'Successful updated (ADD) stock';
            } else {
                $this->stockControlModel->deductStock($pid, $vid, $qty, "Updated stock (DEDUCTED) by ($updater)", $this->dateNow);
                $_SESSION['upload_success'] = 'Successful updated (DEDUCT) stock';
            }
            header("Location: " . $this->domainURL . "stock-control");
            exit;
        }

        require_once __DIR__ . '/../../view/Admin/stock-control.php';
    }

    public function updateProduct($id) {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;
        $pageName    = "Update Product";

        $options = getSelectOptions();
        $country = allSaleCountry();

        $product = GetProductDetails($id);

        $selectedBrandId = $product['brand_id'] ?? null;
        $selectedCategoryId = $product['category_id'] ?? null;
        $options = getSelectOptions($selectedBrandId, $selectedCategoryId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pname = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $description = $_POST['description'] ?? '';
            $type = $_POST['type'] ?? '';
            $category_id = $_POST['category'] ?? null;
            $brand_id = $_POST['brand'] ?? null;
            $sku = $_POST['sku'] ?? null;
            $maxP = $_POST['maxP'] ?? null;
            $weight = $_POST['weight'] ?? null;
            $length = $_POST['length'] ?? null;
            $width = $_POST['width'] ?? null;
            $height = $_POST['height'] ?? null;
            $capPrice = $_POST['capPrice'] ?? null;
            $status = 1;
            $existingImages = $_POST['existing_images'] ?? [];
            $variant_id = $product["variant_id"];

            // SKU uniqueness check
            if ($type === 'simple') {
                if (!$this->variantModel->checkSkuUnique($sku, $variant_id)) {
                    $_SESSION['upload_error'] = 'SKU must be UNIQUE. Please use another.';
                    header("Location: " . $this->domainURL . "update-product/" . $id);
                    exit;
                }
            } elseif ($type === 'variable' && !empty($_POST['variants'])) {
                foreach ($_POST['variants'] as $variant) {
                    $vSku = $variant['sku'] ?? '';
                    $vId = isset($variant['id']) ? intval($variant['id']) : 0;
                    if (!$this->variantModel->checkSkuUnique($vSku, $vId)) {
                        $_SESSION['upload_error'] = 'SKU must be UNIQUE. Please use another.';
                        header("Location: " . $this->domainURL . "update-product/" . $id);
                        exit;
                    }
                }
            }

            $uploadDir = 'assets/images/products/' . $this->currentYear . '/';
            $uploadedFiles = [];
            $errors = [];
            $maxSize = 10 * 1024 * 1024;

            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['upload_error'] = 'Failed to create upload directory.';
                    header("Location: " . $this->domainURL . "update-product/" . $id);
                    exit;
                }
            }

            if (!empty($_FILES['files']['name'][0])) {
                foreach ($_FILES['files']['name'] as $key => $name) {
                    $tmpName = $_FILES['files']['tmp_name'][$key];
                    $error = $_FILES['files']['error'][$key];
                    $size = $_FILES['files']['size'][$key];
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if ($error !== UPLOAD_ERR_OK) { $errors[] = "Error uploading: " . htmlspecialchars($name); continue; }
                    if ($size > $maxSize) { $errors[] = "File too large (max 10MB): " . htmlspecialchars($name); continue; }
                    if (!in_array($ext, $allowed)) { $errors[] = "Invalid file type: " . htmlspecialchars($name); continue; }

                    $uniqueName = time() . "_" . uniqid('img_', true) . '.' . $ext;
                    $destination = $uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $uploadedFiles[] = $uniqueName;
                    } else {
                        $errors[] = "Failed to save: " . htmlspecialchars($name);
                    }
                }

                if (empty($errors)) {
                    $productId = $id;

                    $this->updateProductRecord($productId, $pname, $slug, $description, $type, $category_id, $brand_id, $capPrice, $status, $weight, $length, $width, $height);
                    $this->processVariants($type, $productId, $sku, $maxP, $status);
                    $this->imageModel->deleteOrphanImages($productId, $existingImages);

                    foreach ($uploadedFiles as $file) {
                        $dirFile = $this->currentYear . "/" . $file;
                        $this->imageModel->addImage($productId, $dirFile, $this->dateNow);
                    }

                    $this->processCountryPrices($productId, false);

                    $_SESSION['upload_success'] = "Product and images uploaded successfully.";
                    header("Location: " . $this->domainURL . "update-product/" . $id);
                    exit;
                } else {
                    foreach ($uploadedFiles as $file) {
                        @unlink($uploadDir . $file);
                    }
                    $_SESSION['upload_error'] = implode('<br>', $errors);
                    header("Location: " . $this->domainURL . "update-product/" . $id);
                    exit;
                }
            } else {
                $productId = $id;

                $this->updateProductRecord($productId, $pname, $slug, $description, $type, $category_id, $brand_id, $capPrice, $status, $weight, $length, $width, $height);
                $this->processVariants($type, $productId, $sku, $maxP, $status);
                $this->processCountryPrices($productId, false);
                $this->imageModel->deleteOrphanImages($productId, $existingImages);

                $_SESSION['upload_success'] = "Product and images uploaded successfully.";
                header("Location: " . $this->domainURL . "update-product/" . $id);
                exit;
            }
        }

        require_once __DIR__ . '/../../view/Admin/update-product.php';
    }

    private function updateProductRecord($productId, $pname, $slug, $description, $type, $category_id, $brand_id, $capPrice, $status, $weight, $length, $width, $height)
    {
        $stmt = $this->conn->prepare("UPDATE `products` SET `name`=?, `slug`=?, `description`=?, `type`=?, `category_id`=?, `brand_id`=?, `price_capital`=?, `status`=?, `weight`=?, `length`=?, `width`=?, `height`=?, `updated_at`=? WHERE id=?");
        $stmt->bind_param("sssssssisssssi", $pname, $slug, $description, $type, $category_id, $brand_id, $capPrice, $status, $weight, $length, $width, $height, $this->dateNow, $productId);
        $stmt->execute();
        $stmt->close();
    }

    private function processVariants($type, $productId, $sku, $maxP, $status)
    {
        if ($type === 'variable' && !empty($_POST['variants'])) {
            $submittedIds = [];
            foreach ($_POST['variants'] as $variant) {
                $vName = $variant['name'] ?? '';
                $vSku = $variant['sku'] ?? '';
                $vMaxP = intval($variant['maxP'] ?? 1);
                if (!empty($variant['id'])) {
                    $vId = intval($variant['id']);
                    $submittedIds[] = $vId;
                    $this->variantModel->updateVariant($vId, $productId, [
                        'variant_name' => $vName,
                        'sku'          => $vSku,
                        'max_purchase' => $vMaxP,
                        'updated_at'   => $this->dateNow,
                    ]);
                } else {
                    $newId = $this->variantModel->createVariant([
                        'product_id'   => $productId,
                        'variant_name' => $vName,
                        'sku'          => $vSku,
                        'max_purchase' => $vMaxP,
                        'created_at'   => $this->dateNow,
                        'updated_at'   => $this->dateNow,
                    ]);
                    $submittedIds[] = $newId;
                }
            }
            if (!empty($submittedIds)) {
                $this->variantModel->softDeleteExcept($productId, $submittedIds, $this->dateNow);
            }
        } else {
            $this->variantModel->updateByProduct($productId, [
                'sku'          => $sku,
                'max_purchase' => $maxP,
                'status'       => $status,
                'updated_at'   => $this->dateNow,
            ]);
        }
    }

    private function processCountryPrices($productId, $isNew = true)
    {
        foreach ($_POST['mp'] as $countryId => $marketPrice) {
            $salePrice = $_POST['sp'][$countryId];
            $dataCountry = getCountry($countryId);
            if ($dataCountry && $dataCountry->num_rows > 0) {
                $row = $dataCountry->fetch_assoc();
            }
            $marketPrice = number_format($marketPrice, 2, '.', '');
            $salePrice = number_format($salePrice, 2, '.', '');

            if ($isNew) {
                $this->countryPriceModel->insertPrice($countryId, $productId, $marketPrice, $salePrice, $this->dateNow);
            } else {
                $this->countryPriceModel->updatePrice($countryId, $productId, $marketPrice, $salePrice, $this->dateNow);
            }
        }
    }

    public function deleteProduct($id)
    {
        $this->checkAccess();

        $newdate = new \DateTime($this->dateNow);
        $formattedDate = $newdate->format('j F, Y h:i A');

        $product = GetProductDetails($id);

        $stmt = $this->conn->prepare("UPDATE `products` SET `status`='2', `deleted_at`=? WHERE `id`=?");
        $stmt->bind_param("si", $this->dateNow, $id);
        $stmt->execute();
        $stmt->close();

        $this->variantModel->softDeleteByProduct($id, $this->dateNow);

        $_SESSION['upload_success'] = "Successfull deleted <b>'" . $product["name"] . "'</b> on " . $formattedDate;
        header("Location: " . $this->domainURL . "stock-control");
        exit;
    }

    public function productCategory()
    {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;
        $pageName    = "Category - Add & Update";
        $nameBtn     = "Category";

        $categories = $this->categoryModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pname = $_POST['name'] ?? '';

            $uploadDir = 'assets/images/brand-category/' . $this->currentYear . '/';
            $uploadedFiles = [];
            $errors = [];
            $maxSize = 10 * 1024 * 1024;

            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['upload_error'] = 'Failed to create upload directory.';
                    header("Location: " . $this->domainURL . "category-product");
                    exit;
                }
            }

            if (!empty($_FILES['files']['name'][0])) {
                foreach ($_FILES['files']['name'] as $key => $name) {
                    $tmpName = $_FILES['files']['tmp_name'][$key];
                    $error = $_FILES['files']['error'][$key];
                    $size = $_FILES['files']['size'][$key];
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if ($error !== UPLOAD_ERR_OK) { $errors[] = "Error uploading: " . htmlspecialchars($name); continue; }
                    if ($size > $maxSize) { $errors[] = "File too large (max 10MB): " . htmlspecialchars($name); continue; }
                    if (!in_array($ext, $allowed)) { $errors[] = "Invalid file type: " . htmlspecialchars($name); continue; }

                    $uniqueName = time() . "_" . uniqid('img_', true) . '.' . $ext;
                    $destination = $uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $uploadedFiles[] = $uniqueName;
                    } else {
                        $errors[] = "Failed to save: " . htmlspecialchars($name);
                    }
                }

                if (empty($errors)) {
                    foreach ($uploadedFiles as $file) {
                        $string = ltrim($pname);
                        $theImage = $this->currentYear . "/" . $file;
                        $this->categoryModel->createCategory([
                            'name'        => $pname,
                            'slug'        => $string,
                            'image'       => $theImage,
                            'description' => '-',
                            'created_at'  => $this->dateNow,
                            'updated_at'  => $this->dateNow,
                        ]);
                    }

                    $_SESSION['upload_success'] = "Category and images uploaded successfully.";
                    header("Location: " . $this->domainURL . "category-product");
                    exit;
                } else {
                    $_SESSION['upload_error'] = implode('<br>', $errors);
                    header("Location: " . $this->domainURL . "category-product");
                    exit;
                }
            }
        }

        require_once __DIR__ . '/../../view/Admin/category-product.php';
    }

    public function productBrand()
    {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;
        $pageName    = "Brand - Add & Update";
        $nameBtn     = "Brand";

        $brands = $this->brandModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pname = $_POST['name'] ?? '';

            $uploadDir = 'assets/images/brand-category/' . $this->currentYear . '/';
            $uploadedFiles = [];
            $errors = [];
            $maxSize = 10 * 1024 * 1024;

            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['upload_error'] = 'Failed to create upload directory.';
                    header("Location: " . $this->domainURL . "brand-product");
                    exit;
                }
            }

            if (!empty($_FILES['files']['name'][0])) {
                foreach ($_FILES['files']['name'] as $key => $name) {
                    $tmpName = $_FILES['files']['tmp_name'][$key];
                    $error = $_FILES['files']['error'][$key];
                    $size = $_FILES['files']['size'][$key];
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if ($error !== UPLOAD_ERR_OK) { $errors[] = "Error uploading: " . htmlspecialchars($name); continue; }
                    if ($size > $maxSize) { $errors[] = "File too large (max 10MB): " . htmlspecialchars($name); continue; }
                    if (!in_array($ext, $allowed)) { $errors[] = "Invalid file type: " . htmlspecialchars($name); continue; }

                    $uniqueName = time() . "_" . uniqid('img_', true) . '.' . $ext;
                    $destination = $uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $uploadedFiles[] = $uniqueName;
                    } else {
                        $errors[] = "Failed to save: " . htmlspecialchars($name);
                    }
                }

                if (empty($errors)) {
                    foreach ($uploadedFiles as $file) {
                        $string = ltrim($pname);
                        $string1 = preg_replace('/\s+/', '_', $string);
                        $string2 = strtolower($string1);
                        $theImage = $this->currentYear . "/" . $file;
                        $this->brandModel->createBrand([
                            'name'        => $pname,
                            'slug'        => $string2,
                            'image'       => $theImage,
                            'description' => '-',
                            'created_at'  => $this->dateNow,
                            'updated_at'  => $this->dateNow,
                        ]);
                    }

                    $_SESSION['upload_success'] = "Brand and images uploaded successfully.";
                    header("Location: " . $this->domainURL . "brand-product");
                    exit;
                } else {
                    $_SESSION['upload_error'] = implode('<br>', $errors);
                    header("Location: " . $this->domainURL . "brand-product");
                    exit;
                }
            }
        }

        require_once __DIR__ . '/../../view/Admin/brand-product.php';
    }

    public function updateCategory($id)
    {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;
        $pageName    = "Category - Update";
        $data        = getCategoryBrand($id, 1);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pname = $_POST['name'] ?? '';

            $uploadDir = 'assets/images/brand-category/' . $this->currentYear . '/';
            $uploadedFiles = [];
            $errors = [];
            $maxSize = 10 * 1024 * 1024;

            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['upload_error'] = 'Failed to create upload directory.';
                    header("Location: " . $this->domainURL . "update-category/" . $id);
                    exit;
                }
            }

            if (!empty($_FILES['files']['name'][0])) {
                foreach ($_FILES['files']['name'] as $key => $name) {
                    $tmpName = $_FILES['files']['tmp_name'][$key];
                    $error = $_FILES['files']['error'][$key];
                    $size = $_FILES['files']['size'][$key];
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if ($error !== UPLOAD_ERR_OK) { $errors[] = "Error uploading: " . htmlspecialchars($name); continue; }
                    if ($size > $maxSize) { $errors[] = "File too large (max 10MB): " . htmlspecialchars($name); continue; }
                    if (!in_array($ext, $allowed)) { $errors[] = "Invalid file type: " . htmlspecialchars($name); continue; }

                    $uniqueName = time() . "_" . uniqid('img_', true) . '.' . $ext;
                    $destination = $uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $uploadedFiles[] = $uniqueName;
                    } else {
                        $errors[] = "Failed to save: " . htmlspecialchars($name);
                    }
                }

                if (empty($errors)) {
                    foreach ($uploadedFiles as $file) {
                        $theImage = $this->currentYear . "/" . $file;
                        $this->categoryModel->updateCategory($id, [
                            'name'       => $pname,
                            'image'      => $theImage,
                            'updated_at' => $this->dateNow,
                        ]);
                    }

                    $_SESSION['upload_success'] = "Category and images uploaded successfully.";
                    header("Location: " . $this->domainURL . "update-category/" . $id);
                    exit;
                } else {
                    $_SESSION['upload_error'] = implode('<br>', $errors);
                    header("Location: " . $this->domainURL . "update-category/" . $id);
                    exit;
                }
            } else {
                $this->categoryModel->updateCategory($id, [
                    'name'       => $pname,
                    'updated_at' => $this->dateNow,
                ]);

                $_SESSION['upload_success'] = "Category successfully updated.";
                header("Location: " . $this->domainURL . "update-category/" . $id);
                exit;
            }
        } else {
            require_once __DIR__ . '/../../view/Admin/update-category.php';
        }
    }

    public function updateBrand($id)
    {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;
        $pageName    = "Brand - Update";
        $data        = getCategoryBrand($id, 2);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pname = $_POST['name'] ?? '';

            $uploadDir = 'assets/images/brand-category/' . $this->currentYear . '/';
            $uploadedFiles = [];
            $errors = [];
            $maxSize = 10 * 1024 * 1024;

            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['upload_error'] = 'Failed to create upload brand.';
                    header("Location: " . $this->domainURL . "update-brand/" . $id);
                    exit;
                }
            }

            if (!empty($_FILES['files']['name'][0])) {
                foreach ($_FILES['files']['name'] as $key => $name) {
                    $tmpName = $_FILES['files']['tmp_name'][$key];
                    $error = $_FILES['files']['error'][$key];
                    $size = $_FILES['files']['size'][$key];
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if ($error !== UPLOAD_ERR_OK) { $errors[] = "Error uploading: " . htmlspecialchars($name); continue; }
                    if ($size > $maxSize) { $errors[] = "File too large (max 10MB): " . htmlspecialchars($name); continue; }
                    if (!in_array($ext, $allowed)) { $errors[] = "Invalid file type: " . htmlspecialchars($name); continue; }

                    $uniqueName = time() . "_" . uniqid('img_', true) . '.' . $ext;
                    $destination = $uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $uploadedFiles[] = $uniqueName;
                    } else {
                        $errors[] = "Failed to save: " . htmlspecialchars($name);
                    }
                }

                if (empty($errors)) {
                    foreach ($uploadedFiles as $file) {
                        $theImage = $this->currentYear . "/" . $file;
                        $this->brandModel->updateBrand($id, [
                            'name'       => $pname,
                            'image'      => $theImage,
                            'updated_at' => $this->dateNow,
                        ]);
                    }

                    $_SESSION['upload_success'] = "Brand and images uploaded successfully.";
                    header("Location: " . $this->domainURL . "update-brand/" . $id);
                    exit;
                } else {
                    $_SESSION['upload_error'] = implode('<br>', $errors);
                    header("Location: " . $this->domainURL . "update-brand/" . $id);
                    exit;
                }
            } else {
                $this->brandModel->updateBrand($id, [
                    'name'       => $pname,
                    'updated_at' => $this->dateNow,
                ]);

                $_SESSION['upload_success'] = "Brand successfully updated.";
                header("Location: " . $this->domainURL . "update-brand/" . $id);
                exit;
            }
        } else {
            require_once __DIR__ . '/../../view/Admin/update-brand.php';
        }
    }

    public function deleteBrand($id)
    {
        $this->checkAccess();

        $data = getCategoryBrand($id, 2);

        if (is_null($data['deleted_at'])) {
            $this->brandModel->softDeleteBrand($id, $this->dateNow);
            $_SESSION['upload_success'] = "Brand '" . $data['name'] . "' successfully deleted.";
            header("Location: " . $this->domainURL . "brand-product");
            exit;
        } else {
            echo "Deleted on " . $data['deleted_at'];
        }
    }

    public function deleteCategory($id)
    {
        $this->checkAccess();

        $data = getCategoryBrand($id, 1);

        if (is_null($data['deleted_at'])) {
            $this->categoryModel->softDeleteCategory($id, $this->dateNow);
            $_SESSION['upload_success'] = "Category '" . $data['name'] . "' successfully deleted.";
            header("Location: " . $this->domainURL . "category-product");
            exit;
        } else {
            echo "Deleted on " . $data['deleted_at'];
        }
    }

    public function bulkDeleteCategory()
    {
        $ids = $_POST['ids'] ?? [];
        if (empty($ids)) {
            echo json_encode(['success' => false, 'message' => 'No categories selected']);
            return;
        }

        $deleted = 0;
        $skipped = 0;
        foreach ($ids as $id) {
            $id = intval($id);
            $cnt = $this->categoryModel->countProducts($id);
            if ($cnt > 0) {
                $skipped++;
                continue;
            }
            $this->categoryModel->softDeleteCategory($id, $this->dateNow);
            $deleted++;
        }

        $msg = "{$deleted} category(s) deleted.";
        if ($skipped > 0) {
            $msg .= " {$skipped} skipped (has products).";
        }

        echo json_encode(['success' => true, 'message' => $msg]);
    }

    public function bulkDeleteBrand()
    {
        $ids = $_POST['ids'] ?? [];
        if (empty($ids)) {
            echo json_encode(['success' => false, 'message' => 'No brands selected']);
            return;
        }

        $deleted = 0;
        $skipped = 0;
        foreach ($ids as $id) {
            $id = intval($id);
            $cnt = $this->brandModel->countProducts($id);
            if ($cnt > 0) {
                $skipped++;
                continue;
            }
            $this->brandModel->softDeleteBrand($id, $this->dateNow);
            $deleted++;
        }

        $msg = "{$deleted} brand(s) deleted.";
        if ($skipped > 0) {
            $msg .= " {$skipped} skipped (has products).";
        }

        echo json_encode(['success' => true, 'message' => $msg]);
    }
}
