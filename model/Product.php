<?php

require_once __DIR__ . '/BaseModel.php';

class Product extends BaseModel
{
    protected $table = 'products';

    /**
     * Get product with its first variant (replaces GetProductDetails()).
     */
    public function getWithVariant(int $productId): ?array
    {
        $sql = "
            SELECT
                p.id AS product_id,
                p.name,
                p.slug,
                p.description,
                p.type,
                p.category_id,
                p.brand_id,
                p.price_capital,
                p.status AS product_status,
                p.weight,
                p.length,
                p.width,
                p.height,
                p.created_at AS product_created,
                p.updated_at AS product_updated,
                p.deleted_at AS product_deleted,

                pv.id AS variant_id,
                pv.sku,
                pv.price_retail,
                pv.price_sale,
                pv.stock,
                pv.image,
                pv.max_purchase,
                pv.status AS variant_status,
                pv.created_at AS variant_created,
                pv.updated_at AS variant_updated,
                pv.deleted_at AS variant_deleted

            FROM products p
            LEFT JOIN product_variants pv ON p.id = pv.product_id
            WHERE p.id = ? AND p.deleted_at IS NULL
            LIMIT 1
        ";
        $rows = $this->query($sql, "i", [$productId]);
        if (empty($rows)) {
            return null;
        }
        $row = $rows[0];
        return [
            "name" => $row["name"],
            "slug" => $row["slug"],
            "description" => $row["description"],
            "brand_id" => $row["brand_id"],
            "category_id" => $row["category_id"],
            "max_purchase" => $row["max_purchase"],
            "sku" => $row["sku"],
            "price_capital" => $row["price_capital"],
            "variant_id" => $row["variant_id"],
            "weight" => $row["weight"],
            "length" => $row["length"],
            "width" => $row["width"],
            "height" => $row["height"],
        ];
    }

    /**
     * Get stock balance for a product (replaces stockBalanceIndividual()).
     */
    public function getStockBalance(int $productId): ?array
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

                pv.id AS variant_id,
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

            WHERE p.id = ? AND p.deleted_at IS NULL AND pv.deleted_at IS NULL

            GROUP BY pv.id
            ORDER BY p.id, pv.id
        ";
        $rows = $this->query($sql, "i", [$productId]);
        if (empty($rows)) {
            return null;
        }
        $row = $rows[0];
        return [
            "sku" => $row["sku"],
            "total_stock_in" => $row["total_stock_in"],
            "total_stock_out" => $row["total_stock_out"],
            "physical_stock" => $row["physical_stock"],
            "max_purchase" => $row["max_purchase"],
        ];
    }

    /**
     * Get country-specific price for a product (replaces getPriceOnCountry()).
     */
    public function getPriceForCountry(int $countryId, int $productId): ?array
    {
        $sql = "SELECT * FROM `list_country_product_price`
                WHERE `country_id` = ? AND `product_id` = ?";
        $rows = $this->query($sql, "ii", [$countryId, $productId]);
        if (empty($rows)) {
            return null;
        }
        $row = $rows[0];
        return [
            "market" => $row["market_price"],
            "sale" => $row["sale_price"],
        ];
    }

    /**
     * Get all images for a product (replaces getAllProductImage()).
     */
    public function getImages(int $productId): array
    {
        $sql = "SELECT * FROM `product_image` WHERE `product_id` = ?";
        return $this->query($sql, "i", [$productId]);
    }

    /**
     * Get the first image for a product (replaces getProductImageSingle()).
     */
    public function getFirstImage(int $productId): ?array
    {
        $sql = "SELECT * FROM `product_image`
                WHERE `product_id` = ? ORDER BY `id` ASC LIMIT 1";
        $rows = $this->query($sql, "i", [$productId]);
        if (empty($rows)) {
            return null;
        }
        return [
            "image" => $rows[0]["image"],
        ];
    }

    /**
     * Get newest active products (replaces newProduct()).
     */
    public function getNewest(int $limit = 8): array
    {
        $sql = "SELECT * FROM `products`
                WHERE `status` = '1' AND `deleted_at` IS NULL
                ORDER BY `created_at` DESC
                LIMIT ?";
        return $this->query($sql, "i", [$limit]);
    }

    public function getByCategoryId($categoryId)
    {
        $sql = "SELECT * FROM `products` WHERE `category_id` = ? AND `status` = '1' AND `deleted_at` IS NULL ORDER BY `created_at`";
        return $this->query($sql, "s", [$categoryId]);
    }

    public function getByBrandId($brandId)
    {
        $sql = "SELECT * FROM `products` WHERE `brand_id` = ? AND `status` = '1' AND `deleted_at` IS NULL ORDER BY `created_at`";
        return $this->query($sql, "s", [$brandId]);
    }

    public function getPromoItems($countryId, $limit = 20)
    {
        $sql = "
            SELECT
                p.id AS product_id, p.name AS product_name, p.slug,
                cpp.market_price, cpp.sale_price, cpp.country_id
            FROM list_country_product_price cpp
            JOIN products p ON cpp.product_id = p.id
            WHERE cpp.country_id = ? AND cpp.market_price > cpp.sale_price
                AND p.status = '1' AND p.deleted_at IS NULL
            ORDER BY (cpp.market_price - cpp.sale_price) DESC
            LIMIT ?
        ";
        return $this->query($sql, "si", [$countryId, $limit]);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM `products` WHERE `id` = ?";
        $rows = $this->query($sql, "i", [$id]);
        return $rows[0] ?? null;
    }

    public function slugExists($slug, $excludeId = null)
    {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) AS cnt FROM `products` WHERE `id` != ? AND `slug` = ?";
            $rows = $this->query($sql, "is", [$excludeId, $slug]);
        } else {
            $sql = "SELECT COUNT(*) AS cnt FROM `products` WHERE `slug` = ?";
            $rows = $this->query($sql, "s", [$slug]);
        }
        return (int) ($rows[0]['cnt'] ?? 0) > 0;
    }
}
