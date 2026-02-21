<?php

require_once __DIR__ . '/BaseModel.php';

class StockControl extends BaseModel
{
    protected $table = 'stock_control';

    public function addStock($productId, $variantId, $qty, $comment, $dateNow)
    {
        $sql = "INSERT INTO `stock_control` (`p_id`, `pv_id`, `stock_in`, `stock_out`, `created_at`, `updated_at`, `deleted_at`, `comment`) VALUES (?, ?, ?, '0', ?, ?, NULL, ?)";
        return $this->execute($sql, "iiisss", [$productId, $variantId, $qty, $dateNow, $dateNow, $comment]);
    }

    public function deductStock($productId, $variantId, $qty, $comment, $dateNow)
    {
        $sql = "INSERT INTO `stock_control` (`p_id`, `pv_id`, `stock_in`, `stock_out`, `created_at`, `updated_at`, `deleted_at`, `comment`) VALUES (?, ?, '0', ?, ?, ?, NULL, ?)";
        return $this->execute($sql, "iiisss", [$productId, $variantId, $qty, $dateNow, $dateNow, $comment]);
    }

    public function getStockSummary()
    {
        $sql = "
            SELECT
                p.id AS product_id,
                p.name AS product_name,
                p.slug,
                p.description,
                p.type,
                p.category_id,
                p.brand_id,
                p.price_capital,
                p.status AS product_status,

                pv.id AS variant_id,
                pv.variant_name,
                pv.sku,
                pv.price_retail,
                pv.price_sale,
                pv.stock AS variant_stock,
                pv.image AS variant_image,
                pv.max_purchase,

                (
                    SELECT pi.image
                    FROM product_image pi
                    WHERE pi.product_id = p.id
                    ORDER BY pi.id ASC
                    LIMIT 1
                ) AS product_image,

                IFNULL(SUM(sc.stock_in), 0) AS total_stock_in,
                IFNULL(SUM(sc.stock_out), 0) AS total_stock_out,

                IFNULL((
                    SELECT SUM(c.quantity)
                    FROM cart c
                    WHERE c.pv_id = pv.id AND c.status IN (0,1)
                ), 0) AS stock_reserved,

                IFNULL((
                    SELECT SUM(c.quantity)
                    FROM cart c
                    WHERE c.pv_id = pv.id AND c.status = 1
                ), 0) AS total_sold,

                (IFNULL(SUM(sc.stock_in), 0) - IFNULL(SUM(sc.stock_out), 0) -
                    IFNULL((
                        SELECT SUM(c.quantity)
                        FROM cart c
                        WHERE c.pv_id = pv.id AND c.status IN (0,1)
                    ), 0)
                ) AS physical_stock

            FROM product_variants pv
            JOIN products p ON pv.product_id = p.id
            LEFT JOIN stock_control sc ON pv.id = sc.pv_id

            WHERE p.deleted_at IS NULL AND pv.deleted_at IS NULL

            GROUP BY pv.id
            ORDER BY p.id, pv.id
        ";
        return $this->query($sql);
    }
}
