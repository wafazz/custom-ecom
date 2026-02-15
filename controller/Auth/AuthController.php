<?php
namespace Auth;

require_once __DIR__ . '/../../config/mainConfig.php';

class AuthController {
    public function index() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $errorMessage = $_SESSION['errorMessage'] ?? '';
        unset($_SESSION['errorMessage']); 
        
        $sql = "SELECT * FROM `image_setting` WHERE `use_type`='logo' AND sorting='1'";
        $query = $conn->query($sql);
        $row = $query->fetch_assoc();

        //echo $mainDomain;
        
        require_once __DIR__ . '/../../view/Auth/login.php';
    }


    public function processLogin() {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $token = $_POST["_token"];

        $email = $_POST['user_email'] ?? '';
        $password = $_POST['user_pass'] ?? '';

        $message = login($email, $password);

        $_SESSION['errorMessage'] = $message;

        header("Location: login");

        //echo $mainDomain;
        
        //require_once __DIR__ . '/../../view/Auth/login.php';
    }
    
}