<?php
namespace Setting;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/ListCountry.php';

class countryController
{
    private $countryModel;

    public function __construct()
    {
        $this->countryModel = new \ListCountry();
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
        $conn = getDbConnection();
        $country = allSaleCountry();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];

        $pageName = "List Country";

        $result = $this->countryModel->getAll();

        require_once __DIR__ . '/../../view/Admin/country.php';
    }

    public function addNewCountry()
    {
        $this->checkAccess();

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $country = allSaleCountry();

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];

        $pageName = "Add New Country";

        $allCountries = [];
        $savedCountryNames = [];

        $rows = $this->countryModel->getNamesAndCodes();
        foreach ($rows as $row) {
            $allCountries[] = [
                "name" => $row['name'],
                "code" => $row['phone_code']
            ];
            $savedCountryNames[] = $row['name'];
        }

        $result1 = $this->countryModel->getAllWorldCountries();

        require_once __DIR__ . '/../../view/Admin/new-country.php';
    }

    public function saveCountry()
    {
        $this->checkAccess();

        $domainURL = getMainUrl();
        $dateNow = dateNow();

        $country = $_POST["country"];
        $currency = $_POST["currency"];
        $rate = $_POST["rate"];
        $phone_code = $_POST["phone_code"];

        $this->countryModel->addCountry([
            'name' => $country,
            'sign' => $currency,
            'rate' => $rate,
            'phone_code' => $phone_code,
            'created_at' => $dateNow,
            'updated_at' => $dateNow
        ]);

        $_SESSION['upload_success'] = "Successfull add new country.";
        header("Location: " . $domainURL . "add-new-country");
        exit();
    }

    public function updateCountry()
    {
        $this->checkAccess();

        $domainURL = getMainUrl();
        $dateNow = dateNow();

        $cid = $_POST["cid"];
        $cnames = $_POST["cnames"];
        $ccode = $_POST["ccode"];
        $crate = $_POST["crate"];
        $cstatus = $_POST["cstatus"];

        $this->countryModel->updateCountry($cid, [
            'sign' => $ccode,
            'rate' => $crate,
            'updated_at' => $dateNow,
            'status' => $cstatus
        ]);

        $_SESSION['upload_success'] = "Successfull update data for " . $cnames . ".";
        header("Location: " . $domainURL . "list-country");
        exit();
    }
}
