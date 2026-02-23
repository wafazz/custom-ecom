<?php
namespace Setting;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/SenangPaySetting.php';

class SenangPayController
{
    private $conn;
    private $senangPayModel;

    public function __construct()
    {
        $this->conn = getDbConnection();
        $this->senangPayModel = new \SenangPaySetting();
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
        $pageName = "Senangpay Setting";
        $currentYear = currentYear();
        $dateNow = dateNow();

        $senangpay = $this->senangPayModel->getSettings();

        require_once __DIR__ . '/../../view/Admin/senangpay-setting.php';
    }

    public function saveSenangPay()
    {
        $this->checkAccess();

        $domainURL = getMainUrl();

        if (isset($_POST["saveSandbox"])) {
            $merchantId = $_POST["merchantIdS"];
            $secretKey = $_POST["secretKeyS"];

            $this->senangPayModel->updateSandbox($merchantId, $secretKey);

            $_SESSION['upload_success'] = "Successfully saved SenangPay Sandbox credentials.";
        } else if (isset($_POST["saveProduction"])) {
            $merchantId = $_POST["merchantIdP"];
            $secretKey = $_POST["secretKeyP"];

            $this->senangPayModel->updateProduction($merchantId, $secretKey);

            $_SESSION['upload_success'] = "Successfully saved SenangPay Production credentials.";
        } else if (isset($_POST["saveMode"])) {
            $type = $_POST["type"];
            $this->senangPayModel->updateMode($type);

            if ($type == "production") {
                $_SESSION['upload_success'] = "Successfully activated Production mode.";
            } else {
                $_SESSION['upload_success'] = "Successfully activated Sandbox mode.";
            }
        }

        header("Location: " . $domainURL . "senangpay-setting");
        exit();
    }
}
