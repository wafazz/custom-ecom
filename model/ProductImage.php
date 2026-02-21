<?php

require_once __DIR__ . '/BaseModel.php';

class ProductImage extends BaseModel
{
    protected $table = 'product_image';

    public function getByProductId($productId)
    {
        $sql = "SELECT * FROM `product_image` WHERE `product_id` = ?";
        return $this->query($sql, "i", [$productId]);
    }

    public function addImage($productId, $image, $dateNow)
    {
        $sql = "INSERT INTO `product_image` (`product_id`, `image`, `created_at`) VALUES (?, ?, ?)";
        return $this->execute($sql, "iss", [$productId, $image, $dateNow]);
    }

    public function deleteOrphanImages($productId, $keepImages)
    {
        $allImages = $this->getByProductId($productId);
        foreach ($allImages as $row) {
            if (!in_array($row['image'], $keepImages)) {
                $sql = "DELETE FROM `product_image` WHERE `product_id` = ? AND `image` = ?";
                $this->execute($sql, "is", [$productId, $row['image']]);
            }
        }
    }
}
