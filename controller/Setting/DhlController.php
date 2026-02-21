<?php
namespace Setting;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/DhlSetting.php';

class DhlController
{
    private $dhlModel;

    public function __construct()
    {
        $this->dhlModel = new \DhlSetting();
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

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $options = getSelectOptions();
        $country = allSaleCountry();
        $pageName = "DHL - Setting";
        $currentYear = currentYear();
        $dateNow = dateNow();

        $dhl = dhlDetails();

        require_once __DIR__ . '/../../view/Admin/dhl-setting.php';
    }

    public function saveDHL()
    {
        $this->checkAccess();

        $domainURL = getMainUrl();
        $dateNow = dateNow();

        if (isset($_POST["saveSandbox"])) {
            $clientid = $_POST["clientidS"];
            $password = $_POST["passwordS"];

            $this->dhlModel->updateSandbox($clientid, $password);
            tokenDHLOnSaveSetting($clientid, $password, 2, $dateNow);

            $_SESSION['upload_success'] = "Successfull save data DHL Sandbox.";
        } else if (isset($_POST["saveProduction"])) {
            $clientid = $_POST["clientidP"];
            $password = $_POST["passwordP"];

            $this->dhlModel->updateProduction($clientid, $password);

            if (isset($_POST['createToken'])) {
                tokenDHLOnSaveSetting($clientid, $password, 1, $dateNow);
                $_SESSION['upload_success'] = "Successfull save data DHL Production and create new token.";
            } else {
                $_SESSION['upload_success'] = "Successfull save data DHL Production and don't create new token.";
            }
        } else if (isset($_POST["saveAPI"])) {
            $production_sandbox = $_POST["production_sandbox"];
            $this->dhlModel->updateMode($production_sandbox);

            if ($production_sandbox == "1") {
                $_SESSION['upload_success'] = "Successfull activate Production API.";
            } else if ($production_sandbox == "2") {
                $_SESSION['upload_success'] = "Successfull activate SandBox API.";
            }
        }

        header("Location: " . $domainURL . "dhl-setting");
        exit();
    }
}
