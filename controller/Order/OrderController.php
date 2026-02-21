<?php

namespace Order;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/Order.php';
require_once __DIR__ . '/../../model/Cart.php';

class OrderController
{
    private $domainURL;
    private $mainDomain;
    private $conn;
    private $currentYear;
    private $dateNow;

    private $orderModel;
    private $cartModel;

    public function __construct()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        $this->domainURL   = getMainUrl();
        $this->mainDomain  = mainDomain();
        $this->conn        = getDbConnection();
        $this->currentYear = currentYear();
        $this->dateNow     = dateNow();

        $this->orderModel = new \Order($this->conn);
        $this->cartModel  = new \Cart($this->conn);
    }

    private function checkAccess($segment = null)
    {
        if ($segment === null) {
            $currentPaths = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
            $segments = explode('/', $currentPaths);
            $segment = $segments[0];
        }
        if (roleVerify($segment, $_SESSION['user']->id) == 0) {
            header("Location: " . $this->domainURL . "access-denied");
            exit;
        }
    }

    private function buildHavingCondition($search, $qty)
    {
        $havingCondition = "";
        if (!empty($search) && empty($qty)) {
            $havingCondition = "HAVING COUNT(*) = 1 AND MAX(p.name) LIKE '%$search%'";
        } else if (!empty($search) && $qty > 0) {
            $havingCondition = "HAVING COUNT(*) = 1 AND MAX(p.name) LIKE '%$search%' AND MAX(c.quantity) = $qty";
        }
        return $havingCondition;
    }

    private function buildOrderBy($sort)
    {
        $orderBy = "ORDER BY co.created_at DESC";
        if ($sort === "asc") {
            $orderBy = "ORDER BY MAX(c.quantity) ASC";
        } elseif ($sort === "desc") {
            $orderBy = "ORDER BY MAX(c.quantity) DESC";
        }
        return $orderBy;
    }

    private function listOrders($status, $pageName, $routePrefix, $limit = 30, $withAWB = false)
    {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;

        if (!empty($_GET['page']) && $_GET['page'] == "1") {
            $url = $this->domainURL . $routePrefix;
            $params = [];
            if (isset($_GET["filter"]) && !empty($_GET["filter"])) {
                $params[] = "filter=" . $_GET["filter"];
            }
            if (isset($_GET["qty"])) {
                $params[] = "qty=" . $_GET["qty"];
            }
            if (!empty($params)) {
                $url .= "?" . implode("&", $params);
            }
            header("Location: " . $url);
            exit;
        }

        $search = isset($_GET['filter']) ? $this->conn->real_escape_string($_GET['filter']) : '';
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $qty = isset($_GET['qty']) && is_numeric($_GET['qty']) ? (int)$_GET['qty'] : null;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
        $printed = isset($_GET['printed']) && $_GET['printed'] !== '' ? (int)$_GET['printed'] : null;
        $offset = ($page - 1) * $limit;

        $havingCondition = $this->buildHavingCondition($search, $qty);
        $orderBy = $this->buildOrderBy($sort);

        $totalOrders = $this->orderModel->countByStatusFiltered($status, $havingCondition);
        $totalPages = ceil($totalOrders / $limit);

        if ($withAWB) {
            $orders = $this->orderModel->listByStatusWithAWB($status, $havingCondition, $orderBy, $limit, $offset);
        } else {
            $orders = $this->orderModel->listByStatus($status, $havingCondition, $orderBy, $limit, $offset);
        }

        $firstSegments = $routePrefix;

        require_once __DIR__ . '/../../view/Admin/orders.php';
    }

    public function newOrder()
    {
        $this->listOrders(1, "Order - New", "new-order", 100);
    }

    public function processOrder()
    {
        $this->listOrders(2, "Order - Process", "process-order", 100, true);
    }

    public function inDeliveryOrder()
    {
        $this->listOrders(3, "Order - In Delivery", "indelivery-order", 30);
    }

    public function completeOrder()
    {
        $this->listOrders(4, "Order - Complete", "completed-order", 30);
    }

    public function returnOrder()
    {
        $this->listOrders(5, "Order - Return", "returned-order", 30);
    }

    public function cancelOrder()
    {
        $this->listOrders(6, "Order - Cancel", "cancelled-order", 30);
    }

    public function submitCourier()
    {
        $this->checkAccess();

        if (isset($_POST["dhl"])) {
            $orderID = $_POST["orderID"];
            $createShipping = dhlCreateShipping($orderID);

            if (!empty($createShipping["deliveryConfirmationNo"])) {
                $deliveryConfirmationNo = $createShipping["deliveryConfirmationNo"];
                $tracking = "https://www.dhl.com/my-en/home/tracking.html?tracking-id=" . $deliveryConfirmationNo;

                $this->orderModel->submitToCourier($orderID, 2, 'DHL ECOMMERCE', $deliveryConfirmationNo, $tracking, $this->dateNow);

                $_SESSION['upload_success'] = "Successfully submit order to DHL";
                header("Location: " . $this->domainURL . "new-order");
            } else {
                $formatted = str_pad($orderID, 8, '0', STR_PAD_LEFT);
                $_SESSION['upload_error'] = "Order #" . $formatted . " " . $createShipping["message"];
                header("Location: " . $this->domainURL . "new-order");
            }
        } else if (isset($_POST["jnt"])) {
            $orderID = $_POST["orderID"];
            $createJTShipping = createJTShipping($orderID);

            if (empty($createJTShipping["false"])) {
                $_SESSION['upload_success'] = "All order successfull send all order to J&T.";
                $addActivity = activity($_SESSION['user']->id, "Successfully send All order successfull send all order to J&T.", "customer_orders|$orderID", "submit_awb_jnt");
            } else {
                $_SESSION['upload_error'] = $createJTShipping["failed"] . "/" . $createJTShipping["all"] . " of order failed to send to J&T.<br>" . $createJTShipping["false"];
                $addActivity = activity($_SESSION['user']->id, "Failed send to J&T", "customer_orders|$orderID", "submit_awb_jnt");
            }

            header("Location: " . $this->domainURL . "new-order");
        } else if (isset($_POST["ninja"])) {
            $orderID = $_POST["orderID"];
            $_SESSION['upload_success'] = "Successfully submit order to NinjaVan";
            header("Location: " . $this->domainURL . "new-order");
        }
    }

    public function printAWB()
    {
        $domainURL = $this->domainURL;

        if (isset($_POST["print-awb"]) || isset($_POST["printAWB"])) {
            $orderID = $_POST["orderID"];

            $_SESSION['upload_success'] = "Successfully created pdf AWB J&T.";
            $addActivity = activity($_SESSION['user']->id, "Successfully created pdf AWB J&T.", "customer_orders|$orderID", "print_awb");

?>
            <script>
                const url = "<?= $domainURL; ?>awb-jt.php?id=<?= $orderID ?>";
                window.open(url, "_blank");
                setTimeout(() => {
                    window.location.href = "<?= $domainURL; ?>process-order";
                }, 1000);
            </script>
<?php
            exit;
        }

        if (isset($_POST["move-indelivery"])) {
            $orderID = $_POST["orderID"];

            $orderIDs = explode(",", $_POST["orderID"]);
            $orderIDs = array_map('intval', $orderIDs);

            $this->orderModel->bulkMoveStatus($orderIDs, 3, $this->dateNow);

            $_SESSION['upload_success'] = "Successfully move order to In Delivery.";
            $addActivity = activity($_SESSION['user']->id, "Successfully move order to In Delivery.", "customer_orders|$orderID", "move_to_indelivery");

            header("Location: " . $this->domainURL . "process-order");
            exit;
        }
    }

    public function detailsBuyer()
    {
        $this->checkAccess();

        $domainURL = $this->domainURL;
        $conn      = $this->conn;

        if (!empty($_GET["order_id"])) {
            $id = $_GET["order_id"];
            $addActivity = activity($_SESSION['user']->id, "View details customer", "customer_orders|$id", "view_customers");

            $row = $this->orderModel->getOrderDetails($id);
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
                    $parts = explode(",", $string);

                    foreach ($parts as $varID) {
                        $id = str_replace(['[', ']'], '', $varID);

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
                            <td>Payment Channel</td>
                            <td style="text-align:right;">
                                <p class="p_details"><span class="mention" id="p_order_name"><?= $row["payment_channel"] ?></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>Store Subtotal</td>
                            <td style="text-align:right;">
                                <p class="p_details"><span class="mention" id="p_order_name">RM <?= number_format($row["myr_value_without_postage"], 2) ?></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>Shipping Fee</td>
                            <td style="text-align:right;">
                                <p class="p_details"><span class="mention" id="p_order_name">RM <?= number_format($row["postage_cost"], 2) ?></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>GST</td>
                            <td style="text-align:right;">
                                <p class="p_details"><span class="mention" id="p_order_name">( include )</span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>Total Buyer Payment</td>
                            <td style="text-align:right;">
                                <p class="p_details"><span class="mention" id="p_order_name">RM <?= number_format($row["myr_value_include_postage"], 2) ?></span></p>
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
        $this->checkAccess();

        $domainURL = $this->domainURL;
        $conn      = $this->conn;

        if (!empty($_GET["order_id"])) {
            $id = $_GET["order_id"];

            $row = $this->orderModel->getOrderDetails($id);
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
                        const data = {
                            order_id: $("#orderNo").val(),
                            name: $("#c_name").val().trim(),
                            name_last: $("#c_name_last").val().trim(),
                            email: $("#c_email").val().trim(),
                            phone: $("#c_phone").val().trim(),
                            address1: $("#c_address1").val().trim(),
                            address2: $("#c_address2").val().trim(),
                            city: $("#c_city").val().trim(),
                            postcode: $("#c_postcode").val().trim(),
                            state: $("#c_state").val().trim()
                        };

                        if (!data.name || !data.email || !data.phone ||
                            !data.address1 || !data.city || !data.postcode || !data.state) {
                            Swal.fire({ icon: 'warning', title: 'Missing Fields', text: 'Please fill in all required fields mark by *.' });
                            return;
                        }

                        Swal.fire({ title: 'Updating...', text: 'Please wait while we update the customer details.', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

                        $.ajax({
                            url: "<?= $domainURL ?>update-customer",
                            type: "POST",
                            data: data,
                            success: function(response) {
                                Swal.fire({ icon: 'success', title: 'Success', text: 'Customer details updated successfully. Order #' + data.order_id });
                            },
                            error: function() {
                                Swal.fire({ icon: 'error', title: 'Update Failed', text: 'Something went wrong. Please try again later.' });
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
        $this->checkAccess();

        $order = $this->orderModel->getOrderDetails($orderid);
        $sessionID = $order["session_id"];

        $this->orderModel->updateStatus($orderid, $next, $this->dateNow);

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
            $this->cartModel->updateStatusBySession($sessionID, '2', $this->dateNow);
            $mes = "Successfully set order #" . $orderid . " to Return to Sender";
        } else if ($next == "6") {
            $this->cartModel->updateStatusBySession($sessionID, '3', $this->dateNow);
            $mes = "Successfully set order #" . $orderid . " to Cancelled";
        }

        $addActivity = activity($_SESSION['user']->id, $mes, "customer_orders|$orderid", "order_activity");

        $_SESSION['upload_success'] = $mes;
        header("Location: " . $this->domainURL . $pg);
    }

    public function moveToProcessing($orderid)
    {
        $this->orderModel->updateStatus($orderid, 2, $this->dateNow);

        $previous = $_SERVER['HTTP_REFERER'] ?? '/';
        if (empty($previous)) {
            $previous = '/';
        }

        header("Location: $previous");
        exit;
    }

    public function updateCustomer()
    {
        $order_id = intval($_POST['order_id']);

        $data = [
            'name'      => $_POST['name'] ?? '',
            'name_last' => $_POST['name_last'] ?? '',
            'email'     => $_POST['email'] ?? '',
            'phone'     => $_POST['phone'] ?? '',
            'address1'  => $_POST['address1'] ?? '',
            'address2'  => $_POST['address2'] ?? '',
            'city'      => $_POST['city'] ?? '',
            'postcode'  => $_POST['postcode'] ?? '',
            'state'     => $_POST['state'] ?? '',
        ];

        if ($this->orderModel->updateCustomerDetails($order_id, $data, $this->dateNow)) {
            $addActivity = activity($_SESSION['user']->id, "Successfull update details customer", "customer_orders|$order_id", "update_customer_details");
            echo "success";
        } else {
            http_response_code(500);
            echo "error";
        }
    }

    public function database()
    {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;
        $pageName    = "Order Database";

        require_once __DIR__ . '/../../view/Admin/order-databse.php';
    }

    public function searchOrder()
    {
        $this->checkAccess();

        $domainURL   = $this->domainURL;
        $mainDomain  = $this->mainDomain;
        $conn        = $this->conn;
        $currentYear = $this->currentYear;
        $dateNow     = $this->dateNow;
        $pageName    = "Search Order";

        $search = isset($_GET['search']) ? trim($this->conn->real_escape_string($_GET['search'])) : '';
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $limit = 30;
        $offset = ($page - 1) * $limit;

        $where = "WHERE co.deleted_at IS NULL ";
        if ($search !== '') {
            $searchEscaped = $search;
            $where .= " AND (
                co.id = '$searchEscaped'
                OR co.customer_name LIKE '%$searchEscaped%'
                OR co.customer_phone LIKE '%$searchEscaped%'
                OR co.customer_email LIKE '%$searchEscaped%'
            )";
        }

        $totalOrders = $this->orderModel->countSearchResults($where);
        $totalPages = ceil($totalOrders / $limit);

        $orders = $this->orderModel->searchOrdersList($where, $limit, $offset);

        require_once __DIR__ . '/../../view/Admin/orders-search.php';
    }
}
