<?php

require_once __DIR__ . '/BaseModel.php';

class Brand extends BaseModel
{
    protected $table = 'brands';

    public function getAll()
    {
        $sql = "SELECT `id`, `name`, `slug`, `image`, `description`, `created_at`, `updated_at`, `deleted_at` FROM `brands` WHERE `deleted_at` IS NULL";
        return $this->query($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM `brands` WHERE `id` = ? LIMIT 1";
        $rows = $this->query($sql, "i", [$id]);
        return $rows[0] ?? null;
    }

    public function createBrand($data)
    {
        $sql = "INSERT INTO `brands` (`name`, `slug`, `image`, `description`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?)";
        return $this->execute($sql, "ssssss", [$data['name'], $data['slug'], $data['image'], $data['description'] ?? '-', $data['created_at'], $data['updated_at']]);
    }

    public function updateBrand($id, $data)
    {
        $sets = [];
        $types = '';
        $params = [];

        foreach ($data as $col => $val) {
            $sets[] = "`{$col}` = ?";
            $types .= 's';
            $params[] = $val;
        }

        $types .= 'i';
        $params[] = $id;

        $sql = "UPDATE `brands` SET " . implode(', ', $sets) . " WHERE `id` = ?";
        return $this->execute($sql, $types, $params);
    }

    public function softDeleteBrand($id, $dateNow)
    {
        $sql = "UPDATE `brands` SET `deleted_at` = ? WHERE `id` = ?";
        return $this->execute($sql, "si", [$dateNow, $id]);
    }

    public function countProducts($id)
    {
        $sql = "SELECT COUNT(*) AS cnt FROM `products` WHERE `brand_id` = ? AND `deleted_at` IS NULL";
        $rows = $this->query($sql, "i", [$id]);
        return (int) ($rows[0]['cnt'] ?? 0);
    }

    public function getList()
    {
        $sql = "SELECT `id`, `name`, `slug`, `image` FROM `brands` WHERE `deleted_at` IS NULL";
        return $this->query($sql);
    }

    public function countActive()
    {
        $sql = "SELECT COUNT(*) AS cnt FROM `brands` WHERE `deleted_at` IS NULL";
        $rows = $this->query($sql);
        return (int) ($rows[0]['cnt'] ?? 0);
    }
}
