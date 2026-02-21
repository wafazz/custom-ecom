<?php

require_once __DIR__ . '/BaseModel.php';

class Category extends BaseModel
{
    protected $table = 'categories';

    public function getAll()
    {
        $sql = "SELECT `id`, `name`, `slug`, `image`, `description`, `parent_id`, `sort_order`, `created_at`, `updated_at`, `deleted_at` FROM `categories` WHERE `deleted_at` IS NULL";
        return $this->query($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM `categories` WHERE `id` = ? LIMIT 1";
        $rows = $this->query($sql, "i", [$id]);
        return $rows[0] ?? null;
    }

    public function createCategory($data)
    {
        $sql = "INSERT INTO `categories` (`name`, `slug`, `image`, `description`, `parent_id`, `sort_order`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, NULL, '0', ?, ?)";
        return $this->execute($sql, "ssssss", [$data['name'], $data['slug'], $data['image'], $data['description'] ?? '-', $data['created_at'], $data['updated_at']]);
    }

    public function updateCategory($id, $data)
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

        $sql = "UPDATE `categories` SET " . implode(', ', $sets) . " WHERE `id` = ?";
        return $this->execute($sql, $types, $params);
    }

    public function softDeleteCategory($id, $dateNow)
    {
        $sql = "UPDATE `categories` SET `deleted_at` = ? WHERE `id` = ?";
        return $this->execute($sql, "si", [$dateNow, $id]);
    }

    public function countProducts($id)
    {
        $sql = "SELECT COUNT(*) AS cnt FROM `products` WHERE `category_id` = ? AND `deleted_at` IS NULL";
        $rows = $this->query($sql, "i", [$id]);
        return (int) ($rows[0]['cnt'] ?? 0);
    }

    public function getList()
    {
        $sql = "SELECT `id`, `name`, `slug`, `image` FROM `categories` WHERE `deleted_at` IS NULL";
        return $this->query($sql);
    }

    public function countActive()
    {
        $sql = "SELECT COUNT(*) AS cnt FROM `categories` WHERE `deleted_at` IS NULL";
        $rows = $this->query($sql);
        return (int) ($rows[0]['cnt'] ?? 0);
    }
}
