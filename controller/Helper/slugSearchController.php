<?php
namespace Helper;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/Product.php';

class slugSearchController
{
    private $conn;
    private $productModel;

    public function __construct()
    {
        $this->conn = getDbConnection();
        $this->productModel = new \Product($this->conn);
    }

    public function liveSearch()
    {
        $slug = $_POST['slug'] ?? '';
        $productID = $_POST['productId'] ?? '';

        $excludeId = $productID ? (int) $productID : null;
        $exists = $this->productModel->slugExists($slug, $excludeId);

        echo $exists ? "exists" : "available";
    }
}
