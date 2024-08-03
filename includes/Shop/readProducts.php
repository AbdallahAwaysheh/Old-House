<?php
class Products extends OldHouseDB
{
    public $products = [];
    public $pro_images = [];
    public $categoryProducts = [];


    public function readProducts()
    {
        $this->connect();
        $sql = "SELECT * FROM products;";
        $result = $this->conn->query($sql);

        if ($result === false) {
            throw new Exception("Query Error: " . $this->conn->error);
        }

        while ($row = $result->fetch_assoc()) {
            $this->products[] = $row;
        }

        $this->closeConnection();
        return $this->products;
    }
    public function getProductImage($productID, $productNumberInArray)
    {
        $this->connect();
        $sql = "SELECT `img_id`, `img_path`, `pro_id` FROM `img_pro` WHERE pro_id = $productID;";
        $result = $this->conn->query($sql);

        if ($result === false) {
            throw new Exception("Query Error: " . $this->conn->error);
        }

        while ($row = $result->fetch_assoc()) {
            $this->pro_images[] = $row;
        }
        $this->closeConnection();
        return $this->pro_images[$productNumberInArray];
    }
    public function getCatProducts($categoryID)
    {
        $this->connect();
        $sql = "SELECT * FROM `products` WHERE category = $categoryID;";
        $result = $this->conn->query($sql);

        if ($result === false) {
            throw new Exception("Query Error: " . $this->conn->error);
        }

        while ($row = $result->fetch_assoc()) {
            $this->categoryProducts[] = $row;
        }
        $this->closeConnection();
        return $this->categoryProducts;
    }
}
