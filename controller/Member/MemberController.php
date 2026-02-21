<?php

namespace Member;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/Order.php';
require_once __DIR__ . '/../../model/MemberHq.php';
require_once __DIR__ . '/../../model/Activity.php';

class MemberController
{
    private $conn;
    private $orderModel;
    private $memberModel;
    private $activityModel;

    public function __construct()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $this->conn = getDbConnection();
        $this->orderModel = new \Order($this->conn);
        $this->memberModel = new \MemberHq($this->conn);
        $this->activityModel = new \Activity($this->conn);
    }

    public function dashboard()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;

        $pageName = "Dashboard";

        $query = $this->orderModel->getLatestOrders(30);

        $result = $this->activityModel->getWithMembers();

        require_once __DIR__ . '/../../view/Admin/dashboard.php';
    }

    public function salesStatistics()
    {
        $conn = $this->conn;

        $todayStart = date('Y-m-d');
        $todayEnd = date('Y-m-d', strtotime('+1 day'));

        $yesterdayStart = date('Y-m-d', strtotime('-1 day'));
        $yesterdayEnd = $todayStart;

        $thisWeekStart = date('Y-m-d', strtotime('monday this week'));
        $thisWeekEnd = $todayEnd;

        $lastWeekStart = date('Y-m-d', strtotime('monday last week'));
        $lastWeekEnd = $thisWeekStart;

        $thisMonthStart = date('Y-m-01');
        $thisMonthEnd = $todayEnd;

        $lastMonthStart = date('Y-m-01', strtotime('-1 month'));
        $lastMonthEnd = $thisMonthStart;

        $thisYearStart = date('Y-01-01');
        $thisYearEnd = $todayEnd;

        $lastYearStart = date('Y-01-01', strtotime('-1 year'));
        $lastYearEnd = $thisYearStart;

        $today = fetchSum($conn, $todayStart, $todayEnd);
        $yesterday = fetchSum($conn, $yesterdayStart, $yesterdayEnd);

        $thisWeek = fetchSum($conn, $thisWeekStart, $thisWeekEnd);
        $lastWeek = fetchSum($conn, $lastWeekStart, $lastWeekEnd);

        $thisMonth = fetchSum($conn, $thisMonthStart, $thisMonthEnd);
        $lastMonth = fetchSum($conn, $lastMonthStart, $lastMonthEnd);

        $thisYear = fetchSum($conn, $thisYearStart, $thisYearEnd);
        $lastYear = fetchSum($conn, $lastYearStart, $lastYearEnd);

        $daySales = compare($today['sales'], $yesterday['sales']);
        $dayRevenue = compare($today['revenue'], $yesterday['revenue']);

        $weekSales = compare($thisWeek['sales'], $lastWeek['sales']);
        $weekRevenue = compare($thisWeek['revenue'], $lastWeek['revenue']);

        $monthSales = compare($thisMonth['sales'], $lastMonth['sales']);
        $monthRevenue = compare($thisMonth['revenue'], $lastMonth['revenue']);

        $yearSales = compare($thisYear['sales'], $lastYear['sales']);
        $yearRevenue = compare($thisYear['revenue'], $lastYear['revenue']);

        require_once __DIR__ . '/../../view/Admin/sales-stats.php';
    }

    public function profile()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;

        $pageName = "Profile";

        $row = $this->memberModel->findById($_SESSION['user']->id);

        require_once __DIR__ . '/../../view/Admin/profile.php';
    }

    public function updateProfile()
    {
        $domainURL = getMainUrl();
        $dateNow = dateNow();

        $fname = $_POST['fname'] ?? '';
        $lname = $_POST['lname'] ?? '';
        $phone = $_POST['phone'] ?? '';

        $result = $this->memberModel->updateProfile($_SESSION['user']->id, [
            'f_name' => $fname,
            'l_name' => $lname,
            'phone' => $phone
        ], $dateNow);

        if ($result) {
            $_SESSION['upload_success'] = 'Successful update your profile.';
        } else {
            $_SESSION['upload_error'] = 'Failed to update profile. Try again later.';
        }
        header("Location: {$domainURL}profile");
        exit();
    }

    public function salesReport()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;

        $pageName = "Sales Report";

        $where = [];
        $result = null;
        $sumResult = null;

        $type = $_GET['type'] ?? null;

        if (!empty($_GET['type'] and (!empty($_GET['from']) or !empty($_GET['to']) or !empty($_GET['week'])) or !empty($_GET['month']) or !empty($_GET['year']))) {
            $where = buildDateFilter($type, $_GET);

            $where[] = "status IN (1,2,3,4)";
            $where[] = "deleted_at IS NULL";

            $whereSql = 'WHERE ' . implode(' AND ', $where);

            $result = $this->orderModel->salesReport($whereSql);
            $sumResult = $this->orderModel->salesReportSum($whereSql);
        }

        require_once __DIR__ . '/../../view/Admin/sales-report.php';
    }

    public function testMailer()
    {
        $code = random_int(100000, 999999);

        if (sendSecurityCode('fakrul2897@gmail.com', $code)) {
            echo 'Security code sent';
        } else {
            echo 'Failed to send email';
        }
    }

    public function logout()
    {
        unset($_SESSION["user"]);
        header("Location: login");
        exit;
    }
}
