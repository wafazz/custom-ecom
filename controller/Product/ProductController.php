<?php
namespace Product;

require_once __DIR__ . '/../../config/mainConfig.php';

class ProductController {
    public function newProduct() {

        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: ".$domainURL."access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $options = getSelectOptions();
        $country = allSaleCountry();

        $pageName = "New Product";

        $currentYear = currentYear();
        $dateNow = dateNow();

        

        //echo "welcome back ".$_SESSION['user']->f_name." ".$_SESSION['user']->l_name." ".$domainURL;
        if (isset($_POST['mp']) && isset($_POST['sp'])) {

            $conn = getDbConnection();
            //$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

            $uploadDir = 'assets/images/products/'.$currentYear.'/';
            $uploadedFiles = [];
            $errors = [];
            $maxSize = 10 * 1024 * 1024; // 10MB in bytes

            // Create upload directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['upload_error'] = 'Failed to create upload directory.';
                    header("Location: new-product");
                    exit;
                }
            }

            // Check if files are uploaded
            if (!empty($_FILES['files']['name'][0])) {
                foreach ($_FILES['files']['name'] as $key => $name) {
                    $tmpName = $_FILES['files']['tmp_name'][$key];
                    $error = $_FILES['files']['error'][$key];
                    $size = $_FILES['files']['size'][$key];
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    // Error during upload
                    if ($error !== UPLOAD_ERR_OK) {
                        $errors[] = "Error uploading: " . htmlspecialchars($name);
                        continue;
                    }

                    // Size check
                    if ($size > $maxSize) {
                        $errors[] = "File too large (max 10MB): " . htmlspecialchars($name);
                        continue;
                    }

                    // Type check
                    if (!in_array($ext, $allowed)) {
                        $errors[] = "Invalid file type: " . htmlspecialchars($name);
                        continue;
                    }

                    // Move file
                    $uniqueName = uniqid('img_', true) . '.' . $ext;
                    $destination = $uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $uploadedFiles[] = $uniqueName;
                    } else {
                        $errors[] = "Failed to save: " . htmlspecialchars($name);
                    }
                }

                // Final decision: success or rollback
                if (empty($errors)) {
                    $stmt = $conn->query("INSERT INTO `products`(`id`, `name`, `slug`, `description`, `type`, `category_id`, `brand_id`, `price_capital`, `status`, `weight`, `length`, `width`, `height`, `created_at`, `updated_at`) VALUES (NULL,'$pname','$slug','$description','$type','$category_id','$brand_id','$capPrice','$status','$weight','$length','$width','$height','$dateNow','$dateNow')");

                    

                    $productId = $conn->insert_id;

                    if ($type === 'variable' && !empty($_POST['variants'])) {
                        // Validate SKU uniqueness for variable products
                        $skuError = false;
                        foreach ($_POST['variants'] as $variant) {
                            $vSku = $conn->real_escape_string($variant['sku'] ?? '');
                            $checkSku = $conn->query("SELECT id FROM product_variants WHERE sku = '$vSku'");
                            if ($checkSku->num_rows > 0) {
                                $skuError = true;
                                break;
                            }
                        }
                        if ($skuError) {
                            $_SESSION['upload_error'] = 'SKU must be UNIQUE. Please use another.';
                            header("Location: new-product");
                            exit;
                        }
                        foreach ($_POST['variants'] as $variant) {
                            $vName = $conn->real_escape_string($variant['name'] ?? '');
                            $vSku = $conn->real_escape_string($variant['sku'] ?? '');
                            $vMaxP = intval($variant['maxP'] ?? 1);
                            $conn->query("INSERT INTO `product_variants`(`id`, `product_id`, `variant_name`, `sku`, `price_retail`, `price_sale`, `stock`, `image`, `max_purchase`, `status`, `created_at`, `updated_at`) VALUES (NULL,'$productId','$vName','$vSku','0.00','0.00','0','-','$vMaxP','1','$dateNow','$dateNow')");
                        }
                    } else {
                        $conn->query("INSERT INTO `product_variants`(`id`, `product_id`, `variant_name`, `sku`, `price_retail`, `price_sale`, `stock`, `image`, `max_purchase`, `status`, `created_at`, `updated_at`) VALUES (NULL,'$productId',NULL,'$sku','0.00','0.00','0','-','$maxP','1','$dateNow','$dateNow')");
                    }


                    foreach ($uploadedFiles as $file) {
                        $dirFile = $currentYear."/".$file;
                        $stmts = $conn->query("INSERT INTO `product_image`(`id`, `product_id`, `image`, `created_at`) VALUES (NULL,'$productId','$dirFile','$dateNow')");
                    }

                    foreach ($_POST['mp'] as $countryId => $marketPrice) {
                        $salePrice = $_POST['sp'][$countryId];
        
                        $dataCountry = getCountry($countryId);
        
                        if ($dataCountry && $dataCountry->num_rows > 0) {
                            $row = $dataCountry->fetch_assoc();
                            // use $row["name"], $row["sign"], etc.
                        } else {
                            echo "Country not found.";
                        }
                        // Sanitize and validate values
                        $marketPrice = number_format($marketPrice, 2, '.', '');
                        $salePrice = number_format($salePrice, 2, '.', '');
                
                        $stmtss = $conn->query("INSERT INTO `list_country_product_price`(`id`, `country_id`, `product_id`, `market_price`, `sale_price`, `created_at`, `updated_at`) VALUES (NULL,'$countryId','$productId','$marketPrice','$salePrice','$dateNow','$dateNow')");
                    }

                    $_SESSION['upload_success'] = "Product and images uploaded successfully.";
                    // Redirect back to form
                    header("Location: new-product");
                    exit;
                } else {
                    // Delete any uploaded files on failure
                    foreach ($uploadedFiles as $file) {
                        @unlink($uploadDir . $file);
                    }
                    $_SESSION['upload_error'] = implode('<br>', $errors);
                    // Redirect back to form
                    header("Location: new-product");
                    exit;
                }
            } else {
                $_SESSION['upload_error'] = "No files selected.";
                // Redirect back to form
                header("Location: new-product");
                exit;
            }

            
            
        }else{
            require_once __DIR__ . '/../../view/Admin/new-product.php';
        }

        
    }

    public function stockControl(){
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $dateNow = dateNow();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: ".$domainURL."access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $pageName = "Stock Control";

        $productImageDIR = $domainURL."assets/images/products/";

        $sql = "
            SELECT 
                p.id AS product_id,
                p.name AS product_name,
                p.slug,
                p.description,
                p.type,
                p.category_id,
                p.brand_id,
                p.price_capital,
                p.status AS product_status,

                pv.id AS variant_id,
                pv.variant_name,
                pv.sku,
                pv.price_retail,
                pv.price_sale,
                pv.stock AS variant_stock,
                pv.image AS variant_image,
                pv.max_purchase,

                (
                    SELECT pi.image 
                    FROM product_image pi 
                    WHERE pi.product_id = p.id 
                    ORDER BY pi.id ASC 
                    LIMIT 1
                ) AS product_image,

                IFNULL(SUM(sc.stock_in), 0) AS total_stock_in,
                IFNULL(SUM(sc.stock_out), 0) AS total_stock_out,

                IFNULL((
                    SELECT SUM(c.quantity) 
                    FROM cart c 
                    WHERE c.pv_id = pv.id AND c.status IN (0,1)
                ), 0) AS stock_reserved,

                IFNULL((
                    SELECT SUM(c.quantity) 
                    FROM cart c 
                    WHERE c.pv_id = pv.id AND c.status = 1
                ), 0) AS total_sold,

                (IFNULL(SUM(sc.stock_in), 0) - IFNULL(SUM(sc.stock_out), 0) - 
                    IFNULL((
                        SELECT SUM(c.quantity) 
                        FROM cart c 
                        WHERE c.pv_id = pv.id AND c.status IN (0,1)
                    ), 0)
                ) AS physical_stock

            FROM product_variants pv
            JOIN products p ON pv.product_id = p.id
            LEFT JOIN stock_control sc ON pv.id = sc.pv_id

            WHERE p.deleted_at IS NULL AND pv.deleted_at IS NULL

            GROUP BY pv.id
            ORDER BY p.id, pv.id
        ";

        $result = $conn->query($sql);


        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $var_id = $_POST["product_id"];
            $type = $_POST["type"];
            $qty = $_POST["qty"];

            $sql = "SELECT * FROM `product_variants` WHERE id='$var_id'";
            $dataVar = $conn->query($sql);
            $dRow = $dataVar->fetch_array();

            $pid = $dRow["product_id"];
            $vid = $var_id;

            $updater = $_SESSION['user']->id." : ".$_SESSION['user']->f_name;

            if($type == "1"){
                $add = "INSERT INTO `stock_control`(`id`, `p_id`, `pv_id`, `stock_in`, `stock_out`, `created_at`, `updated_at`, `deleted_at`, `comment`) VALUES (NULL,'$pid','$vid','$qty','0','$dateNow','$dateNow',NULL,'Updated stock (ADDED) by ($updater)')";

                $applied = $conn->query($add);

                $_SESSION['upload_success'] = 'Successful updated (ADD) stock';
                    
            }else{
                $deduct = "INSERT INTO `stock_control`(`id`, `p_id`, `pv_id`, `stock_in`, `stock_out`, `created_at`, `updated_at`, `deleted_at`, `comment`) VALUES (NULL,'$pid','$vid','0','$qty','$dateNow','$dateNow',NULL,'Updated stock (DEDUCTED) by ($updater)')";

                $applied = $conn->query($deduct);

                $_SESSION['upload_success'] = 'Successful updated (DEDUCT) stock';
            }
            header("Location: ".$domainURL."stock-control");
            exit;
        }

        

        require_once __DIR__ . '/../../view/Admin/stock-control.php';
    }

    public function updateProduct($id){
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Update Product";

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: ".$domainURL."access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $options = getSelectOptions();
        $country = allSaleCountry();

        $product = GetProductDetails($id);

        $selectedBrandId = $product['brand_id'] ?? null;       // or from DB
        $selectedCategoryId = $product['category_id'] ?? null; // or from DB

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

            if ($type === 'simple') {
                $check = $conn->query("SELECT * FROM product_variants WHERE `id` != '$variant_id' AND sku = '$sku' AND deleted_at IS NULL");
                if ($check->num_rows > 0) {
                    $_SESSION['upload_error'] = 'SKU must be UNIQUE. Please use another.';
                    header("Location: ".$domainURL."update-product/".$id);
                    exit;
                }
            } elseif ($type === 'variable' && !empty($_POST['variants'])) {
                foreach ($_POST['variants'] as $variant) {
                    $vSku = $conn->real_escape_string($variant['sku'] ?? '');
                    $vId = isset($variant['id']) ? intval($variant['id']) : 0;
                    $checkSku = $conn->query("SELECT id FROM product_variants WHERE sku = '$vSku' AND id != '$vId' AND deleted_at IS NULL");
                    if ($checkSku->num_rows > 0) {
                        $_SESSION['upload_error'] = 'SKU must be UNIQUE. Please use another.';
                        header("Location: ".$domainURL."update-product/".$id);
                        exit;
                    }
                }
            }

            $uploadDir = 'assets/images/products/'.$currentYear.'/';
            $uploadedFiles = [];
            $errors = [];
            $maxSize = 10 * 1024 * 1024; // 10MB in bytes

            // Create upload directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['upload_error'] = 'Failed to create upload directory.';
                    header("Location: ".$domainURL."update-product/".$id);
                    exit;
                }
            }

            // Check if files are uploaded
            if (!empty($_FILES['files']['name'][0])) {
                foreach ($_FILES['files']['name'] as $key => $name) {
                    $tmpName = $_FILES['files']['tmp_name'][$key];
                    $error = $_FILES['files']['error'][$key];
                    $size = $_FILES['files']['size'][$key];
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    // Error during upload
                    if ($error !== UPLOAD_ERR_OK) {
                        $errors[] = "Error uploading: " . htmlspecialchars($name);
                        continue;
                    }

                    // Size check
                    if ($size > $maxSize) {
                        $errors[] = "File too large (max 10MB): " . htmlspecialchars($name);
                        continue;
                    }

                    // Type check
                    if (!in_array($ext, $allowed)) {
                        $errors[] = "Invalid file type: " . htmlspecialchars($name);
                        continue;
                    }

                    // Move file
                    $uniqueName = time()."_".uniqid('img_', true) . '.' . $ext;
                    $destination = $uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $uploadedFiles[] = $uniqueName;
                    } else {
                        $errors[] = "Failed to save: " . htmlspecialchars($name);
                    }

                    // if (move_uploaded_file($tmpName, $destination)) {
                    //     $uploadedFiles[] = $uniqueName;
                    
                    //     // Save immediately
                    //     $dirFile = $currentYear . "/" . $uniqueName;
                    //     $stmts = $conn->query("INSERT INTO `product_image`(`id`, `product_id`, `image`, `created_at`) VALUES (NULL,'$id','$dirFile','$dateNow')");
                    // } else {
                    //     $errors[] = "Failed to save: " . htmlspecialchars($name);
                    // }
                }

                // Final decision: success or rollback
                if (empty($errors)) {

                    

                    $productId = $id;


                    $updateProduct = $conn->query("UPDATE `products` SET `name`='$pname', `slug`='$slug', `description`='$description', `type`='$type', `category_id`='$category_id', `brand_id`='$brand_id', `price_capital`='$capPrice', `status`='$status', `weight`='$weight', `length`='$length', `width`='$width', `height`='$height', `updated_at`='$dateNow' WHERE id='$productId'");

                    if ($type === 'variable' && !empty($_POST['variants'])) {
                        $submittedIds = [];
                        foreach ($_POST['variants'] as $variant) {
                            $vName = $conn->real_escape_string($variant['name'] ?? '');
                            $vSku = $conn->real_escape_string($variant['sku'] ?? '');
                            $vMaxP = intval($variant['maxP'] ?? 1);
                            if (!empty($variant['id'])) {
                                $vId = intval($variant['id']);
                                $submittedIds[] = $vId;
                                $conn->query("UPDATE `product_variants` SET `variant_name`='$vName', `sku`='$vSku', `max_purchase`='$vMaxP', `updated_at`='$dateNow' WHERE id='$vId' AND product_id='$productId'");
                            } else {
                                $conn->query("INSERT INTO `product_variants`(`id`, `product_id`, `variant_name`, `sku`, `price_retail`, `price_sale`, `stock`, `image`, `max_purchase`, `status`, `created_at`, `updated_at`) VALUES (NULL,'$productId','$vName','$vSku','0.00','0.00','0','-','$vMaxP','1','$dateNow','$dateNow')");
                                $submittedIds[] = $conn->insert_id;
                            }
                        }
                        if (!empty($submittedIds)) {
                            $idList = implode(',', $submittedIds);
                            $conn->query("UPDATE `product_variants` SET `status`=2, `deleted_at`='$dateNow' WHERE product_id='$productId' AND id NOT IN ($idList) AND deleted_at IS NULL");
                        }
                    } else {
                        $conn->query("UPDATE `product_variants` SET `sku`='$sku', `max_purchase`='$maxP', `status`='$status', `updated_at`='$dateNow' WHERE product_id='$productId' AND deleted_at IS NULL");
                    }

                    $allImagesQuery = mysqli_query($conn, "SELECT image FROM product_image WHERE product_id = '$productId'");
                    while ($row = mysqli_fetch_assoc($allImagesQuery)) {
                        $imagePath = $row['image'];
                        if (!in_array($imagePath, $existingImages)) {
                            mysqli_query($conn, "DELETE FROM product_image WHERE product_id = '$productId' AND image = '$imagePath'");
                        }
                    }


                    foreach ($uploadedFiles as $file) {
                        $dirFile = $currentYear."/".$file;
                        $stmts = $conn->query("INSERT INTO `product_image` (`id`, `product_id`, `image`, `created_at`) VALUES (NULL, '$productId', '$dirFile', current_timestamp());");
                    }

                    foreach ($_POST['mp'] as $countryId => $marketPrice) {
                        $salePrice = $_POST['sp'][$countryId];

                        $dataCountry = getCountry($countryId);

                        if ($dataCountry && $dataCountry->num_rows > 0) {
                            $row = $dataCountry->fetch_assoc();
                        } else {
                            echo "Country not found.";
                        }
                        $marketPrice = number_format($marketPrice, 2, '.', '');
                        $salePrice = number_format($salePrice, 2, '.', '');

                        $stmtss = $conn->query("UPDATE `list_country_product_price` SET `market_price`='$marketPrice', `sale_price`='$salePrice', `updated_at`='$dateNow' WHERE `country_id`='$countryId' AND `product_id`='$productId'");
                    }

                    $_SESSION['upload_success'] = "Product and images uploaded successfully.";
                    header("Location: ".$domainURL."update-product/".$id);
                    exit;
                } else {
                    // Delete any uploaded files on failure
                    foreach ($uploadedFiles as $file) {
                        @unlink($uploadDir . $file);
                    }
                    $_SESSION['upload_error'] = implode('<br>', $errors);
                    // Redirect back to form
                    header("Location: ".$domainURL."update-product/".$id);
                    exit;
                }
            } else {
                $productId = $id;

                    $updateProduct = $conn->query("UPDATE `products` SET `name`='$pname', `slug`='$slug', `description`='$description', `type`='$type', `category_id`='$category_id', `brand_id`='$brand_id', `price_capital`='$capPrice', `status`='$status', `weight`='$weight', `length`='$length', `width`='$width', `height`='$height', `updated_at`='$dateNow' WHERE id='$productId'");

                    if ($type === 'variable' && !empty($_POST['variants'])) {
                        $submittedIds = [];
                        foreach ($_POST['variants'] as $variant) {
                            $vName = $conn->real_escape_string($variant['name'] ?? '');
                            $vSku = $conn->real_escape_string($variant['sku'] ?? '');
                            $vMaxP = intval($variant['maxP'] ?? 1);
                            if (!empty($variant['id'])) {
                                $vId = intval($variant['id']);
                                $submittedIds[] = $vId;
                                $conn->query("UPDATE `product_variants` SET `variant_name`='$vName', `sku`='$vSku', `max_purchase`='$vMaxP', `updated_at`='$dateNow' WHERE id='$vId' AND product_id='$productId'");
                            } else {
                                $conn->query("INSERT INTO `product_variants`(`id`, `product_id`, `variant_name`, `sku`, `price_retail`, `price_sale`, `stock`, `image`, `max_purchase`, `status`, `created_at`, `updated_at`) VALUES (NULL,'$productId','$vName','$vSku','0.00','0.00','0','-','$vMaxP','1','$dateNow','$dateNow')");
                                $submittedIds[] = $conn->insert_id;
                            }
                        }
                        if (!empty($submittedIds)) {
                            $idList = implode(',', $submittedIds);
                            $conn->query("UPDATE `product_variants` SET `status`=2, `deleted_at`='$dateNow' WHERE product_id='$productId' AND id NOT IN ($idList) AND deleted_at IS NULL");
                        }
                    } else {
                        $conn->query("UPDATE `product_variants` SET `sku`='$sku', `max_purchase`='$maxP', `status`='$status', `updated_at`='$dateNow' WHERE product_id='$productId' AND deleted_at IS NULL");
                    }

                    foreach ($_POST['mp'] as $countryId => $marketPrice) {
                        $salePrice = $_POST['sp'][$countryId];

                        $dataCountry = getCountry($countryId);

                        if ($dataCountry && $dataCountry->num_rows > 0) {
                            $row = $dataCountry->fetch_assoc();
                        } else {
                            echo "Country not found.";
                        }
                        $marketPrice = number_format($marketPrice, 2, '.', '');
                        $salePrice = number_format($salePrice, 2, '.', '');

                        $stmtss = $conn->query("UPDATE `list_country_product_price` SET `market_price`='$marketPrice', `sale_price`='$salePrice', `updated_at`='$dateNow' WHERE `country_id`='$countryId' AND `product_id`='$productId'");
                    }

                    $allImagesQuery = mysqli_query($conn, "SELECT image FROM product_image WHERE product_id = '$productId'");
                    while ($row = mysqli_fetch_assoc($allImagesQuery)) {
                        $imagePath = $row['image'];
                        if (!in_array($imagePath, $existingImages)) {
                            mysqli_query($conn, "DELETE FROM product_image WHERE product_id = '$productId' AND image = '$imagePath'");
                        }
                    }

                    $_SESSION['upload_success'] = "Product and images uploaded successfully.";
                    header("Location: ".$domainURL."update-product/".$id);
                    exit;
            }
        
            
        }

        require_once __DIR__ . '/../../view/Admin/update-product.php';
    }

    public function deleteProduct($id)
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: ".$domainURL."access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $newdate = new \DateTime($dateNow);

        // Format to: Day MonthName, Year Hour:Minute AM/PM
        $formattedDate = $newdate->format('j F, Y h:i A');

        $product = GetProductDetails($id);

        $sdProduct = $conn->query("UPDATE `products` SET `status`='2', `deleted_at`='$dateNow' WHERE `id`='$id'");

        $sdProductVariant = $conn->query("UPDATE `product_variants` SET `status`='2', `deleted_at`='$dateNow' WHERE `product_id`='$id'");

        $_SESSION['upload_success'] = "Successfull deleted <b>'".$product["name"]."'</b> on ".$formattedDate;

        header("Location: ".$domainURL."stock-control");
        exit;

    }

    public function productCategory()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Category - Add & Update";
        $nameBtn = "Category";

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: ".$domainURL."access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $sql = "SELECT `id`, `name`, `slug`, `image`, `description`, `parent_id`, `sort_order`, `created_at`, `updated_at`, `deleted_at` 
        FROM `categories` 
        WHERE `deleted_at` IS NULL";

        $result = $conn->query($sql);

        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $pname = $_POST['name'] ?? '';
            

            $uploadDir = 'assets/images/brand-category/'.$currentYear.'/';
            $uploadedFiles = [];
            $errors = [];
            $maxSize = 10 * 1024 * 1024; // 10MB in bytes

            // Create upload directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['upload_error'] = 'Failed to create upload directory.';
                    header("Location: ".$domainURL."category-product");
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

                    // Error during upload
                    if ($error !== UPLOAD_ERR_OK) {
                        $errors[] = "Error uploading: " . htmlspecialchars($name);
                        continue;
                    }

                    // Size check
                    if ($size > $maxSize) {
                        $errors[] = "File too large (max 10MB): " . htmlspecialchars($name);
                        continue;
                    }

                    // Type check
                    if (!in_array($ext, $allowed)) {
                        $errors[] = "Invalid file type: " . htmlspecialchars($name);
                        continue;
                    }

                    // Move file
                    $uniqueName = time()."_".uniqid('img_', true) . '.' . $ext;
                    $destination = $uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $uploadedFiles[] = $uniqueName;
                    } else {
                        $errors[] = "Failed to save: " . htmlspecialchars($name);
                    }

                    // if (move_uploaded_file($tmpName, $destination)) {
                    //     $uploadedFiles[] = $uniqueName;
                    
                    //     // Save immediately
                    //     $dirFile = $currentYear . "/" . $uniqueName;
                    //     $stmts = $conn->query("INSERT INTO `product_image`(`id`, `product_id`, `image`, `created_at`) VALUES (NULL,'$id','$dirFile','$dateNow')");
                    // } else {
                    //     $errors[] = "Failed to save: " . htmlspecialchars($name);
                    // }
                }

                // Final decision: success or rollback
                if (empty($errors)) {

                    foreach ($uploadedFiles as $file) {
                        $string = ltrim($pname);                 // Remove beginning space only
                        $string1 = preg_replace('/\s+/', '_', $string); // Replace all whitespace with _
                        $string2 = strtolower($string1);
                        $theImage = $currentYear."/".$file;
                        $updateCategory = $conn->query("INSERT INTO `categories`(`id`, `name`, `slug`, `image`, `description`, `parent_id`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL,'$pname','$string','$theImage','-',NULL,'0','$dateNow','$dateNow',NULL);");
                    }

                    $_SESSION['upload_success'] = "Category and images uploaded successfully."; 
                    header("Location: ".$domainURL."category-product");                   
                    exit;
                } else {
                    
                    $_SESSION['upload_error'] = implode('<br>', $errors);
                    // Redirect back to form
                    header("Location: ".$domainURL."category-product");
                    exit;
                }
            }
        }

        require_once __DIR__ . '/../../view/Admin/category-product.php';
    }

    public function productBrand()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Brand - Add & Update";
        $nameBtn = "Brand";

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: ".$domainURL."access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $sql = "SELECT `id`, `name`, `slug`, `image`, `description`, `created_at`, `updated_at`, `deleted_at` 
        FROM `brands` 
        WHERE `deleted_at` IS NULL";

        $result = $conn->query($sql);

        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $pname = $_POST['name'] ?? '';
            

            $uploadDir = 'assets/images/brand-category/'.$currentYear.'/';
            $uploadedFiles = [];
            $errors = [];
            $maxSize = 10 * 1024 * 1024; // 10MB in bytes

            // Create upload directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['upload_error'] = 'Failed to create upload directory.';
                    header("Location: ".$domainURL."brand-product");
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

                    // Error during upload
                    if ($error !== UPLOAD_ERR_OK) {
                        $errors[] = "Error uploading: " . htmlspecialchars($name);
                        continue;
                    }

                    // Size check
                    if ($size > $maxSize) {
                        $errors[] = "File too large (max 10MB): " . htmlspecialchars($name);
                        continue;
                    }

                    // Type check
                    if (!in_array($ext, $allowed)) {
                        $errors[] = "Invalid file type: " . htmlspecialchars($name);
                        continue;
                    }

                    // Move file
                    $uniqueName = time()."_".uniqid('img_', true) . '.' . $ext;
                    $destination = $uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $uploadedFiles[] = $uniqueName;
                    } else {
                        $errors[] = "Failed to save: " . htmlspecialchars($name);
                    }

                    // if (move_uploaded_file($tmpName, $destination)) {
                    //     $uploadedFiles[] = $uniqueName;
                    
                    //     // Save immediately
                    //     $dirFile = $currentYear . "/" . $uniqueName;
                    //     $stmts = $conn->query("INSERT INTO `product_image`(`id`, `product_id`, `image`, `created_at`) VALUES (NULL,'$id','$dirFile','$dateNow')");
                    // } else {
                    //     $errors[] = "Failed to save: " . htmlspecialchars($name);
                    // }
                }

                // Final decision: success or rollback
                if (empty($errors)) {

                    foreach ($uploadedFiles as $file) {
                        $string = ltrim($pname);                 // Remove beginning space only
                        $string1 = preg_replace('/\s+/', '_', $string); // Replace all whitespace with _
                        $string2 = strtolower($string1);
                        $theImage = $currentYear."/".$file;
                        $updateCategory = $conn->query("INSERT INTO `brands`(`id`, `name`, `slug`, `image`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL,'$pname','$string2','$theImage','-','$dateNow','$dateNow',NULL);");
                    }

                    $_SESSION['upload_success'] = "Brand and images uploaded successfully."; 
                    header("Location: ".$domainURL."brand-product");                   
                    exit;
                } else {
                    
                    $_SESSION['upload_error'] = implode('<br>', $errors);
                    // Redirect back to form
                    header("Location: ".$domainURL."brand-product");
                    exit;
                }
            }
        }

        require_once __DIR__ . '/../../view/Admin/brand-product.php';
    }

    public function updateCategory($id)
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Category - Update";
        $data = getCategoryBrand($id, 1);

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: ".$domainURL."access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $pname = $_POST['name'] ?? '';
            

            $uploadDir = 'assets/images/brand-category/'.$currentYear.'/';
            $uploadedFiles = [];
            $errors = [];
            $maxSize = 10 * 1024 * 1024; // 10MB in bytes

            // Create upload directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['upload_error'] = 'Failed to create upload directory.';
                    header("Location: ".$domainURL."update-category/".$id);
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

                    // Error during upload
                    if ($error !== UPLOAD_ERR_OK) {
                        $errors[] = "Error uploading: " . htmlspecialchars($name);
                        continue;
                    }

                    // Size check
                    if ($size > $maxSize) {
                        $errors[] = "File too large (max 10MB): " . htmlspecialchars($name);
                        continue;
                    }

                    // Type check
                    if (!in_array($ext, $allowed)) {
                        $errors[] = "Invalid file type: " . htmlspecialchars($name);
                        continue;
                    }

                    // Move file
                    $uniqueName = time()."_".uniqid('img_', true) . '.' . $ext;
                    $destination = $uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $uploadedFiles[] = $uniqueName;
                    } else {
                        $errors[] = "Failed to save: " . htmlspecialchars($name);
                    }

                    // if (move_uploaded_file($tmpName, $destination)) {
                    //     $uploadedFiles[] = $uniqueName;
                    
                    //     // Save immediately
                    //     $dirFile = $currentYear . "/" . $uniqueName;
                    //     $stmts = $conn->query("INSERT INTO `product_image`(`id`, `product_id`, `image`, `created_at`) VALUES (NULL,'$id','$dirFile','$dateNow')");
                    // } else {
                    //     $errors[] = "Failed to save: " . htmlspecialchars($name);
                    // }
                }

                // Final decision: success or rollback
                if (empty($errors)) {

                    foreach ($uploadedFiles as $file) {
                        $theImage = $currentYear."/".$file;
                        $updateCategory = $conn->query("UPDATE `categories` SET `name`='$pname', `image`='$theImage', `updated_at`='$dateNow' WHERE `id`='$id';");
                    }

                    $_SESSION['upload_success'] = "Category and images uploaded successfully."; 
                    header("Location: ".$domainURL."update-category/".$id);                   
                    exit;
                } else {
                    
                    $_SESSION['upload_error'] = implode('<br>', $errors);
                    // Redirect back to form
                    header("Location: ".$domainURL."update-category/".$id);
                    exit;
                }
            }else{
                    $updateCategory = $conn->query("UPDATE `categories` SET `name`='$pname', `updated_at`='$dateNow' WHERE `id`='$id';");
                

                $_SESSION['upload_success'] = "Category successfully updated."; 
                header("Location: ".$domainURL."update-category/".$id);                   
                exit;
            }
        }else{
            require_once __DIR__ . '/../../view/Admin/update-category.php';
        }
        
    }

    public function updateBrand($id)
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Brand - Update";
        $data = getCategoryBrand($id, 2);

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: ".$domainURL."access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $pname = $_POST['name'] ?? '';
            

            $uploadDir = 'assets/images/brand-category/'.$currentYear.'/';
            $uploadedFiles = [];
            $errors = [];
            $maxSize = 10 * 1024 * 1024; // 10MB in bytes

            // Create upload directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['upload_error'] = 'Failed to create upload brand.';
                    header("Location: ".$domainURL."update-brand/".$id);
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

                    // Error during upload
                    if ($error !== UPLOAD_ERR_OK) {
                        $errors[] = "Error uploading: " . htmlspecialchars($name);
                        continue;
                    }

                    // Size check
                    if ($size > $maxSize) {
                        $errors[] = "File too large (max 10MB): " . htmlspecialchars($name);
                        continue;
                    }

                    // Type check
                    if (!in_array($ext, $allowed)) {
                        $errors[] = "Invalid file type: " . htmlspecialchars($name);
                        continue;
                    }

                    // Move file
                    $uniqueName = time()."_".uniqid('img_', true) . '.' . $ext;
                    $destination = $uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $uploadedFiles[] = $uniqueName;
                    } else {
                        $errors[] = "Failed to save: " . htmlspecialchars($name);
                    }

                    // if (move_uploaded_file($tmpName, $destination)) {
                    //     $uploadedFiles[] = $uniqueName;
                    
                    //     // Save immediately
                    //     $dirFile = $currentYear . "/" . $uniqueName;
                    //     $stmts = $conn->query("INSERT INTO `product_image`(`id`, `product_id`, `image`, `created_at`) VALUES (NULL,'$id','$dirFile','$dateNow')");
                    // } else {
                    //     $errors[] = "Failed to save: " . htmlspecialchars($name);
                    // }
                }

                // Final decision: success or rollback
                if (empty($errors)) {

                    foreach ($uploadedFiles as $file) {
                        $theImage = $currentYear."/".$file;
                        $updateCategory = $conn->query("UPDATE `brands` SET `name`='$pname', `image`='$theImage', `updated_at`='$dateNow' WHERE `id`='$id';");
                    }

                    $_SESSION['upload_success'] = "Brand and images uploaded successfully."; 
                    header("Location: ".$domainURL."update-brand/".$id);                   
                    exit;
                } else {
                    
                    $_SESSION['upload_error'] = implode('<br>', $errors);
                    // Redirect back to form
                    header("Location: ".$domainURL."update-brand/".$id);
                    exit;
                }
            }else{
                    $updateCategory = $conn->query("UPDATE `brands` SET `name`='$pname', `updated_at`='$dateNow' WHERE `id`='$id';");
                

                $_SESSION['upload_success'] = "Brand successfully updated."; 
                header("Location: ".$domainURL."update-brand/".$id);                   
                exit;
            }
        }else{
            require_once __DIR__ . '/../../view/Admin/update-brand.php';
        }
        
    }

    public function deleteBrand($id)
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Brand - Delete";

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: ".$domainURL."access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }


        $data = getCategoryBrand($id, 2);

        if (is_null($data['deleted_at'])) {
            $sql = "UPDATE `brands` SET `deleted_at`='$dateNow' WHERE `id`='$id'";
            $delete = $conn->query($sql);
            $_SESSION['upload_success'] = "Brand '".$data['name']."' successfully deleted."; 
            header("Location: ".$domainURL."brand-product");                   
            exit;
        } else {
            echo "Deleted on " . $data['deleted_at'];
        }
    }

    public function deleteCategory($id)
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Category - Delete";

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: ".$domainURL."access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }


        $data = getCategoryBrand($id, 1);

        if (is_null($data['deleted_at'])) {
            $sql = "UPDATE `categories` SET `deleted_at`='$dateNow' WHERE `id`='$id'";
            $delete = $conn->query($sql);
            $_SESSION['upload_success'] = "Category '".$data['name']."' successfully deleted."; 
            header("Location: ".$domainURL."category-product");                   
            exit;
        } else {
            echo "Deleted on " . $data['deleted_at'];
        }
    }
}