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
}
