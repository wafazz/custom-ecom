<?php

namespace Member;

require_once __DIR__ . '/../../config/mainConfig.php';

class MemberController
{

    public function __construct()
    {
        // This runs automatically when the controller is instantiated
        if (!is_login()) {
            header("Location: login");
            exit;
        }
    }

    public function dashboard()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $pageName = "Dashboard";

        // Latest 30 orders
        $sql = "SELECT * FROM `customer_orders` WHERE `status` IN(1,2,3,4,5,6) AND deleted_at IS NULL ORDER BY id DESC LIMIT 30";
        $query = $conn->query($sql);

        $sqli = "
            SELECT 
                a.id AS activity_id,
                a.user_id,
                a.created_at AS activity_created,
                a.updated_at AS activity_updated,
                a.deleted_at AS activity_deleted,
                a.description,
                a.table_name,
                a.activities,
                m.id AS member_id,
                m.email,
                m.password,
                m.sec_pin,
                m.f_name,
                m.l_name,
                m.phone,
                m.role,
                m.created_at AS member_created,
                m.updated_at AS member_updated,
                m.deleted_at AS member_deleted,
                m.status
            FROM activities a
            LEFT JOIN member_hq m ON a.user_id = m.id
            ORDER BY activity_id DESC
        ";

        $result = $conn->query($sqli);

        require_once __DIR__ . '/../../view/Admin/dashboard.php';
    }

    public function salesStatistics()
    {
        $conn = getDbConnection();

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
        $conn = getDbConnection();

        $pageName = "Profile";

        // Latest 30 orders
        $sql = "SELECT * FROM `member_hq` WHERE `id`='" . $_SESSION['user']->id . "'";
        $query = $conn->query($sql);

        $row = $query->fetch_array();

        require_once __DIR__ . '/../../view/Admin/profile.php';
    }

    public function updateProfile()
    {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $fname = isset($_POST['fname']) ? $conn->real_escape_string($_POST['fname']) : '';
        $lname = isset($_POST['lname']) ? $conn->real_escape_string($_POST['lname']) : '';
        $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';


        $sql = "
            UPDATE `member_hq`
            SET `f_name`='$fname', `l_name`='$lname', `phone`='$phone', `updated_at`='$dateNow' WHERE id='" . $_SESSION['user']->id . "'
        ";


        $query = $conn->query($sql);

        if ($query) {
            $_SESSION['upload_success'] = 'Successful update your profile.';
            header("Location: {$domainURL}profile");
            exit();
        } else {
            $_SESSION['upload_error'] = 'Failed to update profile. Try again later.';
            header("Location: {$domainURL}profile");
            exit();
        }
    }

    public function salesReport()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $pageName = "Sales Report";

        $where = [];

        $type = $_GET['type'] ?? null;

        if (!empty($_GET['type'] and (!empty($_GET['from']) or !empty($_GET['to']) or !empty($_GET['week'])) or !empty($_GET['month']) or !empty($_GET['year']))) {
            $where = buildDateFilter($type, $_GET);


            $where[] = "status IN (1,2,3,4)";
            $where[] = "deleted_at IS NULL";

            $whereSql = 'WHERE ' . implode(' AND ', $where);

            $sql = "
                SELECT *
                FROM customer_orders
                $whereSql
                ORDER BY created_at DESC
                ";

            $sumSql = "
                SELECT 
                    COALESCE(SUM(myr_value_include_postage),0) AS total_sales,
                    COALESCE(SUM(myr_value_without_postage),0) AS total_revenue
                FROM customer_orders
                $whereSql
                ";

            $result = $conn->query($sql);
            $sumResult = $conn->query($sumSql)->fetch_assoc();
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
