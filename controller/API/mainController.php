<?php

namespace API;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/MemberHq.php';
require_once __DIR__ . '/../../model/AppsToken.php';
require_once __DIR__ . '/../../model/Order.php';
require_once __DIR__ . '/../../model/Product.php';
require_once __DIR__ . '/../../model/Category.php';
require_once __DIR__ . '/../../model/Brand.php';
require_once __DIR__ . '/../../model/ListCountry.php';


class mainController
{
    private $conn;
    private $memberModel;
    private $tokenModel;
    private $orderModel;
    private $productModel;
    private $categoryModel;
    private $brandModel;
    private $countryModel;

    public function __construct()
    {
        $this->conn = getDbConnection();
        $this->memberModel = new \MemberHq($this->conn);
        $this->tokenModel = new \AppsToken($this->conn);
        $this->orderModel = new \Order($this->conn);
        $this->productModel = new \Product($this->conn);
        $this->categoryModel = new \Category($this->conn);
        $this->brandModel = new \Brand($this->conn);
        $this->countryModel = new \ListCountry($this->conn);
    }

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
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Headers: Content-Type");

        $dateNow = dateNow();

        $data = json_decode(file_get_contents("php://input"), true);

        $email = $data["email"] ?? '';
        $password = $data["password"] ?? '';

        if (!$email || !$password) {
            http_response_code(400);
            echo json_encode(["message" => "Email and password are required"]);
            exit;
        }

        $hashPass = hash("sha256", $password);

        $user = $this->memberModel->authenticate($email, $hashPass);

        if (!$user) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid email or password"]);
            exit;
        }

        if (!is_null($user["deleted_at"]) || $user["status"] != 1) {
            http_response_code(401);
            echo json_encode(["message" => "Account freeze or been deleted"]);
            exit;
        }

        $token = base64_encode($user["email"] . ":" . uniqid());
        $dateAfter30Days = date('Y-m-d H:i:s', strtotime('+30 days', strtotime($dateNow)));

        $this->tokenModel->createToken($user["id"], $token, $dateNow, $dateAfter30Days);

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
        header('Content-Type: application/json');

        $dateNow = dateNow();

        $data = json_decode(file_get_contents('php://input'), true);
        $token = $data['token'] ?? '';
        $userId = $data['user_id'] ?? '';

        if (!$token || !$userId) {
            echo json_encode(['valid' => false, 'message' => 'Missing token or user_id']);
            exit;
        }

        $row = $this->tokenModel->validateToken($userId, $token);

        if (!$row) {
            echo json_encode(['valid' => false, 'message' => '']);
            exit;
        }

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

        $response = [];

        $response['totalSales'] = $this->orderModel->totalSales();

        $today = date('Y-m-d');
        $response['todaySales'] = $this->orderModel->totalSalesByDate($today);

        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $response['yesterdaySales'] = $this->orderModel->totalSalesByDate($yesterday);

        $response['totalProduct'] = $this->productModel->count(['deleted_at' => null]);
        $response['totalCategory'] = $this->categoryModel->countActive();
        $response['totalBrand'] = $this->brandModel->countActive();
        $response['totalOrder'] = $this->orderModel->countAll();
        $response['confirmOrder'] = $this->orderModel->countByStatusValue(4);

        echo json_encode($response);
    }

    public function profileData($id)
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $row = $this->memberModel->findById($id);

        $response = [];
        $response['first_name'] = $row['f_name'] ?? '';
        $response['last_name'] = $row['l_name'] ?? '';
        $response['phone_number'] = $row['phone'] ?? '';

        echo json_encode($response);
    }

    public function updateProfile($id)
    {
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Authorization, Content-Type");

        $input = json_decode(file_get_contents('php://input'), true);

        $firstName = trim($input['first_name'] ?? '');
        $lastName = trim($input['last_name'] ?? '');
        $phoneNumber = trim($input['phone_number'] ?? '');

        if (empty($firstName) || empty($lastName)) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => 'First name and last name required']);
            exit;
        }

        $update = $this->memberModel->updateProfileApi($id, $firstName, $lastName, $phoneNumber);

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

        $userId = $id;

        $data = json_decode(file_get_contents('php://input'), true);
        $currentPassword = $data['password'] ?? '';
        $newPassword = $data['newPassword'] ?? '';
        $newCPassword = $data['newCPassword'] ?? '';

        if (!$currentPassword || !$newPassword || !$newCPassword) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            exit;
        }

        if ($newPassword !== $newCPassword) {
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
            echo json_encode(['status' => 'error', 'message' => $validationError]);
            exit;
        }

        $user = $this->memberModel->findById($id);

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }

        $oldHashedPassword = hash("sha256", $currentPassword);

        if ($oldHashedPassword != $user['password']) {
            echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
            exit;
        }

        $newHashedPassword = hash("sha256", $newPassword);
        $this->memberModel->updatePassword($userId, $newHashedPassword);

        echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
        exit;
    }

    public function listCountry()
    {
        $rows = $this->countryModel->getActiveWithDetails();

        $countries = [];
        foreach ($rows as $row) {
            $countries[] = [
                "code" => $row["id"],
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
        $brands = $this->brandModel->getList();

        header('Content-Type: application/json');
        echo json_encode($brands);
    }

    public function listCategory()
    {
        $categories = $this->categoryModel->getList();

        header('Content-Type: application/json');
        echo json_encode($categories);
    }

    public function getNewOrder()
    {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 20;
        $offset = ($page - 1) * $limit;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        $searchSql = '';
        if (!empty($search)) {
            $searchSafe = $this->conn->real_escape_string($search);
            $searchSql = " AND (customer_name LIKE '%$searchSafe%' OR country LIKE '%$searchSafe%')";
        }

        $total = $this->orderModel->countNewOrders($searchSql);
        $totalPages = ceil($total / $limit);

        $result = $this->orderModel->getNewOrdersWithProducts($searchSql, $limit, $offset);

        $orders = [];
        foreach ($result as $row) {
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

            if (!empty($row['product_name']) && $row['quantity'] !== null) {
                $orders[$orderId]['products'][] = [
                    'name' => $row['product_name'],
                    'quantity' => (int)$row['quantity']
                ];
            }
        }

        $orders = array_values($orders);

        echo json_encode([
            'orders' => $orders,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total' => $total
        ]);
    }
}
