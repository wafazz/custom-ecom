<?php

namespace Setting;

require_once __DIR__ . '/../../config/mainConfig.php';

class SettingController
{
    private $domainURL;
    private $mainDomain;
    private $conn;
    private $options;
    private $country;
    private $currentYear;
    private $dateNow;

    public function __construct()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $this->domainURL  = getMainUrl();
        $this->mainDomain = mainDomain();
        $this->conn       = getDbConnection();
        $this->options    = getSelectOptions();
        $this->country    = allSaleCountry();
        $this->currentYear = currentYear();
        $this->dateNow     = dateNow();
    }

    public function policy()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Policy - Setting";

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $sql = "SELECT * FROM `policy`";
        $query = $this->conn->query($sql);
        $row = $query->fetch_array();

        require_once __DIR__ . '/../../view/Admin/policy.php';
    }

    public function terms()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $pageName = "Terms & Conditions - Setting";
        $sql = "SELECT * FROM `terms_conditions`";
        $query = $this->conn->query($sql);
        $row = $query->fetch_array();

        require_once __DIR__ . '/../../view/Admin/terms.php';
    }

    public function aboutUs()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $pageName = "About Us - Setting";
        $sql = "SELECT * FROM `about_us`";
        $query = $this->conn->query($sql);
        $row = $query->fetch_array();

        require_once __DIR__ . '/../../view/Admin/about.php';
    }

    public function updatePolicy()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $description = $this->conn->real_escape_string($_POST['description'] ?? '');
        $this->conn->query("UPDATE `policy` SET `description`='$description', updated_at='{$this->dateNow}' WHERE id='1'");

        $_SESSION['upload_success'] = 'Successful updated <b>Policy</b>.';
        header("Location: {$this->domainURL}setting-policy");
    }

    public function updateTerms()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $description = $this->conn->real_escape_string($_POST['description'] ?? '');
        $this->conn->query("UPDATE `terms_conditions` SET `description`='$description', updated_at='{$this->dateNow}' WHERE id='1'");

        $_SESSION['upload_success'] = 'Successful updated <b>Terms & Conditions</b>.';
        header("Location: {$this->domainURL}setting-terms");
    }

    public function updateAboutUs()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $description = $this->conn->real_escape_string($_POST['description'] ?? '');
        $this->conn->query("UPDATE `about_us` SET `description`='$description', updated_at='{$this->dateNow}' WHERE id='1'");

        $_SESSION['upload_success'] = 'Successful updated <b>About Us</b>.';
        header("Location: {$this->domainURL}setting-about-us");
    }

    public function deliveryCharge()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $pageName = "Shipping Cost";

        $sql     = "SELECT * FROM `postage_cost` ORDER BY id ASC";
        $result  = $this->conn->query($sql);

        $sqls    = "SELECT * FROM `list_country` ORDER BY id ASC";
        $results = $this->conn->query($sqls);

        require_once __DIR__ . '/../../view/Admin/shipping.php';
    }

    public function saveDeliveryCharge()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $countryID = $_POST["country"] ?? "";
        $zone      = $_POST["szone"] ?? "1";
        $fkilo     = $_POST["fkilo"] ?? "";
        $nkilo     = $_POST["nkilo"] ?? "";

        $dataCountry = getCountryP($countryID);
        $curSign     = $dataCountry["sign"];

        $validate = $this->conn->query("SELECT * FROM postage_cost WHERE country_id='$countryID' AND shipping_zone='$zone'");

        if ($validate->num_rows > 0) {
            $this->conn->query("UPDATE postage_cost SET currency='$curSign', first_kilo='$fkilo', next_kilo='$nkilo', updated_at='{$this->dateNow}' WHERE country_id='$countryID' AND shipping_zone='$zone'");
            $_SESSION['upload_success'] = 'Successfully updated shipping cost for ' . $dataCountry["name"];
        } else {
            $this->conn->query("INSERT INTO `postage_cost`(`id`, `country_id`, `shipping_zone`, `currency`, `first_kilo`, `next_kilo`, `created_at`, `updated_at`) 
                VALUES (NULL,'$countryID','$zone','$curSign','$fkilo','$nkilo','{$this->dateNow}','{$this->dateNow}')");
            $_SESSION['upload_success'] = 'Successfully added new shipping cost for ' . $dataCountry["name"];
        }

        header("Location: {$this->domainURL}delivery-charge");
    }

    public function saveCodCharge()
    {
        $conn = getDbConnection();
        $dateNow = dateNow();

        $countryId = $_POST["country_id"] ?? "";
        $shippingZone = $_POST["shipping_zone"] ?? "1";
        $benchmarkAmount = $_POST["benchmark_amount"] ?? "0";
        $codFeeBelow = $_POST["cod_fee_below"] ?? "0";
        $codFeeAbove = $_POST["cod_fee_above"] ?? "0";

        if (empty($countryId)) {
            $_SESSION['upload_success'] = 'Please fill in all required fields.';
            header("Location: " . getMainUrl() . "delivery-charge");
            return;
        }

        $existing = $conn->query("SELECT * FROM `cod_charges` WHERE `country_id`='$countryId' AND `shipping_zone`='$shippingZone'");
        if ($existing->num_rows > 0) {
            $conn->query("UPDATE `cod_charges` SET `benchmark_amount`='$benchmarkAmount', `cod_fee_below`='$codFeeBelow', `cod_fee_above`='$codFeeAbove', `updated_at`='$dateNow' WHERE `country_id`='$countryId' AND `shipping_zone`='$shippingZone'");
            $_SESSION['upload_success'] = 'COD charge updated successfully.';
        } else {
            $conn->query("INSERT INTO `cod_charges` (`country_id`, `shipping_zone`, `benchmark_amount`, `cod_fee_below`, `cod_fee_above`, `created_at`, `updated_at`) VALUES ('$countryId', '$shippingZone', '$benchmarkAmount', '$codFeeBelow', '$codFeeAbove', '$dateNow', '$dateNow')");
            $_SESSION['upload_success'] = 'COD charge added successfully.';
        }

        header("Location: " . getMainUrl() . "delivery-charge");
    }

    public function deleteCodCharge($id)
    {
        $conn = getDbConnection();
        $conn->query("DELETE FROM `cod_charges` WHERE `id`='$id'");

        $_SESSION['upload_success'] = 'COD charge deleted.';
        header("Location: " . getMainUrl() . "delivery-charge");
    }

    public function indexAnnouncement()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $pageName = "Announcement & Blog";

        $listNews = $conn->query("SELECT * FROM news_blog WHERE deleted_at IS NULL ORDER BY id DESC");

        require_once __DIR__ . '/../../view/Admin/announcement.php';
    }

    public function saveAnnouncement()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();


        $title = isset($_POST['title']) ? $conn->real_escape_string($_POST['title']) : '';
        $description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';

        $addNews = $conn->query("INSERT INTO `news_blog`(`id`, `post_by`, `update_by`, `title`, `contents`, `created_at`, `updated_at`, `deleted_at`, `reader`) VALUES (NULL,'" . $_SESSION['user']->id . "','','$title','$description','$dateNow','$dateNow',NULL,'')");

        if ($addNews) {
            $_SESSION['upload_success'] = "Successful add new announcement";
        } else {
            $_SESSION['upload_error'] = "Sorry! Failed to add new announcement";
        }

        header("Location: {$this->domainURL}announcement-blog");
        exit;
    }

    public function updateAnnouncement($id)
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();

        $pageName = "Announcement & Blog (Update)";

        $getNews = $conn->query("SELECT * FROM news_blog WHERE id='$id' AND deleted_at IS NULL");

        if ($getNews->num_rows != "1") {
            $_SESSION['upload_error'] = "Sorry! Invalid data/parameter to update Announcement/Blog.";
            header("Location: {$this->domainURL}announcement-blog");
            exit;
        }

        $row = $getNews->fetch_array();

        require_once __DIR__ . '/../../view/Admin/update-announcement.php';
    }

    public function saveUpdateAnnouncement($id)
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();


        $title = isset($_POST['title']) ? $conn->real_escape_string($_POST['title']) : '';
        $description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';

        $getNews = $conn->query("SELECT * FROM news_blog WHERE id='$id' AND deleted_at IS NULL");
        $row = $getNews->fetch_array();

        if (empty($row["update_by"])) {
            $updateBy = "[" . $_SESSION['user']->id . "]|" . $dateNow;
        } else {
            $updateBy = $row["update_by"] . ",[" . $_SESSION['user']->id . "]|" . $dateNow;
        }

        $addNews = $conn->query("UPDATE `news_blog` SET `update_by`='$updateBy', `title`='$title' , `contents`='$description', `updated_at`='$dateNow' WHERE id='$id'");

        if ($addNews) {
            $_SESSION['upload_success'] = "Successful update announcement";
        } else {
            $_SESSION['upload_error'] = "Sorry! Failed to update announcement";
        }

        header("Location: {$this->domainURL}update-post/" . $id);
        exit;
    }

    public function deleteAnnouncement($id)
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();


        $title = isset($_POST['title']) ? $conn->real_escape_string($_POST['title']) : '';
        $description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';

        $getNews = $conn->query("SELECT * FROM news_blog WHERE id='$id' AND deleted_at IS NULL");
        $row = $getNews->fetch_array();

        if (empty($row["update_by"])) {
            $updateBy = "[" . $_SESSION['user']->id . "]|" . $dateNow;
        } else {
            $updateBy = $row["update_by"] . ",[" . $_SESSION['user']->id . "]|" . $dateNow;
        }

        $addNews = $conn->query("UPDATE `news_blog` SET `update_by`='$updateBy', `updated_at`='$dateNow', `deleted_at`='$dateNow' WHERE id='$id'");

        if ($addNews) {
            $_SESSION['upload_success'] = "Successful update Delete Annpuncement";
        } else {
            $_SESSION['upload_error'] = "Sorry! Failed to delete announcement";
        }

        header("Location: {$this->domainURL}announcement-blog");
        exit;
    }

    public function settingJNT()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "J&T Setting";

        $jt = dataSettingJNT();

        require_once __DIR__ . '/../../view/Admin/jt-express.php';
    }

    public function saveJNT()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();

        $jt = dataSettingJNT();

        if (isset($_POST["saveAPI"])) {
            $production_sandbox = $_POST["production_sandbox"];

            $update = $conn->query("UPDATE jt_setting SET `production_sandbox`='$production_sandbox' WHERE id='1'");

            if ($update and $production_sandbox == 1) {
                $_SESSION['upload_success'] = "Successful activate J&T Express to Production Mode";
            } else if ($update and $production_sandbox == 0) {
                $_SESSION['upload_success'] = "Successful deactivate J&T Express and set to Production Mode";
            } else {
                $_SESSION['upload_error'] = "Sorry! Invalid data/parameter to update J&T status.";
            }

            header("Location: {$domainURL}jt-express");
        }

        if (isset($_POST["saveSandbox"])) {
            $username = $_POST["username"];
            $password = $_POST["password"];
            $cuscode = $_POST["cuscode"];
            $key = $_POST["key"];

            $update = $conn->query("UPDATE jt_setting SET `username_sanbox`='$username', `password_sandbox`='$password', `cuscode_sandbox`='$cuscode', `key_sandbox`='$key' WHERE id='1'");

            if ($update) {
                $_SESSION['upload_success'] = "Successful update data in Sandbox Mode";
            } else {
                $_SESSION['upload_error'] = "Sorry! Failed to update data in Sandbox Mode.";
            }

            header("Location: {$domainURL}jt-express");
        }

        if (isset($_POST["saveProduction"])) {
            $username = $_POST["username"];
            $password = $_POST["password"];
            $cuscode = $_POST["cuscode"];
            $key = $_POST["key"];

            $update = $conn->query("UPDATE jt_setting SET `username_production`='$username', `password_production`='$password', `cuscode_production`='$cuscode', `key_production`='$key' WHERE id='1'");

            if ($update) {
                $_SESSION['upload_success'] = "Successful update data in Production Mode";
            } else {
                $_SESSION['upload_error'] = "Sorry! Failed to update data in Production Mode.";
            }

            header("Location: {$domainURL}jt-express");
        }
    }

    public function changePassword()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();


        $pageName = "Password";


        if (isset($_POST["changePass"])) {
            $userID = $_SESSION['user']->id;
            $cpass = $_POST["cpass"];
            $npass = $_POST["npass"];
            $cnpass = $_POST["cnpass"];

            $cpassHash = hash("sha256", $cpass);
            $npassHash = hash("sha256", $npass);
            $cnpassHash = hash("sha256", $cnpass);

            $verify = $conn->query("SELECT * FROM member_hq WHERE id='$userID'");
            $row = $verify->fetch_assoc();

            $errors = "";

            if ($cpassHash != $cpass) {
                $errors .= "Invalid Current Password. ";
            } else {
                $errors .= "";
            }

            if ($npassHash != $cnpassHash) {
                $errors .= "New Password and Confirm Password must be same. ";
            } else {
                $errors .= "";
            }


            if (!empty($errors)) {
                $_SESSION['upload_error'] = "Sorry error updating password! " . $errors;
            } else {
                $_SESSION['upload_success'] = "Successful updating your password. You are advised to logout from system and login using new password.";
            }

            header("Location: {$this->domainURL}password/");
            exit;
        }

        require_once __DIR__ . '/../../view/Admin/password.php';
    }

    public function savePassword()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();

        $userID = $_SESSION['user']->id;
        $cpass = $_POST["cpass"];
        $npass = $_POST["npass"];
        $cnpass = $_POST["cnpass"];

        $cpassHash = hash("sha256", $cpass);
        $npassHash = hash("sha256", $npass);
        $cnpassHash = hash("sha256", $cnpass);

        $verify = $conn->query("SELECT * FROM member_hq WHERE id='$userID'");
        $row = $verify->fetch_assoc();

        $errors = "";

        if ($cpassHash != $row["password"]) {
            $errors .= "Invalid Current Password. ";
        } else {
            $errors .= "";
        }

        if ($npassHash != $cnpassHash) {
            $errors .= "New Password and Confirm Password must be same. ";
        } else {
            $errors .= "";
        }


        if (!empty($errors)) {
            $_SESSION['upload_error'] = "Sorry error updating password! " . $errors;
        } else {
            $updatePassword = $conn->query("UPDATE member_hq SET `password`='$npassHash' WHERE id='$userID'");
            $_SESSION['upload_success'] = "Successful updating your password. You are advised to logout from system and login using new password.";
        }

        header("Location: {$this->domainURL}password");
        exit;
    }

    public function imageSetting()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $pageName = "Setting Image";
        $sql = "SELECT * FROM `image_setting` WHERE `use_type`='logo' AND `deleted_at` IS NULL ORDER BY `created_at` DESC";
        $query = $conn->query($sql);
        // Fetch all rows as an associative array
        $rows = $query->fetch_all(MYSQLI_ASSOC);

        require_once __DIR__ . '/../../view/Admin/image-setting.php';
    }

    public function setLogo()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify('logo-setting', $_SESSION['user']->id) == 0) {
            header("Location: " . $domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        if (isset($_GET["id"]) and !empty($_GET["id"])) {
            $id = $_GET["id"];

            $disableAll = $conn->query("UPDATE image_setting SET sorting='0'");

            if ($disableAll) {
                $activateNew = $conn->query("UPDATE image_setting SET sorting='1' WHERE id='$id'");
                $_SESSION['upload_success'] = "Successful set default logo.";
            } else {
                $_SESSION['upload_error'] = "Failed set default logo. Please try again.";
            }
        } else {
            $_SESSION['upload_error'] = "Failed set default logo. Please try again.";
        }

        header("Location: {$this->domainURL}logo-setting");
        exit;
    }

    public function uploadImages()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();


        $currentYear = currentYear();
        $dateNow = dateNow();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }


        if (isset($_POST['uploadLogo']) && isset($_FILES['file'])) {
            $uploadDir = "assets/images/logo/"; // Folder path

            // Create folder if not exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['file']['tmp_name'] as $key => $tmpName) {
                if (!empty($tmpName)) {
                    $fileName = $currentYear . "_rozeyana_" . basename($_FILES['file']['name'][$key]);
                    $targetPath = $uploadDir . $fileName;

                    // Move uploaded file
                    if (move_uploaded_file($tmpName, $targetPath)) {

                        // If "Set as default" checked
                        if (!empty($_POST['defaultLogo'])) {
                            $disableAll = $conn->query("UPDATE image_setting SET sorting='0'");
                            $addImage = $conn->query("INSERT INTO `image_setting`(`id`, `use_type`, `image_path`, `use_link`, `sorting`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL,'logo','assets/images/logo/$fileName',NULL,'1','$dateNow','$dateNow',NULL)");
                            $_SESSION['upload_success'] = "Successful upload new logo and set as default.";
                        } else {
                            $addImage = $conn->query("INSERT INTO `image_setting`(`id`, `use_type`, `image_path`, `use_link`, `sorting`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL,'logo','assets/images/logo/$fileName',NULL,'0','$dateNow','$dateNow',NULL)");
                            $_SESSION['upload_success'] = "Successful upload new logo.";
                        }
                    } else {

                        $_SESSION['upload_error'] = "Failed to upload new logo. Please try again.";
                    }

                    header("Location: {$this->domainURL}logo-setting");
                    exit;
                }
            }
        }
    }
}
