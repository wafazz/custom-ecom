<?php
namespace Setting;

require_once __DIR__ . '/../../config/mainConfig.php';

class DhlController
{
    public function index()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();

        $pageName = "DHL - Setting";

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

        $dhl = dhlDetails();

        require_once __DIR__ . '/../../view/Admin/dhl-setting.php';
    }

    public function saveDHL()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $options = getSelectOptions();
        $country = allSaleCountry();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: ".$domainURL."access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $pageName = "DHL - Setting";

        $currentYear = currentYear();
        $dateNow = dateNow();

        $dhl = dhlDetails();

        if(isset($_POST["saveSandbox"])){
            $clientid = $_POST["clientidS"];
            $password = $_POST["passwordS"];

            $sql = "UPDATE dhl SET clientid_test='$clientid', password_test='$password' WHERE id='1'";

            $query = $conn->query($sql);

            tokenDHLOnSaveSetting($clientid, $password, 2, $dateNow);

            $_SESSION['upload_success'] = "Successfull save data DHL Sandbox.";
        }else if(isset($_POST["saveProduction"])){
            $clientid = $_POST["clientidP"];
            $password = $_POST["passwordP"];

            $sql = "UPDATE dhl SET `clientid`='$clientid', `password`='$password' WHERE id='1'";

            $query = $conn->query($sql);

            if (isset($_POST['createToken'])) {
                // Checkbox was checked
                tokenDHLOnSaveSetting($clientid, $password, 1, $dateNow);
                $_SESSION['upload_success'] = "Successfull save data DHL Production and create new token.";
            }else{
                $_SESSION['upload_success'] = "Successfull save data DHL Production and don't create new token.";
            }

            
        }else if(isset($_POST["saveAPI"])){
            $production_sandbox = $_POST["production_sandbox"];

            if($production_sandbox == "1"){
                $sql = "UPDATE dhl SET `production_sandbox`='1' WHERE id='1'";
                $_SESSION['upload_success'] = "Successfull activate Production API.";
            }else if($production_sandbox == "2"){
                $sql = "UPDATE dhl SET `production_sandbox`='2' WHERE id='1'";
                $_SESSION['upload_success'] = "Successfull activate SandBox API.";
            }

            $query = $conn->query($sql);
        }

        header("Location: ".$domainURL."dhl-setting");
        exit();
    }
}