<?php

require_once __DIR__ . '/BaseModel.php';

class NewsBlog extends BaseModel
{
    protected $table = 'news_blog';

    public function getAll()
    {
        $sql = "SELECT * FROM `news_blog` WHERE `deleted_at` IS NULL ORDER BY id DESC";
        return $this->query($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM `news_blog` WHERE `id` = ? AND `deleted_at` IS NULL LIMIT 1";
        $rows = $this->query($sql, "i", [$id]);
        return $rows[0] ?? null;
    }

    public function createPost($data)
    {
        $sql = "INSERT INTO `news_blog` (`post_by`, `update_by`, `title`, `contents`, `created_at`, `updated_at`, `deleted_at`, `reader`) VALUES (?, '', ?, ?, ?, ?, NULL, '')";
        return $this->execute($sql, "issss", [$data['post_by'], $data['title'], $data['contents'], $data['created_at'], $data['updated_at']]);
    }

    public function updatePost($id, $data)
    {
        $sql = "UPDATE `news_blog` SET `update_by` = ?, `title` = ?, `contents` = ?, `updated_at` = ? WHERE `id` = ?";
        return $this->execute($sql, "ssssi", [$data['update_by'], $data['title'], $data['contents'], $data['updated_at'], $id]);
    }

    public function softDeletePost($id, $updateBy, $dateNow)
    {
        $sql = "UPDATE `news_blog` SET `update_by` = ?, `updated_at` = ?, `deleted_at` = ? WHERE `id` = ?";
        return $this->execute($sql, "sssi", [$updateBy, $dateNow, $dateNow, $id]);
    }

    public function getPaginated($limit, $offset)
    {
        $sql = "SELECT `id`, `post_by`, `update_by`, `title`, `contents`, `created_at`, `updated_at`, `deleted_at`, `reader` FROM `news_blog` WHERE `deleted_at` IS NULL ORDER BY `created_at` DESC LIMIT ? OFFSET ?";
        return $this->query($sql, "ii", [$limit, $offset]);
    }
}
