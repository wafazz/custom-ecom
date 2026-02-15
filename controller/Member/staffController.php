<?php

namespace Member;

require_once __DIR__ . '/../../config/mainConfig.php';

class staffController
{
    private $domainURL;
    private $mainDomain;
    private $conn;
    private $options;
    private $country;
    private $currentYear;
    private $dateNow;

    public function __construct()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $domainURL  = getMainUrl();
        $this->mainDomain = mainDomain();
        $this->conn       = getDbConnection();
        $this->options    = getSelectOptions();
        $this->country    = allSaleCountry();
        $this->currentYear = currentYear();
        $this->dateNow     = dateNow();
    }

    public function index()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
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


        $pageName = "Staff List";

        $urID = $_SESSION['user']->id;

        $result = $conn->query("SELECT * FROM member_hq WHERE (id != '1' AND id != $urID) AND deleted_at IS NULL");

        require_once __DIR__ . '/../../view/Admin/staff.php';
    }

    public function userDetails($id)
    {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();

        if($_SESSION['user']->id == $id || $id == 1){
            $_SESSION['upload_error'] = 'Access denied to update your own setting.';
            header("Location: ".$domainURL."hq-staff");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: ".$domainURL."access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $pageName = "Staff Details";

        $result = $this->conn->query("SELECT * FROM member_hq WHERE id = '$id'");

        $row = $result->fetch_array();

        $permission = $conn->query("SELECT * FROM role_access ORDER BY sort ASC");

        require_once __DIR__ . '/../../view/Admin/staff-details.php';
    }

    public function saveUsers()
    {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $designation = isset($_POST['designation']) ? $this->conn->real_escape_string($_POST['designation']) : '';
        $fname = isset($_POST['fname']) ? $this->conn->real_escape_string($_POST['fname']) : '';
        $lname = isset($_POST['lname']) ? $this->conn->real_escape_string($_POST['lname']) : '';
        $email = isset($_POST['email']) ? $this->conn->real_escape_string($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? $this->conn->real_escape_string($_POST['phone']) : '';
        $password = isset($_POST['password']) ? $this->conn->real_escape_string($_POST['password']) : '';
        $hashPassword = hash('sha256', $password);

        $now = $this->dateNow;

        $sql = "
            INSERT INTO `member_hq`
            (`id`, `email`, `password`, `sec_pin`, `f_name`, `l_name`, `phone`, `role`, `created_at`, `updated_at`, `deleted_at`, `status`) 
            VALUES 
            (NULL,'$email','$hashPassword','$hashPassword','$fname','$lname','$phone','$designation','$now','$now',NULL,'1')
        ";


        $query = $this->conn->query($sql);

        if ($query) {
            $_SESSION['upload_success'] = 'Successful register new user.';
            header("Location: {$this->domainURL}hq-staff");
            exit();
        } else {
            $_SESSION['upload_error'] = 'Failed register new user. Try again later.';
            header("Location: {$this->domainURL}hq-staff");
            exit();
        }
    }

    public function bannUsers($id)
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();


        $now = $this->dateNow;

        $sql = "
            UPDATE `member_hq`
            SET
            `status`='2', updated_at='$now'
            WHERE id='$id'
        ";


        $query = $this->conn->query($sql);

        if ($query) {
            $_SESSION['upload_success'] = 'Successful banned user #' . $id . '.';
            header("Location: " . $domainURL . "hq-staff");
            exit();
        }
    }

    public function unbannUsers($id)
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();


        $now = $this->dateNow;

        $sql = "
            UPDATE `member_hq`
            SET
            `status`='1', updated_at='$now'
            WHERE id='$id'
        ";


        $query = $this->conn->query($sql);

        if ($query) {
            $_SESSION['upload_success'] = 'Successful unbanned user #' . $id . '.';
            header("Location: " . $domainURL . "hq-staff");
            exit();
        }
    }

    public function userDelete($id)
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();


        $now = $this->dateNow;

        $sql = "
            UPDATE `member_hq`
            SET
            `status`='3', updated_at='$now', deleted_at='$now'
            WHERE id='$id'
        ";


        $query = $this->conn->query($sql);

        if ($query) {
            $_SESSION['upload_success'] = 'Successful deleted user #' . $id . '.';
            header("Location: " . $domainURL . "hq-staff");
            exit();
        }
    }

    public function userPermission()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();

        $id = $_GET["id"];
        $status = $_GET["status"];
        $userid = $_GET["user"];

        //if $status==1, need to remove - $status==2, need to add

        $query = $conn->query("SELECT * FROM role_access WHERE id='$id'");
        $row = $query->fetch_array();

        if ($status == "1") {
            $userids = $row["allowed_user"];
            $items = explode(',', $userids);

            // Filter out [4]
            $filtered = array_filter($items, function ($item) use ($userid) {
                return trim($item) !== "[$userid]";
            });

            $newUserids = implode(',', $filtered);

            $query = $conn->query("UPDATE role_access SET allowed_user='$newUserids' WHERE id='$id'");
        }else if ($status == "2") {
            $userids = $row["allowed_user"].",[".$userid."]";

            $query = $conn->query("UPDATE role_access SET allowed_user='$userids' WHERE id='$id'");
        }
    }
}
