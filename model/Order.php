<?php

require_once __DIR__ . '/BaseModel.php';

class Order extends BaseModel
{
    protected $table = 'customer_orders';

    /**
     * Find order by type (replaces getOrder()).
     * $type: 1 = by id, 2 = by verify_id, 3 = by payment_code
     */
    public function findByType(int $type, string $value): ?array
    {
        $columns = [
            1 => 'id',
            2 => 'verify_id',
            3 => 'payment_code',
        ];

        $col = $columns[$type] ?? null;
        if ($col === null) {
            return null;
        }

        $sql = "SELECT * FROM `customer_orders` WHERE `{$col}` = ? LIMIT 1";
        $paramType = ($type === 1) ? 'i' : 's';
        $rows = $this->query($sql, $paramType, [$value]);
        return $rows[0] ?? null;
    }

    /**
     * Create a new order. Returns the new order ID.
     */
    public function createOrder(array $data): int
    {
        return $this->create($data);
    }

    /**
     * Update order status.
     */
    public function updateStatus(int $orderId, int $status, string $dateNow): bool
    {
        $sql = "UPDATE `customer_orders` SET `status` = ?, `updated_at` = ? WHERE `id` = ?";
        return $this->execute($sql, "isi", [$status, $dateNow, $orderId]);
    }

    /**
     * Set payment info from callback.
     */
    public function setPaymentInfo(int $orderId, string $channel, string $code, string $dateNow): bool
    {
        $sql = "UPDATE `customer_orders`
                SET `payment_channel` = ?, `payment_code` = ?, `updated_at` = ?
                WHERE `id` = ?";
        return $this->execute($sql, "sssi", [$channel, $code, $dateNow, $orderId]);
    }

    /**
     * Count orders grouped by status (replaces menuOrderCount()).
     */
    public function countByStatus(): array
    {
        $sql = "SELECT `status`, COUNT(*) AS cnt
                FROM `customer_orders`
                WHERE `deleted_at` IS NULL
                GROUP BY `status`";
        $rows = $this->query($sql);

        $counts = [
            "0" => 0, // new (status=1)
            "1" => 0, // processing (status=2)
            "2" => 0, // delivery (status=3)
            "3" => 0, // complete (status=4)
            "4" => 0, // return (status=5)
            "5" => 0, // cancel (status=6)
        ];

        foreach ($rows as $row) {
            $statusIndex = (int) $row['status'] - 1;
            $key = (string) $statusIndex;
            if (isset($counts[$key])) {
                $counts[$key] = (int) $row['cnt'];
            }
        }

        return $counts;
    }

    /**
     * Get total sales amount (replaces totalSales()).
     */
    public function totalSales(): float
    {
        $sql = "SELECT SUM(myr_value_include_postage) AS total_myr
                FROM `customer_orders`
                WHERE `status` IN (1, 2, 3, 4)
                AND `deleted_at` IS NULL";
        $rows = $this->query($sql);
        return (float) ($rows[0]['total_myr'] ?? 0);
    }

    /**
     * Set tracking/shipping info on an order.
     */
    public function setTracking(int $orderId, string $courier, string $awb, string $trackingUrl, string $dateNow): bool
    {
        $sql = "UPDATE `customer_orders`
                SET `courier_service` = ?, `awb_number` = ?, `tracking_url` = ?, `updated_at` = ?
                WHERE `id` = ?";
        return $this->execute($sql, "ssssi", [$courier, $awb, $trackingUrl, $dateNow, $orderId]);
    }

    public function listByStatus($status, $havingCondition, $orderBy, $limit, $offset)
    {
        $sql = "
            SELECT
                co.id AS order_id, co.session_id, co.customer_name, co.created_at AS order_date,
                co.awb_number, co.country, co.product_var_id, co.myr_value_include_postage,
                co.payment_channel, co.status, co.payment_url, co.ship_channel,
                co.courier_service, co.tracking_url,
                ANY_VALUE(c.pv_id) AS pv_id, ANY_VALUE(c.quantity) AS quantity,
                ANY_VALUE(p.name) AS product_name
            FROM customer_orders AS co
            JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
            JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL
            WHERE co.deleted_at IS NULL AND co.status = '{$status}'
            GROUP BY co.id, co.session_id, co.customer_name, co.created_at, co.awb_number
            {$havingCondition}
            {$orderBy}
            LIMIT {$limit} OFFSET {$offset}
        ";
        return $this->query($sql);
    }

    public function listByStatusWithAWB($status, $havingCondition, $orderBy, $limit, $offset)
    {
        $sql = "
            SELECT
                co.id AS order_id, co.session_id, co.customer_name, co.created_at AS order_date,
                co.awb_number, co.country, co.product_var_id, co.myr_value_include_postage,
                co.payment_channel, co.status, co.payment_url, co.ship_channel,
                co.courier_service, co.tracking_url,
                ANY_VALUE(c.pv_id) AS pv_id, ANY_VALUE(c.quantity) AS quantity,
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
            WHERE co.deleted_at IS NULL AND co.status = '{$status}'
            GROUP BY co.id, co.session_id, co.customer_name, co.created_at, co.awb_number
            {$havingCondition}
            {$orderBy}
            LIMIT {$limit} OFFSET {$offset}
        ";
        return $this->query($sql);
    }

    public function countByStatusFiltered($status, $havingCondition = '')
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM (
                SELECT co.id
                FROM customer_orders AS co
                JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
                JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL
                WHERE co.deleted_at IS NULL AND co.status = '{$status}'
                GROUP BY co.id
                {$havingCondition}
            ) AS filtered_orders
        ";
        $rows = $this->query($sql);
        return (int)($rows[0]['total'] ?? 0);
    }

    public function getOrderDetails($id)
    {
        $sql = "
            SELECT `id`, `session_id`, `order_to`, `product_var_id`, `total_qty`, `total_price`, `postage_cost`,
                `currency_sign`, `country_id`, `country`, `state`, `city`, `postcode`, `address_2`, `address_1`,
                `customer_name`, `customer_name_last`, `customer_phone`, `customer_email`, `status`, `payment_channel`, `payment_code`,
                `payment_url`, `ship_channel`, `courier_service`, `awb_number`, `tracking_url`, `created_at`, `updated_at`,
                `deleted_at`, `remark_comment`, `tracking_milestone`, `to_myr_rate`, `myr_value_include_postage`,
                `myr_value_without_postage`
            FROM `customer_orders`
            WHERE `id` = ? AND `deleted_at` IS NULL
        ";
        $rows = $this->query($sql, "i", [$id]);
        return $rows[0] ?? null;
    }

    public function submitToCourier($orderId, $status, $courier, $awb, $trackingUrl, $dateNow)
    {
        $sql = "UPDATE `customer_orders` SET `status` = ?, `courier_service` = ?, `awb_number` = ?, `tracking_url` = ?, `updated_at` = ? WHERE `id` = ?";
        return $this->execute($sql, "issssi", [$status, $courier, $awb, $trackingUrl, $dateNow, $orderId]);
    }

    public function bulkMoveStatus($orderIds, $status, $dateNow)
    {
        if (empty($orderIds)) return false;
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        $types = 'is' . str_repeat('i', count($orderIds));
        $params = array_merge([$status, $dateNow], $orderIds);
        $sql = "UPDATE `customer_orders` SET `status` = ?, `updated_at` = ? WHERE `id` IN ({$placeholders})";
        return $this->execute($sql, $types, $params);
    }

    public function updateCustomerDetails($orderId, $data, $dateNow)
    {
        $sql = "UPDATE `customer_orders` SET
            `state` = ?, `city` = ?, `postcode` = ?, `address_2` = ?, `address_1` = ?,
            `customer_name` = ?, `customer_name_last` = ?, `customer_phone` = ?, `customer_email` = ?,
            `updated_at` = ?
            WHERE `id` = ?";
        return $this->execute($sql, "ssssssssssi", [
            $data['state'], $data['city'], $data['postcode'], $data['address2'], $data['address1'],
            $data['name'], $data['name_last'], $data['phone'], $data['email'],
            $dateNow, $orderId
        ]);
    }

    public function searchOrdersList($whereCondition, $limit, $offset)
    {
        $sql = "
            SELECT
                co.id AS order_id, co.session_id, co.customer_name, co.customer_phone,
                co.customer_email, co.created_at AS order_date, co.awb_number, co.country,
                co.product_var_id, co.myr_value_include_postage, co.payment_channel,
                co.status, co.ship_channel, co.courier_service, co.tracking_url,
                ANY_VALUE(c.pv_id) AS pv_id, ANY_VALUE(c.quantity) AS quantity,
                ANY_VALUE(p.name) AS product_name
            FROM customer_orders AS co
            JOIN cart AS c ON c.session_id = co.session_id AND c.deleted_at IS NULL
            JOIN products AS p ON p.id = c.p_id AND p.deleted_at IS NULL
            {$whereCondition}
            GROUP BY co.id, co.session_id, co.customer_name, co.customer_phone, co.customer_email, co.created_at, co.awb_number, co.country,
                     co.product_var_id, co.myr_value_include_postage, co.payment_channel, co.status, co.ship_channel, co.courier_service, co.tracking_url
            ORDER BY co.created_at DESC
            LIMIT {$limit} OFFSET {$offset}
        ";
        return $this->query($sql);
    }

    public function countSearchResults($whereCondition)
    {
        $sql = "
            SELECT COUNT(DISTINCT co.id) AS total
            FROM customer_orders AS co
            {$whereCondition}
        ";
        $rows = $this->query($sql);
        return (int)($rows[0]['total'] ?? 0);
    }
}
