<?php

namespace API;

require_once __DIR__ . '/../../config/mainConfig.php';


class mainController
{
    private function checkRateLimit($maxAttempts = 60, $window = 60)
    {
        $ip = getUserIP();
        if (!rate_limit("ratelimit:api:{$ip}", $maxAttempts, $window)) {
            http_response_code(429);
            echo json_encode(["message" => "Too many requests. Please try again later."]);
            exit;
        }
    }

    public function login()
    {
        $this->checkRateLimit(10, 60);
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *"); // Only for development. Use proper CORS settings in production.
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Headers: Content-Type");

        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();

        if ($conn->connect_error) {
            http_response_code(500);
            echo json_encode(["message" => "Database connection failed"]);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        $email = $data["email"] ?? '';
        $password = $data["password"] ?? '';

        if (!$email || !$password) {
            http_response_code(400);
            echo json_encode(["message" => "Email and password are required"]);
            exit;
        }

        $hashPass = hash("sha256", $password);

        $validate = $conn->query("SELECT * FROM `member_hq` WHERE `email`='$email' AND `password`='$hashPass'");

        if ($validate->num_rows === 0) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid email or password"]);
            exit;
        }

        $user = $validate->fetch_assoc();

        if (!is_null($user["deleted_at"]) || $user["status"] != 1) {
            http_response_code(401);
            echo json_encode(["message" => "Account freeze or been deleted"]);
            exit;
        }

        $token = base64_encode($user["email"] . ":" . uniqid());

        $dateAfter30Days = date('Y-m-d H:i:s', strtotime('+30 days', strtotime($dateNow)));


        $addToken = $conn->query("INSERT INTO `apps_token`(`id`, `user_id`, `token`, `created_at`, `expired_at`) VALUES (NULL,'" . $user["id"] . "','$token','$dateNow','$dateAfter30Days')");

        echo json_encode([
            "token" => $token,
            "user" => [
                "id" => $user["id"],
                "fname" => $user["f_name"],
                "lname" => $user["l_name"],
                "role" => $user["role"],
                "email" => $user["email"]
            ]
        ]);
    }

    public function validateToken()
    {
        include "db.php";
        header('Content-Type: application/json');


        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();

        $data = json_decode(file_get_contents('php://input'), true);
        $token = $data['token'] ?? '';
        $userId = $data['user_id'] ?? '';

        if (!$token || !$userId) {
            echo json_encode(['valid' => false, 'message' => 'Missing token or user_id']);
            exit;
        }

        $validate = $conn->query("SELECT * FROM apps_token WHERE user_id='$userId' AND `token`='$token'");

        if ($validate->num_rows != 1) {
            echo json_encode(['valid' => false, 'message' => '']);
            exit;
        }

        $row = $validate->fetch_assoc();

        if ($dateNow > $row["expired_at"]) {
            echo json_encode(['valid' => false, 'message' => 'Redirecting to login.']);
            exit;
        }

        echo json_encode([
            "valid" => true
        ]);
    }

    public function dashboardData()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');


        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();

        $response = [];

        // try {
        // Total Sales
        $result = $conn->query("SELECT SUM(myr_value_include_postage) AS total_sales FROM customer_orders WHERE status IN(1,2,3,4) AND deleted_at IS NULL");
        $row = $result->fetch_assoc();
        $response['totalSales'] = floatval($row['total_sales'] ?? 0);

        // Today's Sales
        $today = date('Y-m-d');
        $result = $conn->query("SELECT SUM(myr_value_include_postage) AS today_sales FROM customer_orders WHERE status IN(1,2,3,4) AND created_at LIKE '%$today%' AND deleted_at IS NULL");
        $row = $result->fetch_assoc();
        $response['todaySales'] = floatval($row['today_sales'] ?? 0);

        // Yesterday's Sales
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $result = $conn->query("SELECT SUM(myr_value_include_postage) AS yesterday_sales FROM customer_orders WHERE status IN(1,2,3,4) AND created_at LIKE '%$yesterday%' AND deleted_at IS NULL");
        $row = $result->fetch_assoc();
        $response['yesterdaySales'] = floatval($row['yesterday_sales'] ?? 0);

        // Total Products
        $result = $conn->query("SELECT COUNT(*) AS total_product FROM products WHERE deleted_at IS NULL");
        $row = $result->fetch_assoc();
        $response['totalProduct'] = intval($row['total_product'] ?? 0);

        // Total Categories
        $result = $conn->query("SELECT COUNT(*) AS total_category FROM categories WHERE deleted_at IS NULL");
        $row = $result->fetch_assoc();
        $response['totalCategory'] = intval($row['total_category'] ?? 0);

        // Total Brands
        $result = $conn->query("SELECT COUNT(*) AS total_brand FROM brands WHERE deleted_at IS NULL");
        $row = $result->fetch_assoc();
        $response['totalBrand'] = intval($row['total_brand'] ?? 0);

        // Total Orders
        $result = $conn->query("SELECT COUNT(*) AS total_order FROM customer_orders WHERE deleted_at IS NULL");
        $row = $result->fetch_assoc();
        $response['totalOrder'] = intval($row['total_order'] ?? 0);

        // Confirmed Orders (assuming confirmed status = 'completed')
        $result = $conn->query("SELECT COUNT(*) AS confirm_order FROM customer_orders WHERE status = '4'");
        $row = $result->fetch_assoc();
        $response['confirmOrder'] = intval($row['confirm_order'] ?? 0);

        echo json_encode($response);
        // } catch (Exception $e) {
        //     http_response_code(500);
        //     echo json_encode(['error' => 'Server error', 'message' => $e->getMessage()]);
        // }

        $conn->close();
    }

    public function profileData($id)
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');


        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();

        $response = [];

        $result = $conn->query("SELECT * FROM member_hq WHERE id='$id'");
        $row = $result->fetch_assoc();

        //const { first_name, last_name, phone_number } = response.data;
        $response['first_name'] = $row['f_name'] ?? '';
        $response['last_name'] = $row['l_name'] ?? '';
        $response['phone_number'] = $row['phone'] ?? '';

        echo json_encode($response);
        // } catch (Exception $e) {
        //     http_response_code(500);
        //     echo json_encode(['error' => 'Server error', 'message' => $e->getMessage()]);
        // }

        $conn->close();
    }

    public function updateProfile($id)
    {
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Authorization, Content-Type");


        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();



        $input = json_decode(file_get_contents('php://input'), true);


        $firstName = trim($input['first_name'] ?? '');
        $lastName = trim($input['last_name'] ?? '');
        $phoneNumber = trim($input['phone_number'] ?? '');

        if (empty($firstName) || empty($lastName)) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => 'First name and last name required']);
            exit;
        }

        $update = $conn->query("UPDATE member_hq SET f_name='$firstName', l_name='$lastName', phone='$phoneNumber' WHERE id='$id'");

        if ($update) {
            echo json_encode(['status' => 'success', 'message' => 'Profile updated']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
        }
    }

    public function updatePassword($id)
    {
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Authorization, Content-Type");


        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();

        $userId = $id;



        $data = json_decode(file_get_contents('php://input'), true);
        $currentPassword = $data['password'] ?? '';
        $newPassword = $data['newPassword'] ?? '';
        $newCPassword = $data['newCPassword'] ?? '';

        if (!$currentPassword || !$newPassword || !$newCPassword) {
            //http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            exit;
        }

        if ($newPassword !== $newCPassword) {
            //http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => 'New passwords do not match']);
            exit;
        }

        // Validate password format
        function validatePassword($password)
        {
            if (strlen($password) < 8) return 'Password must be at least 8 characters';
            if (!preg_match('/[A-Z]/', $password)) return 'Password must contain at least one uppercase letter';
            if (!preg_match('/[\W_]/', $password)) return 'Password must contain at least one special character';
            return '';
        }

        $validationError = validatePassword($newPassword);
        if ($validationError) {
            //http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => $validationError]);
            exit;
        }

        // Get user from database
        $stmt = $conn->query("SELECT * FROM member_hq WHERE id = '$id'");

        if ($stmt->num_rows != 1) {
            //http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }


        $user = $stmt->fetch_assoc();

        $oldHashedPassword = hash("sha256", $currentPassword);

        if ($oldHashedPassword != $user['password']) {
            //http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
            exit;
        }

        // Hash new password and update
        $newHashedPassword = hash("sha256", $newPassword);
        $updateStmt = $conn->query("UPDATE member_hq SET password = '$newHashedPassword' WHERE id = '$userId'");
        //$updateStmt->execute([$newHashedPassword, $userId]);

        echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
        exit;
    }

    public function listCountry()
    {
        $domainURL = getMainUrl();

        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Select Country";

        $listCountry = allSaleCountry();


        $sql = "SELECT id, name, sign, rate, phone_code FROM list_country WHERE status = 1";
        $result = $conn->query($sql);

        $countries = [];
        while ($row = $result->fetch_assoc()) {
            $countries[] = [
                "code" => $row["id"],         // you can also map to ISO code if available
                "name" => $row["name"],
                "sign" => $row["sign"],
                "rate" => $row["rate"],
                "phone_code" => $row["phone_code"]
            ];
        }

        echo json_encode($countries);
    }

    public function listBrand()
    {
        $domainURL = getMainUrl();

        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Select Country";

        $listCountry = allSaleCountry();


        $sql = "SELECT id, name, slug, image FROM brands WHERE deleted_at IS NULL";
        $result = $conn->query($sql);

        $brands = [];
        while ($row = $result->fetch_assoc()) {
            $brands[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode($brands);
    }

    public function listCategory()
    {
        $domainURL = getMainUrl();

        $mainDomain = mainDomain();
        $conn = getDbConnection();
        $currentYear = currentYear();
        $dateNow = dateNow();
        $pageName = "Select Country";

        $listCountry = allSaleCountry();


        $sql = "SELECT id, name, slug, image FROM categories WHERE deleted_at IS NULL";
        $result = $conn->query($sql);

        $brands = [];
        while ($row = $result->fetch_assoc()) {
            $brands[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode($brands);
    }

    public function getNewOrder()
    {
        $conn = getDbConnection();

        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 20;
        $offset = ($page - 1) * $limit;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // Prepare search filter
        $searchSql = '';
        if (!empty($search)) {
            $searchSafe = $conn->real_escape_string($search);
            $searchSql = " AND (customer_name LIKE '%$searchSafe%' OR country LIKE '%$searchSafe%')";
        }

        // Count total
        $countSql = "SELECT COUNT(*) AS total FROM customer_orders WHERE status = 1 AND deleted_at IS NULL $searchSql";
        $countResult = $conn->query($countSql);
        $totalRow = $countResult->fetch_assoc();
        $total = (int)$totalRow['total'];
        $totalPages = ceil($total / $limit);

        // Main query
        $sql = "
    SELECT 
        co.id AS order_id,
        co.customer_name,
        co.total_qty,
        co.postage_cost,
        co.currency_sign,
        co.country,
        co.status,
        co.customer_phone,
        co.created_at,
        co.myr_value_without_postage,
        c.quantity,
        p.name AS product_name
    FROM customer_orders co
    LEFT JOIN cart c ON c.session_id = co.session_id
    LEFT JOIN products p ON p.id = c.p_id
    WHERE co.status = 1 AND co.deleted_at IS NULL AND c.deleted_at IS NULL AND c.status IN(0,1) $searchSql
    ORDER BY co.created_at DESC
    LIMIT $limit OFFSET $offset
";

        $result = $conn->query($sql);

        $orders = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $orderId = $row['order_id'];

                if (!isset($orders[$orderId])) {
                    $orders[$orderId] = [
                        'id' => $orderId,
                        'customer_name' => $row['customer_name'],
                        'total_qty' => $row['total_qty'],
                        'postage_cost' => $row['postage_cost'],
                        'currency_sign' => $row['currency_sign'],
                        'country' => $row['country'],
                        'status' => $row['status'],
                        'customer_phone' => $row['customer_phone'],
                        'created_at' => $row['created_at'],
                        'myr_value_without_postage' => $row['myr_value_without_postage'],
                        'products' => []
                    ];
                }

                // Only add product if exists (in case LEFT JOIN has no match)
                if (!empty($row['product_name']) && $row['quantity'] !== null) {
                    $orders[$orderId]['products'][] = [
                        'name' => $row['product_name'],
                        'quantity' => (int)$row['quantity']
                    ];
                }
            }
        }

        // Reset numeric array keys
        $orders = array_values($orders);

        // Output JSON response
        echo json_encode([
            'orders' => $orders,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total' => $total
        ]);


        // $orders = [];
        // if ($result && $result->num_rows > 0) {
        //     while ($row = $result->fetch_assoc()) {
        //         $orders[] = $row;
        //     }
        // }

        // echo json_encode([
        //     'orders' => $orders,
        //     'current_page' => $page,
        //     'total_pages' => $totalPages,
        //     'total' => $total
        // ]);


    }
}
