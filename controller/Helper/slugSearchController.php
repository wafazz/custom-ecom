<?php
namespace Helper;

require_once __DIR__ . '/../../config/mainConfig.php';

class slugSearchController
{
    public function liveSearch()
    {
        $conn = getDbConnection();

        $slug = $_POST['slug'] ?? '';
        $productID = $_POST['productId'] ?? '';

        if ($productID) {
            $stmt = $conn->query("SELECT * FROM products WHERE id != '$productID' AND slug = '$slug'");
            //$stmt->bind_param("si", $slugText, $productID);
            $xx = 1;
        } else {
            $stmt = $conn->query("SELECT * FROM products WHERE slug = '$slug'");
            $xx = 2;
        }

       

        echo ($stmt->num_rows > 0) ? "exists" : "available";
    }
}