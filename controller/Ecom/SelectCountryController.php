<?php
namespace Ecom;

require_once __DIR__ . '/../../config/mainConfig.php';

class SelectCountryController
{
    public function index()
    {
        $domainURL = getMainUrl();
        if (isset($_COOKIE['country'])) {
            header("Location: ".$domainURL."main");
            exit;
        }
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Select Country";

        $listCountry = allSaleCountry();

        $brands = getListCategoryBrand(1);
        $categories = getListCategoryBrand(2);
        $categories2 = getListCategoryBrand2(2);

        $newArrival = newProduct(8);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['country'])) {
                $country = $_POST['country'];

                $cookieName = "country";
                $cookieValue = $country;
                $expireTime = time() + (60 * 60 * 24 * 60); 
                setcookie($cookieName, $cookieValue, $expireTime, "/"); // "/" = available across whole domain

                header("Location: ".$domainURL."main");
                exit;

            } 
        }

        require_once __DIR__ . '/../../view/Auth/selectCountry.php';
    }
}