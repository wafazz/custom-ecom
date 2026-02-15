<?php
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';
$conn = getDbConnection();


header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=order_preview_" . date("Ymd_His") . ".xls");

echo "<table border='1'>";
echo "<tr>
  <th>Date</th><th>Order ID</th><th>Customer</th><th>Phone</th>
  <th>Country</th><th>SKU</th><th>Product</th><th>Qty</th><th>Status</th><th>Total (MYR)</th>
</tr>";

include "fetch-order-preview2.php";

echo '<tfoot>
                        <tr>
                            <th colspan="7" style="text-align: right;">Total</th>
                            <th>'.$totalQty.'</th>
                            <th>—</th>
                            <th>RM'.number_format($totalPrice, 2).'</th>
                        </tr>
                    </tfoot>';

echo "</table>";
?>