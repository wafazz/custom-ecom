<?php
namespace Ecom;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/Cart.php';
require_once __DIR__ . '/../../model/Product.php';

class AddToCartController
{
    private $conn;
    private $cart;
    private $product;

    public function __construct()
    {
        $this->conn = getDbConnection();
        $this->cart = new \Cart($this->conn);
        $this->product = new \Product($this->conn);
    }

    public function addCart()
    {
        $sessionId = $_SESSION['session_id'] ?? session_id();
        if (!rate_limit("ratelimit:cart:{$sessionId}", 30, 60)) {
            header("Content-Type: application/json");
            http_response_code(429);
            echo json_encode(["message" => "Too many requests. Please slow down."]);
            return;
        }

        if (isset($_COOKIE['country'])) {
            $country = $_COOKIE['country'];
        } else {
            header("Location: /");
            exit;
        }

        $currentYear = currentYear();
        $dateNow = dateNow();

        $data = dataCountry($country);

        $countryID = $country;
        $countryName = $data["name"];
        $currencySign = $data["sign"];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $p_id = (int) $_POST['p_id'];
            $pv_id = (int) $_POST['pv_id'];
            $qty = (int) $_POST['qty'];

            if ($qty <= 0) {
                echo "Invalid quantity";
                exit;
            }

            $product = $this->product->getWithVariant($p_id);
            $pPrice = $this->product->getPriceForCountry($countryID, $p_id);

            $cartPrice = $pPrice["sale"];

            if (!isset($_SESSION["session_id"]) || empty($_SESSION["session_id"])) {
                $_SESSION["session_id"] = $currentYear . "_" . $countryName . "_" . $currencySign . "_" . uniqid('cart_', true);
            }

            $session_id = $_SESSION["session_id"];

            $stock = $this->product->getStockBalance($p_id);

            if ($qty > $stock["physical_stock"]) {
                echo "Not enough stock";
                exit;
            }

            if ($qty > $product["max_purchase"]) {
                echo "This item can be added to cart Max Quantity " . $product["max_purchase"] . " per purchase.";
                exit;
            }

            $weightUnit = $product["weight"];
            $totalWeight = $weightUnit * $qty;

            // Check if item already exists in cart
            $existing = $this->cart->findExistingItem($session_id, $p_id, $pv_id);

            if ($existing === null) {
                // Insert new cart item
                $newId = $this->cart->addItem([
                    'session_id' => $session_id,
                    'p_id' => $p_id,
                    'pv_id' => $pv_id,
                    'quantity' => $qty,
                    'price' => $cartPrice,
                    'weight' => $weightUnit,
                    'total_weight' => $totalWeight,
                    'currency_sign' => $currencySign,
                    'country_id' => $countryID,
                    'created_at' => $dateNow,
                    'updated_at' => $dateNow,
                    'status' => '0',
                ]);

                if ($newId) {
                    $this->cart->touchSession($session_id, $dateNow);
                    invalidateCache_cart($session_id);
                    echo "success";
                } else {
                    echo "Database error";
                }
            } else {
                $oldQty = $existing["quantity"];
                $newQty = $oldQty + $qty;

                if ($newQty > $product["max_purchase"]) {
                    echo "This item can be added to cart Max Quantity " . $product["max_purchase"] . " per purchase.";
                    exit;
                }

                $newWeight = $weightUnit * $newQty;

                $updated = $this->cart->updateItemQuantity($session_id, $p_id, $pv_id, $newQty, $newWeight, $dateNow);

                if ($updated) {
                    $this->cart->touchSession($session_id, $dateNow);
                    invalidateCache_cart($session_id);
                    echo "success";
                } else {
                    echo "Database error";
                }
            }
        }
    }

    public function countCart()
    {
        if (!isset($_SESSION["session_id"]) || empty($_SESSION["session_id"])) {
            echo 0;
            return;
        }
        $count = $this->cart->countBySession($_SESSION["session_id"]);
        echo $count;
    }

    public function listCart()
    {
        $domainURL = getMainUrl();

        if (!isset($_SESSION["session_id"]) || empty($_SESSION["session_id"])) {
            ?>
            <li style="color:grey !important;">no item in cart</li>
            <?php
            return;
        }

        $items = $this->cart->getActiveBySession($_SESSION["session_id"]);

        if (empty($items)) {
            ?>
            <li style="color:grey !important;">no item in cart</li>
            <?php
        } else {
            ?>
            <li><b>CART ITEMS</b></li>
            <?php
            foreach ($items as $row) {
                $productData = $this->product->getWithVariant($row["p_id"]);
                ?>
                <li>
                    <table style="width:100%;">
                        <tr>
                            <td><?= htmlspecialchars($productData["name"], ENT_QUOTES, 'UTF-8') ?></td>
                            <td style="width:40px;vertical-align:top;text-align:right;">x<b><?= htmlspecialchars($row["quantity"], ENT_QUOTES, 'UTF-8') ?></b></td>
                        </tr>
                    </table>
                </li>
                <?php
            }
            ?>
            <li><button class="btn btn-dark" onClick="window.location.href = '<?= htmlspecialchars($domainURL, ENT_QUOTES, 'UTF-8') ?>checkout'">CHECKOUT</button></li>
            <?php
        }
    }
}
