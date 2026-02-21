<?php

require_once __DIR__ . '/BaseModel.php';

class ProductVariant extends BaseModel
{
    protected $table = 'product_variants';

    public function getById($id)
    {
        $sql = "SELECT * FROM `product_variants` WHERE `id` = ? LIMIT 1";
        $rows = $this->query($sql, "i", [$id]);
        return $rows[0] ?? null;
    }

    public function checkSkuUnique($sku, $excludeId = 0)
    {
        $sql = "SELECT `id` FROM `product_variants` WHERE `sku` = ? AND `id` != ? AND `deleted_at` IS NULL";
        $rows = $this->query($sql, "si", [$sku, $excludeId]);
        return empty($rows);
    }

    public function createVariant($data)
    {
        $sql = "INSERT INTO `product_variants` (`product_id`, `variant_name`, `sku`, `price_retail`, `price_sale`, `stock`, `image`, `max_purchase`, `status`, `created_at`, `updated_at`) VALUES (?, ?, ?, '0.00', '0.00', '0', '-', ?, '1', ?, ?)";
        $this->execute($sql, "ississ", [
            $data['product_id'],
            $data['variant_name'],
            $data['sku'],
            $data['max_purchase'],
            $data['created_at'],
            $data['updated_at'],
        ]);
        return $this->conn->insert_id;
    }

    public function updateVariant($id, $productId, $data)
    {
        $sql = "UPDATE `product_variants` SET `variant_name` = ?, `sku` = ?, `max_purchase` = ?, `updated_at` = ? WHERE `id` = ? AND `product_id` = ?";
        return $this->execute($sql, "ssissi", [$data['variant_name'], $data['sku'], $data['max_purchase'], $data['updated_at'], $id, $productId]);
    }

    public function updateByProduct($productId, $data)
    {
        $sql = "UPDATE `product_variants` SET `sku` = ?, `max_purchase` = ?, `status` = ?, `updated_at` = ? WHERE `product_id` = ? AND `deleted_at` IS NULL";
        return $this->execute($sql, "sissi", [$data['sku'], $data['max_purchase'], $data['status'], $data['updated_at'], $productId]);
    }

    public function softDeleteExcept($productId, $keepIds, $dateNow)
    {
        if (empty($keepIds)) return false;
        $placeholders = implode(',', array_fill(0, count($keepIds), '?'));
        $types = 'i' . str_repeat('i', count($keepIds)) . 's';
        $params = array_merge([$productId], $keepIds, [$dateNow]);
        $sql = "UPDATE `product_variants` SET `status` = 2, `deleted_at` = ? WHERE `product_id` = ? AND `id` NOT IN ({$placeholders}) AND `deleted_at` IS NULL";
        // Reorder: dateNow first, then productId, then keepIds
        $types = 's' . 'i' . str_repeat('i', count($keepIds));
        $params = array_merge([$dateNow, $productId], $keepIds);
        return $this->execute($sql, $types, $params);
    }

    public function softDeleteByProduct($productId, $dateNow)
    {
        $sql = "UPDATE `product_variants` SET `status` = '2', `deleted_at` = ? WHERE `product_id` = ?";
        return $this->execute($sql, "si", [$dateNow, $productId]);
    }
}
