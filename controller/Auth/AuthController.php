<?php
namespace Auth;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/ImageSetting.php';

class AuthController {

    private $imageModel;

    public function __construct()
    {
        $this->imageModel = new \ImageSetting();
    }

    public function index() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();

        $errorMessage = $_SESSION['errorMessage'] ?? '';
        unset($_SESSION['errorMessage']);

        $row = $this->imageModel->findOne(['use_type' => 'logo', 'sorting' => '1']);

        require_once __DIR__ . '/../../view/Auth/login.php';
    }


    public function processLogin() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();

        $token = $_POST["_token"];

        $email = $_POST['user_email'] ?? '';
        $password = $_POST['user_pass'] ?? '';

        $message = login($email, $password);

        $_SESSION['errorMessage'] = $message;

        header("Location: login");
    }

}
