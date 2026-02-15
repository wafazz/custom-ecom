<?php
namespace Setting;

require_once __DIR__ . '/../../config/mainConfig.php';

class countryController
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
        $country = allSaleCountry();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: ".$domainURL."access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $pageName = "List Country";

        $sql = "SELECT `id`, `name`, `sign`, `rate`, `phone_code`, `created_at`, `updated_at`, `status` FROM `list_country`";
        $result = $conn->query($sql);

        require_once __DIR__ . '/../../view/Admin/country.php';
    }

    public function addNewCountry()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $country = allSaleCountry();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: ".$domainURL."access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $pageName = "Add New Country";


        $allCountries = [];

        $sql = "SELECT `name`, `phone_code` FROM list_country ORDER BY name ASC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $allCountries[] = [
                    "name" => $row['name'],
                    "code" => $row['phone_code']
                ];
                $savedCountryNames[] = $row['name'];
            }
        }

        $sql1 = "SELECT * FROM all_country";
        $result1 = $conn->query($sql1);

        require_once __DIR__ . '/../../view/Admin/new-country.php';
    }

    public function saveCountry()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $country = allSaleCountry();
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

        $country = $_POST["country"];
        $currency = $_POST["currency"];
        $rate = $_POST["rate"];
        $phone_code = $_POST["phone_code"];


        $add = $conn->query("INSERT INTO `list_country`(`id`, `name`, `sign`, `rate`, `phone_code`, `created_at`, `updated_at`, `status`) VALUES (NULL,'$country','$currency','$rate','$phone_code','$dateNow','$dateNow','1')");


        $_SESSION['upload_success'] = "Successfull add new country.";
        header("Location: ".$domainURL."add-new-country");
        exit();
    }

    public function updateCountry()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $country = allSaleCountry();
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

        $cid = $_POST["cid"];
        $cnames = $_POST["cnames"];
        $ccode = $_POST["ccode"];
        $crate = $_POST["crate"];
        $cstatus = $_POST["cstatus"];

        $add = $conn->query("UPDATE  `list_country` SET `sign`='$ccode', `rate`='$crate', `updated_at`='$dateNow', `status`='$cstatus' WHERE id='$cid'");


        $_SESSION['upload_success'] = "Successfull update data for ".$cnames.".";
        header("Location: ".$domainURL."list-country");
        exit();
    }
}