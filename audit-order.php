<?php
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';

$conn = getDbConnection();


$sql = "
SELECT 
    co.id AS order_id,
    co.session_id,
    co.order_to,
    co.total_qty,
    co.total_price AS o_price,
    co.customer_name,
    co.status AS order_status,
    co.payment_url,
    co.created_at,
    SUM(c.quantity * c.price) AS total_item_price,
    GROUP_CONCAT(DISTINCT CONCAT('[', c.pv_id, ']') ORDER BY c.pv_id ASC) AS pv_ids,
    SUM(c.quantity) AS total_cart_qty
FROM customer_orders co
LEFT JOIN cart c ON co.session_id = c.session_id
WHERE co.total_qty = 0 AND co.status = 1 AND c.status = 1
GROUP BY co.id
ORDER BY co.id ASC
";

if ($conn->query($sql)->num_rows < 1) {
    echo "<p>No orders found.</p>";
} else {
    $data = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

    echo "<ol>";
    $x = 1;
    foreach ($data as &$row) {
        $theOrderID = htmlspecialchars($row['order_id']);
        if ($x == 1) {
            $pvs = htmlspecialchars($row['pv_ids']);
            $cqty = htmlspecialchars($row['total_cart_qty']);
            $updateOrder = $conn->query("UPDATE customer_orders SET product_var_id = '$pvs', total_qty='$cqty' WHERE id = '$theOrderID'");
            $upd = "<br>-->> <span style='color: green;font-weight:bold;'>UPDATED</span>";
        } else {
            $upd = "";
        }
        echo "<li>" . htmlspecialchars($row['order_id']) . " - RM(" . htmlspecialchars($row['o_price']) . ") - " . htmlspecialchars($row['session_id']) . " on " . htmlspecialchars($row['created_at']) . " 
    <br>
    Cart Items: " . htmlspecialchars($row['pv_ids']) . " (Cart QTY: " . htmlspecialchars($row['total_cart_qty']) . ") - RM(" . htmlspecialchars($row['total_item_price']) . ")
    <br>
    Payment URL: <a href='https://rozeyana.com/check-stripe-status.php?payment_intent=" . htmlspecialchars($row['payment_url']) . "' target='_blank'>Check Payment Status</a>" . $upd . "
    </li>";
        $x++;
    }
    echo "</ol>";
}
