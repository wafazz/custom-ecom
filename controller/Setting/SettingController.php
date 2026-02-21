<?php

namespace Setting;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/PageContent.php';
require_once __DIR__ . '/../../model/CourierSetting.php';
require_once __DIR__ . '/../../model/PostageCost.php';
require_once __DIR__ . '/../../model/CodCharge.php';
require_once __DIR__ . '/../../model/NewsBlog.php';
require_once __DIR__ . '/../../model/ImageSetting.php';
require_once __DIR__ . '/../../model/MemberHq.php';

class SettingController
{
    private $domainURL;
    private $mainDomain;
    private $conn;
    private $options;
    private $country;
    private $currentYear;
    private $dateNow;

    private $pageContent;
    private $courierSetting;
    private $postageCost;
    private $codCharge;
    private $newsBlog;
    private $imageSetting;
    private $memberHq;

    public function __construct()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $this->domainURL   = getMainUrl();
        $this->mainDomain  = mainDomain();
        $this->conn        = getDbConnection();
        $this->options     = getSelectOptions();
        $this->country     = allSaleCountry();
        $this->currentYear = currentYear();
        $this->dateNow     = dateNow();

        $this->pageContent    = new \PageContent($this->conn);
        $this->courierSetting = new \CourierSetting($this->conn);
        $this->postageCost    = new \PostageCost($this->conn);
        $this->codCharge      = new \CodCharge($this->conn);
        $this->newsBlog       = new \NewsBlog($this->conn);
        $this->imageSetting   = new \ImageSetting($this->conn);
        $this->memberHq       = new \MemberHq($this->conn);
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

    public function policy()
    {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $options     = $this->options;
        $country     = $this->country;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;

        $pageName = "Policy - Setting";
        $row = $this->pageContent->getContent('policy');

        require_once __DIR__ . '/../../view/Admin/policy.php';
    }

    public function terms()
    {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $options     = $this->options;
        $country     = $this->country;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;

        $pageName = "Terms & Conditions - Setting";
        $row = $this->pageContent->getContent('terms_conditions');

        require_once __DIR__ . '/../../view/Admin/terms.php';
    }

    public function aboutUs()
    {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $options     = $this->options;
        $country     = $this->country;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;

        $pageName = "About Us - Setting";
        $row = $this->pageContent->getContent('about_us');

        require_once __DIR__ . '/../../view/Admin/about.php';
    }

    public function updatePolicy()
    {
        $this->checkAccess();

        $description = $_POST['description'] ?? '';
        $this->pageContent->updateContent('policy', $description, $this->dateNow);

        $_SESSION['upload_success'] = 'Successful updated <b>Policy</b>.';
        header("Location: {$this->domainURL}setting-policy");
    }

    public function updateTerms()
    {
        $this->checkAccess();

        $description = $_POST['description'] ?? '';
        $this->pageContent->updateContent('terms_conditions', $description, $this->dateNow);

        $_SESSION['upload_success'] = 'Successful updated <b>Terms & Conditions</b>.';
        header("Location: {$this->domainURL}setting-terms");
    }

    public function updateAboutUs()
    {
        $this->checkAccess();

        $description = $_POST['description'] ?? '';
        $this->pageContent->updateContent('about_us', $description, $this->dateNow);

        $_SESSION['upload_success'] = 'Successful updated <b>About Us</b>.';
        header("Location: {$this->domainURL}setting-about-us");
    }

    public function deliveryCharge()
    {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $options     = $this->options;
        $country     = $this->country;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;

        $pageName = "Shipping Cost";

        $postageCosts = $this->postageCost->getAll();
        $codCharges   = $this->codCharge->findAll([], 'country_id ASC, shipping_zone ASC');

        $stmt = $this->conn->prepare("SELECT * FROM `list_country` ORDER BY id ASC");
        $stmt->execute();
        $res = $stmt->get_result();
        $countries = [];
        while ($r = $res->fetch_assoc()) {
            $countries[] = $r;
        }
        $stmt->close();

        require_once __DIR__ . '/../../view/Admin/shipping.php';
    }

    public function saveDeliveryCharge()
    {
        $this->checkAccess();

        $countryID = $_POST["country"] ?? "";
        $zone      = $_POST["szone"] ?? "1";
        $fkilo     = $_POST["fkilo"] ?? "";
        $nkilo     = $_POST["nkilo"] ?? "";

        $dataCountry = getCountryP($countryID);
        $curSign     = $dataCountry["sign"];

        $existing = $this->postageCost->findByCountryZone($countryID, $zone);

        if ($existing) {
            $this->postageCost->upsert($countryID, $zone, $curSign, $fkilo, $nkilo, $this->dateNow);
            $_SESSION['upload_success'] = 'Successfully updated shipping cost for ' . $dataCountry["name"];
        } else {
            $this->postageCost->upsert($countryID, $zone, $curSign, $fkilo, $nkilo, $this->dateNow);
            $_SESSION['upload_success'] = 'Successfully added new shipping cost for ' . $dataCountry["name"];
        }

        header("Location: {$this->domainURL}delivery-charge");
    }

    public function saveCodCharge()
    {
        $countryId       = $_POST["country_id"] ?? "";
        $shippingZone    = $_POST["shipping_zone"] ?? "1";
        $benchmarkAmount = $_POST["benchmark_amount"] ?? "0";
        $codFeeBelow     = $_POST["cod_fee_below"] ?? "0";
        $codFeeAbove     = $_POST["cod_fee_above"] ?? "0";

        if (empty($countryId)) {
            $_SESSION['upload_success'] = 'Please fill in all required fields.';
            header("Location: {$this->domainURL}delivery-charge");
            return;
        }

        $existing = $this->codCharge->findByCountryZone($countryId, $shippingZone);

        if ($existing) {
            $this->codCharge->upsert($countryId, $shippingZone, $benchmarkAmount, $codFeeBelow, $codFeeAbove, $this->dateNow);
            $_SESSION['upload_success'] = 'COD charge updated successfully.';
        } else {
            $this->codCharge->upsert($countryId, $shippingZone, $benchmarkAmount, $codFeeBelow, $codFeeAbove, $this->dateNow);
            $_SESSION['upload_success'] = 'COD charge added successfully.';
        }

        header("Location: {$this->domainURL}delivery-charge");
    }

    public function deleteCodCharge($id)
    {
        $this->codCharge->deleteById($id);

        $_SESSION['upload_success'] = 'COD charge deleted.';
        header("Location: {$this->domainURL}delivery-charge");
    }

    public function indexAnnouncement()
    {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $options     = $this->options;
        $country     = $this->country;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;

        $pageName = "Announcement & Blog";
        $listNews = $this->newsBlog->getAll();

        require_once __DIR__ . '/../../view/Admin/announcement.php';
    }

    public function saveAnnouncement()
    {
        $title       = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';

        $result = $this->newsBlog->createPost([
            'post_by'    => $_SESSION['user']->id,
            'title'      => $title,
            'contents'   => $description,
            'created_at' => $this->dateNow,
            'updated_at' => $this->dateNow,
        ]);

        if ($result) {
            $_SESSION['upload_success'] = "Successful add new announcement";
        } else {
            $_SESSION['upload_error'] = "Sorry! Failed to add new announcement";
        }

        header("Location: {$this->domainURL}announcement-blog");
        exit;
    }

    public function updateAnnouncement($id)
    {
        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $options     = $this->options;
        $country     = $this->country;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;

        $pageName = "Announcement & Blog (Update)";
        $row = $this->newsBlog->getById($id);

        if (!$row) {
            $_SESSION['upload_error'] = "Sorry! Invalid data/parameter to update Announcement/Blog.";
            header("Location: {$this->domainURL}announcement-blog");
            exit;
        }

        require_once __DIR__ . '/../../view/Admin/update-announcement.php';
    }

    public function saveUpdateAnnouncement($id)
    {
        $title       = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';

        $row = $this->newsBlog->getById($id);

        if (empty($row["update_by"])) {
            $updateBy = "[" . $_SESSION['user']->id . "]|" . $this->dateNow;
        } else {
            $updateBy = $row["update_by"] . ",[" . $_SESSION['user']->id . "]|" . $this->dateNow;
        }

        $result = $this->newsBlog->updatePost($id, [
            'update_by'  => $updateBy,
            'title'      => $title,
            'contents'   => $description,
            'updated_at' => $this->dateNow,
        ]);

        if ($result) {
            $_SESSION['upload_success'] = "Successful update announcement";
        } else {
            $_SESSION['upload_error'] = "Sorry! Failed to update announcement";
        }

        header("Location: {$this->domainURL}update-post/" . $id);
        exit;
    }

    public function deleteAnnouncement($id)
    {
        $row = $this->newsBlog->getById($id);

        if (empty($row["update_by"])) {
            $updateBy = "[" . $_SESSION['user']->id . "]|" . $this->dateNow;
        } else {
            $updateBy = $row["update_by"] . ",[" . $_SESSION['user']->id . "]|" . $this->dateNow;
        }

        $result = $this->newsBlog->softDeletePost($id, $updateBy, $this->dateNow);

        if ($result) {
            $_SESSION['upload_success'] = "Successful update Delete Annpuncement";
        } else {
            $_SESSION['upload_error'] = "Sorry! Failed to delete announcement";
        }

        header("Location: {$this->domainURL}announcement-blog");
        exit;
    }

    public function settingJNT()
    {
        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $options     = $this->options;
        $country     = $this->country;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;

        $pageName = "J&T Setting";
        $jt = dataSettingJNT();

        require_once __DIR__ . '/../../view/Admin/jt-express.php';
    }

    public function saveJNT()
    {
        if (isset($_POST["saveAPI"])) {
            $production_sandbox = $_POST["production_sandbox"];
            $update = $this->courierSetting->updateMode('jt_setting', $production_sandbox);

            if ($update and $production_sandbox == 1) {
                $_SESSION['upload_success'] = "Successful activate J&T Express to Production Mode";
            } else if ($update and $production_sandbox == 0) {
                $_SESSION['upload_success'] = "Successful deactivate J&T Express and set to Production Mode";
            } else {
                $_SESSION['upload_error'] = "Sorry! Invalid data/parameter to update J&T status.";
            }

            header("Location: {$this->domainURL}jt-express");
        }

        if (isset($_POST["saveSandbox"])) {
            $data = [
                'username' => $_POST["username"],
                'password' => $_POST["password"],
                'cuscode'  => $_POST["cuscode"],
                'key'      => $_POST["key"],
            ];
            $update = $this->courierSetting->updateSandbox('jt_setting', $data);

            if ($update) {
                $_SESSION['upload_success'] = "Successful update data in Sandbox Mode";
            } else {
                $_SESSION['upload_error'] = "Sorry! Failed to update data in Sandbox Mode.";
            }

            header("Location: {$this->domainURL}jt-express");
        }

        if (isset($_POST["saveProduction"])) {
            $data = [
                'username' => $_POST["username"],
                'password' => $_POST["password"],
                'cuscode'  => $_POST["cuscode"],
                'key'      => $_POST["key"],
            ];
            $update = $this->courierSetting->updateProduction('jt_setting', $data);

            if ($update) {
                $_SESSION['upload_success'] = "Successful update data in Production Mode";
            } else {
                $_SESSION['upload_error'] = "Sorry! Failed to update data in Production Mode.";
            }

            header("Location: {$this->domainURL}jt-express");
        }
    }

    public function settingNinjaVan()
    {
        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $options     = $this->options;
        $country     = $this->country;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;

        $pageName = "NinjaVan Setting";
        $ninjavan = dataSettingNinjaVan();

        require_once __DIR__ . '/../../view/Admin/ninjavan-setting.php';
    }

    public function saveNinjaVan()
    {
        if (isset($_POST["saveAPI"])) {
            $production_sandbox = $_POST["production_sandbox"];
            $update = $this->courierSetting->updateMode('ninjavan_setting', $production_sandbox);

            if ($update and $production_sandbox == 1) {
                $_SESSION['upload_success'] = "Successful activate NinjaVan to Production Mode";
            } else if ($update and $production_sandbox == 0) {
                $_SESSION['upload_success'] = "Successful deactivate NinjaVan and set to Sandbox Mode";
            } else {
                $_SESSION['upload_error'] = "Sorry! Invalid data/parameter to update NinjaVan status.";
            }

            header("Location: {$this->domainURL}ninjavan-setting");
        }

        if (isset($_POST["saveSandbox"])) {
            $data = [
                'username' => $_POST["username"],
                'password' => $_POST["password"],
                'cuscode'  => $_POST["cuscode"],
                'key'      => $_POST["key"],
            ];
            $update = $this->courierSetting->updateSandbox('ninjavan_setting', $data);

            if ($update) {
                $_SESSION['upload_success'] = "Successful update data in Sandbox Mode";
            } else {
                $_SESSION['upload_error'] = "Sorry! Failed to update data in Sandbox Mode.";
            }

            header("Location: {$this->domainURL}ninjavan-setting");
        }

        if (isset($_POST["saveProduction"])) {
            $data = [
                'username' => $_POST["username"],
                'password' => $_POST["password"],
                'cuscode'  => $_POST["cuscode"],
                'key'      => $_POST["key"],
            ];
            $update = $this->courierSetting->updateProduction('ninjavan_setting', $data);

            if ($update) {
                $_SESSION['upload_success'] = "Successful update data in Production Mode";
            } else {
                $_SESSION['upload_error'] = "Sorry! Failed to update data in Production Mode.";
            }

            header("Location: {$this->domainURL}ninjavan-setting");
        }
    }

    public function settingPosLaju()
    {
        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $options     = $this->options;
        $country     = $this->country;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;

        $pageName = "Pos Laju Setting";
        $poslaju = dataSettingPosLaju();

        require_once __DIR__ . '/../../view/Admin/poslaju-setting.php';
    }

    public function savePosLaju()
    {
        if (isset($_POST["saveAPI"])) {
            $production_sandbox = $_POST["production_sandbox"];
            $update = $this->courierSetting->updateMode('poslaju_setting', $production_sandbox);

            if ($update and $production_sandbox == 1) {
                $_SESSION['upload_success'] = "Successful activate Pos Laju to Production Mode";
            } else if ($update and $production_sandbox == 0) {
                $_SESSION['upload_success'] = "Successful deactivate Pos Laju and set to Sandbox Mode";
            } else {
                $_SESSION['upload_error'] = "Sorry! Invalid data/parameter to update Pos Laju status.";
            }

            header("Location: {$this->domainURL}poslaju-setting");
        }

        if (isset($_POST["saveSandbox"])) {
            $data = [
                'username' => $_POST["username"],
                'password' => $_POST["password"],
                'cuscode'  => $_POST["cuscode"],
                'key'      => $_POST["key"],
            ];
            $update = $this->courierSetting->updateSandbox('poslaju_setting', $data);

            if ($update) {
                $_SESSION['upload_success'] = "Successful update data in Sandbox Mode";
            } else {
                $_SESSION['upload_error'] = "Sorry! Failed to update data in Sandbox Mode.";
            }

            header("Location: {$this->domainURL}poslaju-setting");
        }

        if (isset($_POST["saveProduction"])) {
            $data = [
                'username' => $_POST["username"],
                'password' => $_POST["password"],
                'cuscode'  => $_POST["cuscode"],
                'key'      => $_POST["key"],
            ];
            $update = $this->courierSetting->updateProduction('poslaju_setting', $data);

            if ($update) {
                $_SESSION['upload_success'] = "Successful update data in Production Mode";
            } else {
                $_SESSION['upload_error'] = "Sorry! Failed to update data in Production Mode.";
            }

            header("Location: {$this->domainURL}poslaju-setting");
        }
    }

    public function changePassword()
    {
        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $options     = $this->options;
        $country     = $this->country;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;

        $pageName = "Password";

        if (isset($_POST["changePass"])) {
            $userID = $_SESSION['user']->id;
            $cpass  = $_POST["cpass"];
            $npass  = $_POST["npass"];
            $cnpass = $_POST["cnpass"];

            $cpassHash  = hash("sha256", $cpass);
            $npassHash  = hash("sha256", $npass);
            $cnpassHash = hash("sha256", $cnpass);

            $row = $this->memberHq->findById($userID);

            $errors = "";

            if ($cpassHash != $cpass) {
                $errors .= "Invalid Current Password. ";
            }

            if ($npassHash != $cnpassHash) {
                $errors .= "New Password and Confirm Password must be same. ";
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
        $userID = $_SESSION['user']->id;
        $cpass  = $_POST["cpass"];
        $npass  = $_POST["npass"];
        $cnpass = $_POST["cnpass"];

        $cpassHash  = hash("sha256", $cpass);
        $npassHash  = hash("sha256", $npass);
        $cnpassHash = hash("sha256", $cnpass);

        $row = $this->memberHq->findById($userID);

        $errors = "";

        if ($cpassHash != $row["password"]) {
            $errors .= "Invalid Current Password. ";
        }

        if ($npassHash != $cnpassHash) {
            $errors .= "New Password and Confirm Password must be same. ";
        }

        if (!empty($errors)) {
            $_SESSION['upload_error'] = "Sorry error updating password! " . $errors;
        } else {
            $this->memberHq->updatePassword($userID, $npassHash);
            $_SESSION['upload_success'] = "Successful updating your password. You are advised to logout from system and login using new password.";
        }

        header("Location: {$this->domainURL}password");
        exit;
    }

    public function imageSetting()
    {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $options     = $this->options;
        $country     = $this->country;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;

        $pageName = "Setting Image";
        $rows = $this->imageSetting->getLogos();

        require_once __DIR__ . '/../../view/Admin/image-setting.php';
    }

    public function setLogo()
    {
        $this->checkAccess('logo-setting');

        if (isset($_GET["id"]) and !empty($_GET["id"])) {
            $id = $_GET["id"];

            $result = $this->imageSetting->setDefault($id);

            if ($result) {
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
        $this->checkAccess();

        if (isset($_POST['uploadLogo']) && isset($_FILES['file'])) {
            $uploadDir = "assets/images/logo/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['file']['tmp_name'] as $key => $tmpName) {
                if (!empty($tmpName)) {
                    $fileName   = $this->currentYear . "_rozeyana_" . basename($_FILES['file']['name'][$key]);
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $sorting = '0';
                        $message = "Successful upload new logo.";

                        if (!empty($_POST['defaultLogo'])) {
                            $this->imageSetting->disableAll();
                            $sorting = '1';
                            $message = "Successful upload new logo and set as default.";
                        }

                        $this->imageSetting->addImage([
                            'use_type'   => 'logo',
                            'image_path' => 'assets/images/logo/' . $fileName,
                            'sorting'    => $sorting,
                            'created_at' => $this->dateNow,
                            'updated_at' => $this->dateNow,
                        ]);
                        $_SESSION['upload_success'] = $message;
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
