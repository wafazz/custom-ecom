<?php
$where = "co.deleted_at IS NULL";
if (!empty($_GET['date_from'])) {
  $from = $conn->real_escape_string($_GET['date_from']);
  $where .= " AND DATE(co.created_at) >= '$from'";
}
if (!empty($_GET['date_to'])) {
  $to = $conn->real_escape_string($_GET['date_to']);
  $where .= " AND DATE(co.created_at) <= '$to'";
}
if (!empty($_GET['status'])) {
  $status = (int)$_GET['status'];
  $where .= " AND co.status = '$status'";
}
if (!empty($_GET['country'])) {
  $country = $conn->real_escape_string($_GET['country']);
  $where .= " AND co.country = '$country'";
}
if (!empty($_GET['product'])) {
  $product = $conn->real_escape_string($_GET['product']);
  $where .= " AND p.name LIKE '%$product%'";
}
if (!empty($_GET['sku'])) {
    $sku = $conn->real_escape_string($_GET['sku']);
    $where .= " AND pv.sku LIKE '%$sku%'";
}

$sql = "
  SELECT co.id, co.customer_name, co.customer_phone, co.created_at, co.country,
         p.name AS product_name, pv.sku, c.quantity, co.status, co.myr_value_include_postage
  FROM customer_orders co
  JOIN cart c ON co.session_id = c.session_id AND c.deleted_at IS NULL
  JOIN products p ON c.p_id = p.id AND p.deleted_at IS NULL
  JOIN product_variants pv ON pv.id = c.pv_id
  WHERE $where
  ORDER BY co.created_at DESC
";
$res = $conn->query($sql);
$statusLabels = [
  1 => 'New', 2 => 'Processing', 3 => 'In Delivery',
  4 => 'Completed', 5 => 'Return', 6 => 'Canceled'
];

$totalQty = 0;
$totalPrice = 0;

while ($row = $res->fetch_assoc()) {
  echo "<tr>
    <td>{$row['created_at']}</td>
    <td>#{$row['id']}</td>
    <td>{$row['customer_name']}</td>
    <td>{$row['customer_phone']}</td>
    <td>{$row['country']}</td>
    <td>{$row['sku']}</td>
    <td>{$row['product_name']}</td>
    <td>{$row['quantity']}</td>
    <td>{$statusLabels[$row['status']]}</td>
    <td>{$row['myr_value_include_postage']}</td>
  </tr>";

        $totalQty += $row['quantity'];
        $totalPrice += $row['myr_value_include_postage'];
}
?>