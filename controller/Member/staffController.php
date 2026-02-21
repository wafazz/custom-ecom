<?php

namespace Member;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/MemberHq.php';
require_once __DIR__ . '/../../model/RoleAccess.php';

class staffController
{
    public $domainURL;
    private $conn;
    private $memberModel;
    private $roleModel;
    private $dateNow;

    public function __construct()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $this->domainURL = getMainUrl();
        $this->conn = getDbConnection();
        $this->dateNow = dateNow();
        $this->memberModel = new \MemberHq($this->conn);
        $this->roleModel = new \RoleAccess($this->conn);
    }

    private function checkAccess()
    {
        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];
        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            exit;
        }
    }

    public function index()
    {
        $this->checkAccess();

        $domainURL = $this->domainURL;
        $mainDomain = mainDomain();
        $conn = $this->conn;
        $currentYear = currentYear();
        $dateNow = $this->dateNow;

        $pageName = "Staff List";

        $urID = $_SESSION['user']->id;

        $result = $this->memberModel->getStaffList([1, $urID]);

        require_once __DIR__ . '/../../view/Admin/staff.php';
    }

    public function userDetails($id)
    {
        $domainURL = $this->domainURL;
        $mainDomain = mainDomain();
        $conn = $this->conn;
        $currentYear = currentYear();
        $dateNow = $this->dateNow;

        if ($_SESSION['user']->id == $id || $id == 1) {
            $_SESSION['upload_error'] = 'Access denied to update your own setting.';
            header("Location: " . $domainURL . "hq-staff");
            exit;
        }

        $this->checkAccess();

        $pageName = "Staff Details";

        $row = $this->memberModel->findById($id);

        $permission = $this->roleModel->getAllSorted();

        require_once __DIR__ . '/../../view/Admin/staff-details.php';
    }

    public function saveUsers()
    {
        $domainURL = $this->domainURL;

        $designation = $_POST['designation'] ?? '';
        $fname = $_POST['fname'] ?? '';
        $lname = $_POST['lname'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';
        $hashPassword = hash('sha256', $password);

        $now = $this->dateNow;

        $result = $this->memberModel->addStaff([
            'email' => $email,
            'password' => $hashPassword,
            'sec_pin' => $hashPassword,
            'f_name' => $fname,
            'l_name' => $lname,
            'phone' => $phone,
            'role' => $designation,
            'created_at' => $now,
            'updated_at' => $now
        ]);

        if ($result) {
            $_SESSION['upload_success'] = 'Successful register new user.';
        } else {
            $_SESSION['upload_error'] = 'Failed register new user. Try again later.';
        }
        header("Location: {$domainURL}hq-staff");
        exit();
    }

    public function bannUsers($id)
    {
        $domainURL = $this->domainURL;

        $this->memberModel->updateMemberStatus($id, '2', $this->dateNow);

        $_SESSION['upload_success'] = 'Successful banned user #' . $id . '.';
        header("Location: " . $domainURL . "hq-staff");
        exit();
    }

    public function unbannUsers($id)
    {
        $domainURL = $this->domainURL;

        $this->memberModel->updateMemberStatus($id, '1', $this->dateNow);

        $_SESSION['upload_success'] = 'Successful unbanned user #' . $id . '.';
        header("Location: " . $domainURL . "hq-staff");
        exit();
    }

    public function userDelete($id)
    {
        $domainURL = $this->domainURL;

        $this->memberModel->updateMemberStatus($id, '3', $this->dateNow, true);

        $_SESSION['upload_success'] = 'Successful deleted user #' . $id . '.';
        header("Location: " . $domainURL . "hq-staff");
        exit();
    }

    public function userPermission()
    {
        $id = $_GET["id"];
        $status = $_GET["status"];
        $userid = $_GET["user"];

        $row = $this->roleModel->getById($id);

        if ($status == "1") {
            $userids = $row["allowed_user"];
            $items = explode(',', $userids);

            $filtered = array_filter($items, function ($item) use ($userid) {
                return trim($item) !== "[$userid]";
            });

            $newUserids = implode(',', $filtered);
            $this->roleModel->updateAllowedUser($id, $newUserids);
        } else if ($status == "2") {
            $userids = $row["allowed_user"] . ",[" . $userid . "]";
            $this->roleModel->updateAllowedUser($id, $userids);
        }
    }
}
