<?php

require_once __DIR__ . '/BaseModel.php';

class ShopProduct extends BaseModel
{
    protected $table = 'product';

    public function getAllWithCategory($enableField = 'enable_moq')
    {
        $sql = "
            SELECT p.id AS product_id, p.sku AS product_sku, p.product_name AS product_name,
                p.product_image AS product_image, p.selling_price AS selling_price,
                c.cat_name AS category_name
            FROM product p
            JOIN category c ON p.category = c.id
            WHERE p.{$enableField} = '1' AND p.status = '1'
        ";
        return $this->query($sql);
    }

    public function getProductWithCategory($id, $enableField = 'enable_moq')
    {
        $sql = "
            SELECT p.member_point AS member_point, p.product_description AS product_description,
                p.sku AS product_sku, p.product_name AS product_name, p.product_image AS product_image,
                p.selling_price AS selling_price, c.cat_name AS category_name
            FROM product p
            JOIN category c ON p.category = c.id
            WHERE p.id = ? AND p.{$enableField} = '1' AND p.status = '1'
        ";
        $rows = $this->query($sql, "i", [$id]);
        return $rows[0] ?? null;
    }
}
