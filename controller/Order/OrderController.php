<?php

namespace Order;

require_once __DIR__ . '/../../config/mainConfig.php';

class OrderController
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
        $this->domainURL  = getMainUrl();
        $this->mainDomain = mainDomain();
        $this->conn       = getDbConnection();
        $this->options    = getSelectOptions();
        $this->country    = allSaleCountry();
        $this->currentYear = currentYear();
        $this->dateNow     = dateNow();

        if (!is_login()) {
            header("Location: " . $this->domainURL . "login");
            exit;
        }
    }

    // public function newOrder()
    // {
    //     $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    //     $segments = explode('/', $currentPaths);
    //     $firstSegment = $segments[0];

    //     if (roleVerify($firstSegment, $_SESSION['user']->id) == 0) {
    //         header("Location: " . $this->domainURL . "access-denied");
    //         exit;
    //     }

    //     $domainURL = getMainUrl();
    //     $conn = getDbConnection();
    //     $dateNow = dateNow();
    //     $pageName = "Order - New";

    //     if (!empty($_GET['page']) && $_GET['page'] == "1") {
    //         if (isset($_GET["filter"]) && !empty($_GET["filter"]) && isset($_GET["qty"])) {
    //             header("Location: " . $domainURL . "new-order?filter=" . $_GET["filter"] . "&qty=" . $_GET["qty"]);
    //         } else {
    //             header("Location: " . $domainURL . "new-order");
    //         }
    //         exit;
    //     }

    //     $search = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';
    //     $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    //     $qty = isset($_GET["qty"]) && is_numeric($_GET["qty"]) ? (int)$_GET["qty"] : null;
    //     $onlySingle = isset($_GET['single']) ? true : false;  // New flag
    //     $limit = 30;
    //     $offset = ($page - 1) * $limit;

    //     // Build dynamic HAVING clauses
    //     $havingParts = [];

    //     if (!empty($search)) {
    //         $havingParts[] = "MAX(p.name) LIKE '%{$search}%'";
    //     }
    //     if (!is_null($qty) && $qty > 0) {
    //         $havingParts[] = "SUM(c.quantity) = {$qty}";
    //     }

    //     if ($onlySingle) {
    //         $havingParts[] = "COUNT(DISTINCT c.p_id) = 1"; // Single-item orders
    //     }
    //     // Otherwise, mixed-item orders by default (no single constraint)

    //     $having = '';
    //     if (!empty($havingParts)) {
    //         $having = 'HAVING ' . implode(' AND ', $havingParts);
    //     }

    //     // 1. Total Count for Pagination
    //     $countSql = "
    //     SELECT COUNT(*) AS total
    //     FROM (
    //         SELECT co.id
    //         FROM customer_orders co
    //         JOIN cart c ON c.session_id = co.session_id AND c.deleted_at IS NULL
    //         JOIN products p ON p.id = c.p_id AND p.deleted_at IS NULL
    //         WHERE co.deleted_at IS NULL AND co.status = '1'
    //         GROUP BY co.id
    //         {$having}
    //     ) AS filtered_orders
    // ";

    //     $totalRow = $conn->query($countSql)->fetch_assoc();
    //     $totalOrders = (int)$totalRow['total'];
    //     $totalPages = ceil($totalOrders / $limit);

    //     // 2. Main Query to Retrieve Orders
    //     $getOrdersSql = "
    //     SELECT
    //         co.id                AS order_id,
    //         co.session_id,
    //         co.customer_name,
    //         co.created_at        AS order_date,
    //         ANY_VALUE(p.name)    AS product_name,
    //         SUM(c.quantity)      AS total_quantity,
    //         COUNT(DISTINCT c.p_id) AS unique_items
    //     FROM customer_orders co
    //     JOIN cart c ON c.session_id = co.session_id AND c.deleted_at IS NULL
    //     JOIN products p ON p.id = c.p_id AND p.deleted_at IS NULL
    //     WHERE co.deleted_at IS NULL AND co.status = '1'
    //     GROUP BY co.id, co.session_id, co.customer_name, co.created_at
    //     {$having}
    //     ORDER BY co.created_at DESC
    //     LIMIT {$limit} OFFSET {$offset}
    // ";

    //     $result = $conn->query($getOrdersSql);
    //     require_once __DIR__ . '/../../view/Admin/orders.php';
    // }


    public function newOrder()
    {

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }


        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Order - New";


        if (!empty($_GET['page']) && $_GET['page'] == "1") {
            if (isset($_GET["filter"]) && !empty($_GET["filter"]) && isset($_GET["qty"])) {
                header("Location: " . $domainURL . "new-order?filter=" . $_GET["filter"] . "&qty=" . $_GET["qty"]);
            } else {
                header("Location: " . $domainURL . "new-order");
            }
        }

        $search = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $qty = $_GET["qty"];

        $limit = 100;
        $offset = ($page - 1) * $limit;

        $havingCondition = "";
        if (!empty($search) &&  empty($qty)) {
            $havingCondition = "HAVING COUNT(*) = 1 
                            AND MAX(p.name) LIKE '%$search%'";
        } else if (!empty($search) && $qty > 0) {
            $havingCondition = "HAVING COUNT(*) = 1 
                            AND MAX(p.name) LIKE '%$search%' 
                            AND MAX(c.quantity) = $qty";
        }

        // -------------------- 1. Total Count --------------------
        $countSql = "
        SELECT COUNT(*) AS total
        FROM (
            SELECT co.id
            FROM customer_orders AS co
            JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
            JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL
            WHERE co.deleted_at IS NULL AND co.status = '1'
            GROUP BY co.id
            $havingCondition
        ) AS filtered_orders
    ";

        $totalResult = $conn->query($countSql);
        $totalRow = $totalResult->fetch_assoc();
        $totalOrders = (int) $totalRow['total'];
        $totalPages = ceil($totalOrders / $limit);

        // -------------------- 2. Main Query --------------------
        $getOrdersSql = "
        SELECT 
            co.id AS order_id,
            co.session_id,
            co.customer_name,
            co.created_at AS order_date,
            co.awb_number,
            co.country,
            co.product_var_id,
            co.myr_value_include_postage,
            co.payment_channel,
            co.status,
            co.payment_url,
            co.ship_channel,
            co.courier_service,
            co.tracking_url,

            ANY_VALUE(c.pv_id) AS pv_id,
            ANY_VALUE(c.quantity) AS quantity,
            ANY_VALUE(p.name) AS product_name

        FROM customer_orders AS co
        JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
        JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL

        WHERE co.deleted_at IS NULL AND co.status = '1'

        GROUP BY co.id, co.session_id, co.customer_name, co.created_at, co.awb_number
        $havingCondition

        ORDER BY co.created_at DESC
        LIMIT $limit OFFSET $offset
    ";

        $result = $conn->query($getOrdersSql);
        require_once __DIR__ . '/../../view/Admin/orders.php';
    }

    // public function processOrder()
    // {
    //     // ... (initial role verification and setup as before)

    //     $domainURL = getMainUrl();
    //     $conn = getDbConnection();
    //     $dateNow = dateNow();

    //     // Sanitize input
    //     $search = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';
    //     $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    //     $qty = isset($_GET['qty']) && is_numeric($_GET['qty']) ? (int)$_GET['qty'] : null;
    //     $onlySingle = isset($_GET['single']) ? true : false; // New flag to target only single‑item orders
    //     $limit = 50;
    //     $offset = ($page - 1) * $limit;

    //     // Build HAVING condition
    //     $havingConds = [];
    //     if (!empty($search)) {
    //         $havingConds[] = "MAX(p.name) LIKE '%$search%'";
    //     }
    //     if ($qty !== null && $qty > 0) {
    //         $havingConds[] = "SUM(c.quantity) = $qty";
    //     }

    //     // Decide between single-item vs. mixed-item orders
    //     if ($onlySingle) {
    //         // Only orders with exactly one unique product
    //         $havingConds[] = "COUNT(DISTINCT c.p_id) = 1";
    //     }
    //     // Else, mixed orders would be COUNT(DISTINCT c.p_id) > 1 or absence of the flag

    //     $having = '';
    //     if (!empty($havingConds)) {
    //         $having = 'HAVING ' . implode(' AND ', $havingConds);
    //     }

    //     // 1. Total count for pagination
    //     $countSql = "
    //     SELECT COUNT(*) AS total
    //     FROM (
    //         SELECT co.id
    //         FROM customer_orders co
    //         JOIN cart c ON c.session_id = co.session_id AND c.deleted_at IS NULL
    //         JOIN products p ON p.id = c.p_id AND p.deleted_at IS NULL
    //         WHERE co.deleted_at IS NULL AND co.status = '2'
    //         GROUP BY co.id
    //         $having
    //     ) AS filtered_orders
    // ";

    //     $totalRow = $conn->query($countSql)->fetch_assoc();
    //     $totalOrders = (int)$totalRow['total'];
    //     $totalPages = ceil($totalOrders / $limit);

    //     // 2. Main query to fetch matching orders
    //     $getOrdersSql = "
    //     SELECT 
    //         co.id AS order_id,
    //         co.session_id,
    //         co.customer_name,
    //         co.created_at AS order_date,
    //         ANY_VALUE(p.name) AS product_name,
    //         ANY_VALUE(c.quantity) AS quantity,
    //         COUNT(DISTINCT c.p_id) AS unique_items
    //     FROM customer_orders co
    //     JOIN cart c ON c.session_id = co.session_id AND c.deleted_at IS NULL
    //     JOIN products p ON p.id = c.p_id AND p.deleted_at IS NULL
    //     WHERE co.deleted_at IS NULL AND co.status = '2'
    //     GROUP BY co.id, co.session_id, co.customer_name, co.created_at
    //     $having
    //     ORDER BY co.created_at DESC
    //     LIMIT $limit OFFSET $offset
    // ";

    //     $result = $conn->query($getOrdersSql);
    //     require_once __DIR__ . '/../../view/Admin/orders.php';
    // }


    public function processOrder()
    {

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            //header("Location: ".$this->domainURL."access-denied");
            require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Order - Process";

        if (!empty($_GET['page']) && $_GET['page'] == "1") {
            if (isset($_GET["filter"]) && !empty($_GET["filter"]) && isset($_GET["qty"])) {
                header("Location: " . $domainURL . "process-order?filter=" . $_GET["filter"] . "&qty=" . $_GET["qty"]);
            } else {
                header("Location: " . $domainURL . "process-order");
            }
        }



        $search = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $sort    = isset($_GET['sort']) ? $_GET['sort'] : '';

        $qty = isset($_GET['qty']) && is_numeric($_GET['qty']) ? (int)$_GET['qty'] : null;

        $limit = 100;
        $offset = ($page - 1) * $limit;

        $havingCondition = "";
        if (!empty($search) &&  empty($qty)) {
            $havingCondition = "HAVING COUNT(*) = 1 
                            AND MAX(p.name) LIKE '%$search%'";
        } else if (!empty($search) && $qty > 0) {
            $havingCondition = "HAVING COUNT(*) = 1 
                            AND MAX(p.name) LIKE '%$search%' 
                            AND MAX(c.quantity) = $qty";
        }

        // Sort logic
        $orderBy = "ORDER BY co.created_at DESC";
        if ($sort === "asc") {
            $orderBy = "ORDER BY MAX(c.quantity) ASC";
        } elseif ($sort === "desc") {
            $orderBy = "ORDER BY MAX(c.quantity) DESC";
        }

        // -------------------- 1. Total Count --------------------
        $countSql = "
            SELECT COUNT(*) AS total
            FROM (
                SELECT co.id
                FROM customer_orders AS co
                JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
                JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL
                WHERE co.deleted_at IS NULL AND co.status = '2'
                GROUP BY co.id
                $havingCondition
            ) AS filtered_orders
        ";

        $totalResult = $conn->query($countSql);
        $totalRow = $totalResult->fetch_assoc();
        $totalOrders = (int) $totalRow['total'];
        $totalPages = ceil($totalOrders / $limit);

        // -------------------- 2. Main Query --------------------
        $getOrdersSql = "
        SELECT 
            co.id AS order_id,
            co.session_id,
            co.customer_name,
            co.created_at AS order_date,
            co.awb_number,
            co.country,
            co.product_var_id,
            co.myr_value_include_postage,
            co.payment_channel,
            co.status,
            co.payment_url,
            co.ship_channel,
            co.courier_service,
            co.tracking_url,

            ANY_VALUE(c.pv_id) AS pv_id,
            ANY_VALUE(c.quantity) AS quantity,
            ANY_VALUE(p.name) AS product_name,
            ANY_VALUE(ap.id) AS awb_printed_id,
            ANY_VALUE(ap.printed_by) AS printed_by,
            ANY_VALUE(ap.created_at) AS awb_printed_date,
            CASE 
                WHEN ANY_VALUE(ap.id) IS NOT NULL THEN '<span class=\"btn btn-danger\">PRINTED AWB</span>'
                ELSE '<span class=\"btn btn-info\">UNPRINTED AWB</span>'
            END AS printed_status

        FROM customer_orders AS co
        JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
        JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL
        LEFT JOIN awb_printed AS ap 
            ON ap.deleted_at IS NULL
            AND ap.order_id LIKE CONCAT('%[', co.id, ']%')

        WHERE co.deleted_at IS NULL AND co.status = '2'

        GROUP BY co.id, co.session_id, co.customer_name, co.created_at, co.awb_number
        $havingCondition
        $orderBy
        LIMIT $limit OFFSET $offset
    ";

        $result = $conn->query($getOrdersSql);
        require_once __DIR__ . '/../../view/Admin/orders.php';
    }

    public function inDeliveryOrder()
    {

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            //header("Location: ".$this->domainURL."access-denied");
            require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Order - In Delivery";

        if (!empty($_GET['page']) && $_GET['page'] == "1") {
            if (isset($_GET["filter"]) && !empty($_GET["filter"])) {
                header("Location: " . $domainURL . "indelivery-order?filter=" . $_GET["filter"]);
            } else {
                header("Location: " . $domainURL . "indelivery-order");
            }
        }

        $search = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $sort    = isset($_GET['sort']) ? $_GET['sort'] : '';

        $printed = isset($_GET['printed']) && $_GET['printed'] !== '' ? (int)$_GET['printed'] : null;

        $qty = isset($_GET['qty']) && is_numeric($_GET['qty']) ? (int)$_GET['qty'] : null;

        $limit = 30;
        $offset = ($page - 1) * $limit;

        $havingCondition = "";
        if (!empty($search) &&  empty($qty)) {
            $havingCondition = "HAVING COUNT(*) = 1 
                        AND MAX(p.name) LIKE '%$search%'";
        } else if (!empty($search) && $qty > 0) {
            $havingCondition = "HAVING COUNT(*) = 1 
                        AND MAX(p.name) LIKE '%$search%' 
                        AND MAX(c.quantity) = $qty";
        }

        // if (!empty($search) && empty($qty) && $printed === null) {
        //     $havingCondition = "HAVING COUNT(*) = 1 
        //                 AND MAX(p.name) LIKE '%$search%'";
        // }

        // // 2) Search + Qty
        // else if (!empty($search) && $qty > 0 && $printed === null) {
        //     $havingCondition = "HAVING COUNT(*) = 1 
        //                 AND MAX(p.name) LIKE '%$search%' 
        //                 AND MAX(c.quantity) = $qty";
        // }

        // // 3) Search + Printed
        // else if (!empty($search) && empty($qty) && $printed !== null) {
        //     $havingCondition = "HAVING COUNT(*) = 1 
        //                 AND MAX(p.name) LIKE '%$search%' 
        //                 AND MAX(co.printed_awb) = '$printed'";
        // }

        // // 4) Search + Qty + Printed
        // else if (!empty($search) && $qty > 0 && $printed !== null) {
        //     $havingCondition = "HAVING COUNT(*) = 1 
        //                 AND MAX(p.name) LIKE '%$search%' 
        //                 AND MAX(c.quantity) = $qty
        //                 AND MAX(co.printed_awb) = '$printed'";
        // }

        // // 5) Qty only
        // else if (empty($search) && $qty > 0 && $printed === null) {
        //     $havingCondition = "HAVING COUNT(*) = 1 
        //                 AND MAX(c.quantity) = $qty";
        // }

        // // 6) Printed only
        // else if (empty($search) && empty($qty) && $printed !== null) {
        //     $havingCondition = "HAVING COUNT(*) = 1 
        //                 AND MAX(co.printed_awb) = '$printed'";
        // }

        // // 7) Qty + Printed
        // else if (empty($search) && $qty > 0 && $printed !== null) {
        //     $havingCondition = "HAVING COUNT(*) = 1 
        //                 AND MAX(c.quantity) = $qty
        //                 AND MAX(co.printed_awb) = '$printed'";
        // }

        // Sort logic
        $orderBy = "ORDER BY co.created_at DESC";
        if ($sort === "asc") {
            $orderBy = "ORDER BY MAX(c.quantity) ASC";
        } elseif ($sort === "desc") {
            $orderBy = "ORDER BY MAX(c.quantity) DESC";
        }

        // -------------------- 1. Total Count --------------------
        $countSql = "
    SELECT COUNT(*) AS total
    FROM (
        SELECT co.id
        FROM customer_orders AS co
        JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
        JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL
        WHERE co.deleted_at IS NULL AND co.status = '3'
        GROUP BY co.id
        $havingCondition
    ) AS filtered_orders
";

        $totalResult = $conn->query($countSql);
        $totalRow = $totalResult->fetch_assoc();
        $totalOrders = (int) $totalRow['total'];
        $totalPages = ceil($totalOrders / $limit);

        // -------------------- 2. Main Query --------------------
        $getOrdersSql = "
    SELECT 
        co.id AS order_id,
        co.session_id,
        co.customer_name,
        co.created_at AS order_date,
        co.awb_number,
        co.country,
        co.product_var_id,
        co.myr_value_include_postage,
        co.payment_channel,
        co.status,
        co.payment_url,
        co.ship_channel,
        co.courier_service,
        co.tracking_url,

        ANY_VALUE(c.pv_id) AS pv_id,
        ANY_VALUE(c.quantity) AS quantity,
        ANY_VALUE(p.name) AS product_name

    FROM customer_orders AS co
    JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
    JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL

    WHERE co.deleted_at IS NULL AND co.status = '3'

    GROUP BY co.id, co.session_id, co.customer_name, co.created_at, co.awb_number
    $havingCondition
    $orderBy
    LIMIT $limit OFFSET $offset
";

        $result = $conn->query($getOrdersSql);
        require_once __DIR__ . '/../../view/Admin/orders.php';
    }

    public function completeOrder()
    {

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            //header("Location: ".$this->domainURL."access-denied");
            require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }



        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Order - Complete";

        if (!empty($_GET['page']) && $_GET['page'] == "1") {
            if (isset($_GET["filter"]) && !empty($_GET["filter"])) {
                header("Location: " . $domainURL . "completed-order?filter=" . $_GET["filter"]);
            } else {
                header("Location: " . $domainURL . "completed-order");
            }
        }

        $search = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

        $qty = $_GET["qty"];

        $limit = 30;
        $offset = ($page - 1) * $limit;

        $havingCondition = "";
        if (!empty($search) &&  empty($qty)) {
            $havingCondition = "HAVING COUNT(*) = 1 
                        AND MAX(p.name) LIKE '%$search%'";
        } else if (!empty($search) && $qty > 0) {
            $havingCondition = "HAVING COUNT(*) = 1 
                        AND MAX(p.name) LIKE '%$search%' 
                        AND MAX(c.quantity) = $qty";
        }

        // -------------------- 1. Total Count --------------------
        $countSql = "
    SELECT COUNT(*) AS total
    FROM (
        SELECT co.id
        FROM customer_orders AS co
        JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
        JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL
        WHERE co.deleted_at IS NULL AND co.status = '4'
        GROUP BY co.id
        $havingCondition
    ) AS filtered_orders
";

        $totalResult = $conn->query($countSql);
        $totalRow = $totalResult->fetch_assoc();
        $totalOrders = (int) $totalRow['total'];
        $totalPages = ceil($totalOrders / $limit);

        // -------------------- 2. Main Query --------------------
        $getOrdersSql = "
    SELECT 
        co.id AS order_id,
        co.session_id,
        co.customer_name,
        co.created_at AS order_date,
        co.awb_number,
        co.country,
        co.product_var_id,
        co.myr_value_include_postage,
        co.payment_channel,
        co.status,
        co.payment_url,
        co.ship_channel,
        co.courier_service,
        co.tracking_url,

        ANY_VALUE(c.pv_id) AS pv_id,
        ANY_VALUE(c.quantity) AS quantity,
        ANY_VALUE(p.name) AS product_name

    FROM customer_orders AS co
    JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
    JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL

    WHERE co.deleted_at IS NULL AND co.status = '4'

    GROUP BY co.id, co.session_id, co.customer_name, co.created_at, co.awb_number
    $havingCondition

    ORDER BY co.created_at DESC
    LIMIT $limit OFFSET $offset
";

        $result = $conn->query($getOrdersSql);
        require_once __DIR__ . '/../../view/Admin/orders.php';
    }

    public function returnOrder()
    {

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            //header("Location: ".$this->domainURL."access-denied");
            require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }



        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Order - Return";

        if (!empty($_GET['page']) && $_GET['page'] == "1") {
            if (isset($_GET["filter"]) && !empty($_GET["filter"])) {
                header("Location: " . $domainURL . "returned-order?filter=" . $_GET["filter"]);
            } else {
                header("Location: " . $domainURL . "returned-order");
            }
        }

        $search = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

        $qty = $_GET["qty"];

        $limit = 30;
        $offset = ($page - 1) * $limit;

        $havingCondition = "";
        if (!empty($search) &&  empty($qty)) {
            $havingCondition = "HAVING COUNT(*) = 1 
                        AND MAX(p.name) LIKE '%$search%'";
        } else if (!empty($search) && $qty > 0) {
            $havingCondition = "HAVING COUNT(*) = 1 
                        AND MAX(p.name) LIKE '%$search%' 
                        AND MAX(c.quantity) = $qty";
        }

        // -------------------- 1. Total Count --------------------
        $countSql = "
    SELECT COUNT(*) AS total
    FROM (
        SELECT co.id
        FROM customer_orders AS co
        JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
        JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL
        WHERE co.deleted_at IS NULL AND co.status = '5'
        GROUP BY co.id
        $havingCondition
    ) AS filtered_orders
";

        $totalResult = $conn->query($countSql);
        $totalRow = $totalResult->fetch_assoc();
        $totalOrders = (int) $totalRow['total'];
        $totalPages = ceil($totalOrders / $limit);

        // -------------------- 2. Main Query --------------------
        $getOrdersSql = "
    SELECT 
        co.id AS order_id,
        co.session_id,
        co.customer_name,
        co.created_at AS order_date,
        co.awb_number,
        co.country,
        co.product_var_id,
        co.myr_value_include_postage,
        co.payment_channel,
        co.status,
        co.payment_url,
        co.ship_channel,
        co.courier_service,
        co.tracking_url,

        ANY_VALUE(c.pv_id) AS pv_id,
        ANY_VALUE(c.quantity) AS quantity,
        ANY_VALUE(p.name) AS product_name

    FROM customer_orders AS co
    JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
    JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL

    WHERE co.deleted_at IS NULL AND co.status = '5'

    GROUP BY co.id, co.session_id, co.customer_name, co.created_at, co.awb_number
    $havingCondition

    ORDER BY co.created_at DESC
    LIMIT $limit OFFSET $offset
";

        $result = $conn->query($getOrdersSql);
        require_once __DIR__ . '/../../view/Admin/orders.php';
    }



    public function cancelOrder()
    {

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            //header("Location: ".$this->domainURL."access-denied");
            require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }


        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Order - Cancel";

        if (!empty($_GET['page']) && $_GET['page'] == "1") {
            if (isset($_GET["filter"]) && !empty($_GET["filter"])) {
                header("Location: " . $domainURL . "cancelled-order?filter=" . $_GET["filter"]);
            } else {
                header("Location: " . $domainURL . "cancelled-order");
            }
        }



        $search = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

        $qty = $_GET["qty"];

        $limit = 30;
        $offset = ($page - 1) * $limit;

        $havingCondition = "";
        if (!empty($search) &&  empty($qty)) {
            $havingCondition = "HAVING COUNT(*) = 1 
                        AND MAX(p.name) LIKE '%$search%'";
        } else if (!empty($search) && $qty > 0) {
            $havingCondition = "HAVING COUNT(*) = 1 
                        AND MAX(p.name) LIKE '%$search%' 
                        AND MAX(c.quantity) = $qty";
        }

        // -------------------- 1. Total Count --------------------
        $countSql = "
    SELECT COUNT(*) AS total
    FROM (
        SELECT co.id
        FROM customer_orders AS co
        JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
        JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL
        WHERE co.deleted_at IS NULL AND co.status = '6'
        GROUP BY co.id
        $havingCondition
    ) AS filtered_orders
";

        $totalResult = $conn->query($countSql);
        $totalRow = $totalResult->fetch_assoc();
        $totalOrders = (int) $totalRow['total'];
        $totalPages = ceil($totalOrders / $limit);

        // -------------------- 2. Main Query --------------------
        $getOrdersSql = "
    SELECT 
        co.id AS order_id,
        co.session_id,
        co.customer_name,
        co.created_at AS order_date,
        co.awb_number,
        co.country,
        co.product_var_id,
        co.myr_value_include_postage,
        co.payment_channel,
        co.status,
        co.payment_url,
        co.ship_channel,
        co.courier_service,
        co.tracking_url,

        ANY_VALUE(c.pv_id) AS pv_id,
        ANY_VALUE(c.quantity) AS quantity,
        ANY_VALUE(p.name) AS product_name

    FROM customer_orders AS co
    JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
    JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL

    WHERE co.deleted_at IS NULL AND co.status = '6'

    GROUP BY co.id, co.session_id, co.customer_name, co.created_at, co.awb_number
    $havingCondition

    ORDER BY co.created_at DESC
    LIMIT $limit OFFSET $offset
";

        $result = $conn->query($getOrdersSql);
        require_once __DIR__ . '/../../view/Admin/orders.php';
    }

    public function submitCourier()
    {

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();

        if (isset($_POST["dhl"])) {
            $orderID = $_POST["orderID"];

            $createShipping = dhlCreateShipping($orderID);

            if (!empty($createShipping["deliveryConfirmationNo"])) {
                $deliveryConfirmationNo = $createShipping["deliveryConfirmationNo"];
                $tracking = "https://www.dhl.com/my-en/home/tracking.html?tracking-id=" . $deliveryConfirmationNo;


                $sql = "UPDATE customer_orders SET `status`='2', courier_service='DHL ECOMMERCE', awb_number='$deliveryConfirmationNo', tracking_url='$tracking', updated_at='$dateNow' WHERE id='$orderID'";
                $query = $conn->query($sql);

                $_SESSION['upload_success'] = "Successfully submit order to DHL";
                header("Location: " . $domainURL . "new-order");
            } else {

                $formatted = str_pad($orderID, 8, '0', STR_PAD_LEFT);
                $_SESSION['upload_error'] = "Order #" . $formatted . " " . $createShipping["message"];
                header("Location: " . $domainURL . "new-order");
            }
        } else if (isset($_POST["jnt"])) {
            $orderID = $_POST["orderID"];

            $createJTShipping = createJTShipping($orderID);

            if (empty($createJTShipping["false"])) {
                $_SESSION['upload_success'] = "All order successfull send all order to J&T.";
                $addActivity = activity($_SESSION['user']->id, "Successfully send All order successfull send all order to J&T.", "customer_orders|$orderID", "submit_awb_jnt");
            } else {
                //$failed = $createJTShipping["all"] - $createJTShipping["success"];
                $_SESSION['upload_error'] = $createJTShipping["failed"] . "/" . $createJTShipping["all"] . " of order failed to send to J&T.<br>" . $createJTShipping["false"];
                $addActivity = activity($_SESSION['user']->id, "Failed send to J&T", "customer_orders|$orderID", "submit_awb_jnt");
            }


            header("Location: " . $domainURL . "new-order");
        } else if (isset($_POST["ninja"])) {

            $orderID = $_POST["orderID"];

            $_SESSION['upload_success'] = "Successfully submit order to NinjaVan";
            header("Location: " . $domainURL . "new-order");
        }
    }

    public function printAWB()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();

        if (isset($_POST["print-awb"]) || isset($_POST["printAWB"])) {
            $orderID = $_POST["orderID"];

            // $sql = "INSERT INTO `dhl_bulk_print`(`id`, `order_id`, `status`, `deleted_at`, `updated_at`) VALUES (NULL,'$orderID','0','$dateNow','$dateNow')";

            // $query = $conn->query("$sql");

            // $lastInsertID = $conn->insert_id;

            // header("Location: " . $domainURL . "awb-dhl.php?id=" . $lastInsertID);



            $_SESSION['upload_success'] = "Successfully created pdf AWB J&T.";
            $addActivity = activity($_SESSION['user']->id, "Successfully created pdf AWB J&T.", "customer_orders|$orderID", "print_awb");


?>
            <script>
                // Open new tab
                const url = "<?= $domainURL; ?>awb-jt.php?id=<?= $orderID ?>";
                window.open(url, "_blank");

                // Redirect current page back to process-order (after short delay to ensure popup isn't blocked)
                setTimeout(() => {
                    window.location.href = "<?= $domainURL; ?>process-order";
                }, 1000); // 0.5 seconds delay to allow popup to register
            </script>
        <?php
            exit;
            //header("Location: " . $domainURL . "process-order");
        }

        if (isset($_POST["move-indelivery"]) || isset($_POST["move-indelivery"])) {
            $orderID = $_POST["orderID"];

            // $sql = "INSERT INTO `dhl_bulk_print`(`id`, `order_id`, `status`, `deleted_at`, `updated_at`) VALUES (NULL,'$orderID','0','$dateNow','$dateNow')";

            // $query = $conn->query("$sql");

            // $lastInsertID = $conn->insert_id;

            // header("Location: " . $domainURL . "awb-dhl.php?id=" . $lastInsertID);
            $orderIDs = explode(",", $_POST["orderID"]);
            $orderIDs = array_map('intval', $orderIDs);

            $idList = implode(",", $orderIDs);
            $sql = "UPDATE customer_orders SET `status` = '3', updated_at='$dateNow' WHERE id IN ($idList)";
            $conn->query($sql);


            $_SESSION['upload_success'] = "Successfully move order to In Delivery.";
            $addActivity = activity($_SESSION['user']->id, "Successfully move order to In Delivery.", "customer_orders|$orderID", "move_to_indelivery");



            header("Location: " . $domainURL . "process-order");
            exit;
            //header("Location: " . $domainURL . "process-order");
        }
    }

    public function detailsBuyer()
    {

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            //header("Location: ".$this->domainURL."access-denied");
            require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }


        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();

        if (!empty($_GET["order_id"])) {
            $id = $_GET["order_id"];
            $addActivity = activity($_SESSION['user']->id, "View details customer", "customer_orders|$id", "view_customers");

            $sql = "
                SELECT `id`, `session_id`, `order_to`, `product_var_id`, `total_qty`, `total_price`, `postage_cost`,
                    `currency_sign`, `country_id`, `country`, `state`, `city`, `postcode`, `address_2`, `address_1`,
                    `customer_name`, `customer_phone`, `customer_email`, `status`, `payment_channel`, `payment_code`,
                    `payment_url`, `ship_channel`, `courier_service`, `awb_number`, `tracking_url`, `created_at`, `updated_at`,
                    `deleted_at`, `remark_comment`, `tracking_milestone`, `to_myr_rate`, `myr_value_include_postage`,
                    `myr_value_without_postage`
                FROM `customer_orders`
                WHERE `id`='$id' AND `deleted_at` IS NULL
                ";
            $result = $conn->query($sql);
            $row = $result->fetch_array();
            $session_id = $row["session_id"];
        ?>
            <h5>Order Details for Order #
                <?= str_pad($_GET["order_id"], 8, "0", STR_PAD_LEFT); ?>
            </h5>
            <h6 class="headline-modal">Customer Details</h6>
            <p class="p_details">
                Name: <span class="mention" id="p_order_name">
                    <?= $row["customer_name"] ?>
                </span>
            </p>
            <div class="row">
                <div class="col-md-6">
                    <p class="p_details">
                        Email: <span class="mention" id="p_order_name">
                            <?= $row["customer_email"] ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="p_details">
                        Phone: <span class="mention" id="p_order_name">
                            <?= $row["customer_phone"] ?>
                        </span>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p class="p_details">
                        Address Line 1: <span class="mention" id="p_order_name">
                            <?= $row["address_1"] ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="p_details">
                        Address Line 2: <span class="mention" id="p_order_name">
                            <?= $row["address_2"] ?>
                        </span>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p class="p_details">
                        City: <span class="mention" id="p_order_name">
                            <?= $row["city"] ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="p_details">
                        Postcode: <span class="mention" id="p_order_name">
                            <?= $row["postcode"] ?>
                        </span>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p class="p_details">
                        State: <span class="mention" id="p_order_name">
                            <?= $row["state"] ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="p_details">
                        Country: <span class="mention" id="p_order_name">
                            <?= $row["country"] ?>
                        </span>
                    </p>
                </div>
            </div>
            <h6 class="headline-modal">Status</h6>
            <p class="p_details">
                <span id="p_order_name">
                    <?= $row["ship_channel"] ?>
                </span>
                <br>
                <span class="mention" id="p_order_name">
                    <?= $row["payment_channel"] ?>
                </span>
            </p>
            <h6 class="headline-modal">Logistic Information</h6>
            <div id="l_info">
                <table>
                    <?php
                    $string = $row["product_var_id"];

                    // Step 1: explode by comma
                    $parts = explode(",", $string);

                    // Step 2: loop and clean brackets
                    foreach ($parts as $varID) {
                        // Remove [ and ] using str_replace
                        $id = str_replace(['[', ']'], '', $varID);
                        //echo $id . "<br>";

                        $sql1 = "SELECT * FROM product_variants WHERE id='$id'";
                        $result1 = $conn->query($sql1);

                        while ($row1 = $result1->fetch_assoc()) {

                            $product_id = $row1["product_id"];
                            $pv_id = $row1["id"];

                            $sql11 = "SELECT * FROM `products` WHERE `id`='$product_id'";
                            $result11 = $conn->query($sql11);
                            $row11 = $result11->fetch_assoc();

                            $sql111 = "SELECT * FROM `product_image` WHERE `product_id`='$product_id' ORDER BY id ASC LIMIT 1";
                            $result111 = $conn->query($sql111);
                            $row111 = $result111->fetch_assoc();

                            //cart
                            $sql1111 = "SELECT * FROM cart WHERE `session_id`='$session_id' AND p_id='$product_id' AND pv_id='$id' AND deleted_at IS NULL";
                            $result1111 = $conn->query($sql1111);
                            $row1111 = $result1111->fetch_assoc();

                    ?>
                            <tr>
                                <td>
                                    <div class="text-wrap-image">
                                        <img src="<?= $domainURL ?>assets/images/products/<?= $row111['image'] ?>" style="width:60px;"
                                            alt="Example" class="wrap-img">
                                        <p>
                                            (
                                            <?= $row1["sku"] ?>)
                                            <?= $row11["name"] ?>
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    x
                                    <?= $row1111['quantity'] ?>
                                </td>
                            </tr>
                        <?php
                        }

                        ?>

                    <?php
                    }
                    ?>

                </table>
            </div>
            <h6 class="headline-modal">Buyer Payment</h6>

            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-6">
                    <table style="width:100%;">
                        <tr>
                            <td>
                                Payment Channel
                            </td>
                            <td style="text-align:right;">
                                <p class="p_details">
                                    <span class="mention" id="p_order_name">
                                        <?= $row["payment_channel"] ?>
                                    </span>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Store Subtotal
                            </td>
                            <td style="text-align:right;">
                                <p class="p_details">
                                    <span class="mention" id="p_order_name">RM
                                        <?= number_format($row["myr_value_without_postage"], 2) ?>
                                    </span>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Shipping Fee
                            </td>
                            <td style="text-align:right;">
                                <p class="p_details">
                                    <span class="mention" id="p_order_name">RM
                                        <?= number_format($row["postage_cost"], 2) ?>
                                    </span>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                GST
                            </td>
                            <td style="text-align:right;">
                                <p class="p_details">
                                    <span class="mention" id="p_order_name">( include )</span>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Total Buyer Payment
                            </td>
                            <td style="text-align:right;">
                                <p class="p_details">
                                    <span class="mention" id="p_order_name">RM
                                        <?= number_format($row["myr_value_include_postage"], 2) ?>
                                    </span>
                                </p>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
        <?php
        }
    }

    public function updateBuyer()
    {

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }
        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            //header("Location: ".$this->domainURL."access-denied");
            require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }


        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();

        if (!empty($_GET["order_id"])) {
            $id = $_GET["order_id"];
            $sql = "
                SELECT `id`, `session_id`, `order_to`, `product_var_id`, `total_qty`, `total_price`, `postage_cost`,
                    `currency_sign`, `country_id`, `country`, `state`, `city`, `postcode`, `address_2`, `address_1`,
                    `customer_name`, `customer_name_last`, `customer_phone`, `customer_email`, `status`, `payment_channel`, `payment_code`,
                    `payment_url`, `ship_channel`, `courier_service`, `awb_number`, `tracking_url`, `created_at`, `updated_at`,
                    `deleted_at`, `remark_comment`, `tracking_milestone`, `to_myr_rate`, `myr_value_include_postage`,
                    `myr_value_without_postage`
                FROM `customer_orders`
                WHERE `id`='$id' AND `deleted_at` IS NULL
                ";
            $result = $conn->query($sql);
            $row = $result->fetch_array();
            $session_id = $row["session_id"];
        ?>
            <h5>Order Details for Order #
                <?= str_pad($_GET["order_id"], 8, "0", STR_PAD_LEFT); ?>
            </h5>

            <style>
                .swal2-container {
                    z-index: 20000 !important;
                }
            </style>

            <form id="customerDetailsForm">
                <input type="hidden" id="orderNo" value="<?= $_GET["order_id"] ?>">

                <h6 class="headline-modal">Customer Details</h6>



                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="c_name" class="form-label">First Name *</label>
                        <input type="text" class="form-control" id="c_name" name="name" required
                            value="<?= htmlspecialchars($row["customer_name"]) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="c_name" class="form-label">Last Name *</label>
                        <input type="text" class="form-control" id="c_name_last" name="name_last" required
                            value="<?= htmlspecialchars($row["customer_name_last"]) ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="c_email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="c_email" name="email" required
                            value="<?= htmlspecialchars($row["customer_email"]) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="c_phone" class="form-label">Phone *</label>
                        <input type="text" class="form-control" id="c_phone" name="phone" required
                            value="<?= htmlspecialchars($row["customer_phone"]) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="c_address1" class="form-label">Address Line 1 *</label>
                    <input type="text" class="form-control" id="c_address1" name="address1" required
                        value="<?= htmlspecialchars($row["address_1"]) ?>">
                </div>

                <div class="mb-3">
                    <label for="c_address2" class="form-label">Address Line 2 <small class="text-muted">(optional)</small></label>
                    <input type="text" class="form-control" id="c_address2" name="address2"
                        value="<?= htmlspecialchars($row["address_2"]) ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="c_city" class="form-label">City *</label>
                        <input type="text" class="form-control" id="c_city" name="city" required
                            value="<?= htmlspecialchars($row["city"]) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="c_postcode" class="form-label">Postcode *</label>
                        <input type="text" class="form-control" id="c_postcode" name="postcode" required
                            value="<?= htmlspecialchars($row["postcode"]) ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="c_state" class="form-label">State *</label>
                        <input type="text" class="form-control" id="c_state" name="state" required
                            value="<?= htmlspecialchars($row["state"]) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Country</label>
                        <div class="form-control" readonly>
                            <?= htmlspecialchars($row["country"]) ?>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-primary" id="updateCustomerBtn">Update Customer Details</button>
                </div>
            </form>

            <script>
                $(document).ready(function() {
                    $("#updateCustomerBtn").click(function() {
                        const order_id = $("#orderNo").val();

                        // Collect form values
                        const data = {
                            order_id: $("#orderNo").val(),
                            name: $("#c_name").val().trim(),
                            name_last: $("#c_name_last").val().trim(),
                            email: $("#c_email").val().trim(),
                            phone: $("#c_phone").val().trim(),
                            address1: $("#c_address1").val().trim(),
                            address2: $("#c_address2").val().trim(), // Optional field
                            city: $("#c_city").val().trim(),
                            postcode: $("#c_postcode").val().trim(),
                            state: $("#c_state").val().trim()
                        };

                        // Basic validation
                        if (
                            !data.name || !data.email || !data.phone ||
                            !data.address1 || !data.city || !data.postcode || !data.state
                        ) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Missing Fields',
                                text: 'Please fill in all required fields mark by *.'
                            });
                            return;
                        }

                        // Optional: Show loading
                        Swal.fire({
                            title: 'Updating...',
                            text: 'Please wait while we update the customer details.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "<?= $domainURL ?>update-customer",
                            //url: "https://webhook.site/e7241f82-e5f1-4499-a10f-8de310fa433a",
                            type: "POST",
                            data: data,
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Customer details updated successfully. Order #' + data.order_id
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Update Failed',
                                    text: 'Something went wrong. Please try again later.'
                                });
                            }
                        });
                    });
                });
            </script>


<?php
        }
    }

    public function statusOrder($orderid, $current, $next)
    {

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }



        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();

        $getOrder = $conn->query("SELECT * FROM customer_orders WHERE id='$orderid'");
        $getOrders = $getOrder->fetch_array();
        $sessionID = $getOrders["session_id"];

        $sql = "UPDATE customer_orders SET `status`='$next', `updated_at`='$dateNow' WHERE id='$orderid'";
        $query = $conn->query($sql);

        if ($current == "1") {
            $pg = "new-order";
        } else if ($current == "2") {
            $pg = "process-order";
        } else if ($current == "3") {
            $pg = "indelivery-order";
        }

        if ($next == "3") {
            $mes = "Successfully set order #" . $orderid . " to In Delivery";
        } else if ($next == "4") {
            $mes = "Successfully set order #" . $orderid . " to Completed";
        } else if ($next == "5") {
            $updateCart = $conn->query("UPDATE cart SET updated_at='$dateNow', `status`='2' WHERE session_id='$sessionID' AND deleted_at IS NULL");
            $mes = "Successfully set order #" . $orderid . " to Return to Sender";
        } else if ($next == "6") {
            $updateCart = $conn->query("UPDATE cart SET updated_at='$dateNow', `status`='3' WHERE session_id='$sessionID' AND deleted_at IS NULL");
            $mes = "Successfully set order #" . $orderid . " to Cancelled";
        }

        $addActivity = activity($_SESSION['user']->id, $mes, "customer_orders|$orderid", "order_activity");

        $_SESSION['upload_success'] = $mes;
        header("Location: " . $domainURL . $pg);
    }

    public function moveToProcessing($orderid)
    {

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();

        $sql = "UPDATE customer_orders SET `status`='2', `updated_at`='$dateNow' WHERE id='$orderid'";
        $query = $conn->query($sql);

        $previous = $_SERVER['HTTP_REFERER'] ?? '/';

        if (empty($previous)) {
            $previous = '/';
        }

        header("Location: $previous");
        exit;
    }

    public function updateCustomer()
    {
        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        // if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
        //     //header("Location: ".$this->domainURL."access-denied");
        //     require_once __DIR__ . '/../../view/Admin/access-denied.php';
        //     exit;
        // }



        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();

        $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
        $customer_name = mysqli_real_escape_string($conn, $_POST['name']);
        $customer_name_last = mysqli_real_escape_string($conn, $_POST['name_last']);
        $customer_email = mysqli_real_escape_string($conn, $_POST['email']);
        $customer_phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address_1 = mysqli_real_escape_string($conn, $_POST['address1']);
        $address_2 = mysqli_real_escape_string($conn, $_POST['address2']);
        $city = mysqli_real_escape_string($conn, $_POST['city']);
        $postcode = mysqli_real_escape_string($conn, $_POST['postcode']);
        $state = mysqli_real_escape_string($conn, $_POST['state']);

        $validatePhone = validatePhoneNumber($customer_phone);
        $valPhone = $validatePhone['international'];

        // Build update SQL query
        $sql = "
                UPDATE customer_orders SET 
                    state = '$state',
                    city = '$city',
                    postcode = '$postcode',
                    address_2 = '$address_2',
                    address_1 = '$address_1',
                    customer_name = '$customer_name',
                    customer_name_last = '$customer_name_last',
                    customer_phone = '$customer_phone',
                    customer_email = '$customer_email',
                    updated_at = '$dateNow'
                WHERE id = '$order_id'
            ";

        // Execute the query
        if (mysqli_query($conn, $sql)) {
            $addActivity = activity($_SESSION['user']->id, "Successfull update details customer", "customer_orders|$order_id", "update_customer_details");
            echo "success";
        } else {
            http_response_code(500);
            echo "error: " . mysqli_error($conn);
        }
    }

    public function database()
    {
        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            //require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];


        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            //header("Location: ".$this->domainURL."access-denied");
            require_once __DIR__ . '/../../view/Admin/access-denied.php';
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Order Database";

        require_once __DIR__ . '/../../view/Admin/order-databse.php';
    }

    public function searchOrder()
    {
        $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segmentss = explode('/', $currentPaths);
        $firstSegments = $segmentss[0];

        if (roleVerify($firstSegments, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            exit;
        }

        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Search Order";

        $search = isset($_GET['search']) ? trim($conn->real_escape_string($_GET['search'])) : '';
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $limit = 30;
        $offset = ($page - 1) * $limit;

        // Build WHERE conditions
        $where = "WHERE co.deleted_at IS NULL ";
        if ($search !== '') {
            // If search is numeric, also check order ID exact match, else LIKE searches on text fields
            $searchEscaped = $search;
            $where .= " AND (
            co.id = '$searchEscaped'
            OR co.customer_name LIKE '%$searchEscaped%'
            OR co.customer_phone LIKE '%$searchEscaped%'
            OR co.customer_email LIKE '%$searchEscaped%'
        )";
        }

        // -------------------- 1. Total Count --------------------
        $countSql = "
        SELECT COUNT(DISTINCT co.id) AS total
        FROM customer_orders AS co
        $where
    ";

        $totalResult = $conn->query($countSql);
        $totalRow = $totalResult->fetch_assoc();
        $totalOrders = (int) $totalRow['total'];
        $totalPages = ceil($totalOrders / $limit);

        // -------------------- 2. Main Query --------------------
        $getOrdersSql = "
        SELECT 
            co.id AS order_id,
            co.session_id,
            co.customer_name,
            co.customer_phone,
            co.customer_email,
            co.created_at AS order_date,
            co.awb_number,
            co.country,
            co.product_var_id,
            co.myr_value_include_postage,
            co.payment_channel,
            co.status,
            co.ship_channel,
            co.courier_service,
            co.tracking_url,

            ANY_VALUE(c.pv_id) AS pv_id,
            ANY_VALUE(c.quantity) AS quantity,
            ANY_VALUE(p.name) AS product_name

        FROM customer_orders AS co
        JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
        JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL

        $where

        GROUP BY co.id, co.session_id, co.customer_name, co.customer_phone, co.customer_email, co.created_at, co.awb_number, co.country,
                 co.product_var_id, co.myr_value_include_postage, co.payment_channel, co.status, co.ship_channel, co.courier_service, co.tracking_url

        ORDER BY co.created_at DESC
        LIMIT $limit OFFSET $offset
    ";

        $result = $conn->query($getOrdersSql);

        require_once __DIR__ . '/../../view/Admin/orders-search.php';
    }
}
