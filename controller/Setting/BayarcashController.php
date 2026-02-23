<?php
namespace Setting;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/Bayarcash.php';

class BayarcashController
{
    private $conn;
    private $bayarcashModel;

    public function __construct()
    {
        $this->conn = getDbConnection();
        $this->bayarcashModel = new \Bayarcash();
    }

    private function checkAccess()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }
        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];
        $domainURL = getMainUrl();
        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $domainURL . "access-denied");
            exit;
        }
    }

    public function index()
    {
        $this->checkAccess();

        $conn = $this->conn;
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $options = getSelectOptions();
        $country = allSaleCountry();
        $pageName = "Bayarcash Setting";
        $currentYear = currentYear();
        $dateNow = dateNow();

        $bayarcash = $this->bayarcashModel->getSettings();

        require_once __DIR__ . '/../../view/Admin/bayarcash-setting.php';
    }

    public function saveBayarcash()
    {
        $this->checkAccess();

        $domainURL = getMainUrl();

        if (isset($_POST["saveSandbox"])) {
            $apiToken = $_POST["sandboxApiToken"];
            $secretKey = $_POST["sandboxSecretKey"];
            $portalKey = $_POST["sandboxPortalKey"];

            $this->bayarcashModel->updateSandbox($apiToken, $secretKey, $portalKey);

            $_SESSION['upload_success'] = "Successfully saved Bayarcash Sandbox credentials.";
        } else if (isset($_POST["saveProduction"])) {
            $apiToken = $_POST["prodApiToken"];
            $secretKey = $_POST["prodSecretKey"];
            $portalKey = $_POST["prodPortalKey"];

            $this->bayarcashModel->updateProduction($apiToken, $secretKey, $portalKey);

            $_SESSION['upload_success'] = "Successfully saved Bayarcash Production credentials.";
        } else if (isset($_POST["saveMode"])) {
            $type = $_POST["type"];
            $this->bayarcashModel->updateMode($type);

            if ($type == "production") {
                $_SESSION['upload_success'] = "Successfully activated Production mode.";
            } else {
                $_SESSION['upload_success'] = "Successfully activated Sandbox mode.";
            }
        }

        header("Location: " . $domainURL . "bayarcash-setting");
        exit();
    }
}
