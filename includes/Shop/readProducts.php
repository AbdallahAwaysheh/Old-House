<?php
class Products extends OldHouseDB
{
    public $products = [];
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
    public function getProductImage($productID)
    {
        $this->connect();
        $sql = "SELECT `img_path` FROM `img_pro` WHERE pro_id = $productID;";
        $result = $this->conn->query($sql);

        if ($result === false) {
            throw new Exception("Query Error: " . $this->conn->error);
        }
        $pro_image = [];
        while ($row = $result->fetch_assoc()) {
            $pro_image[] = $row;
        }
        $this->closeConnection();
        if (isset($pro_image[0])) {
            return $pro_image[0]['img_path'];
        } else {
            // echo "No Image For This Porduct";
            return "/images/defultImage.jpg";
        }
    }

    public function getCatProducts($categoryID)
    {
        $this->connect();
        $sql = "SELECT * FROM `products` WHERE cat_id = $categoryID;";
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

    public function getProductImagesWithNamesLimited($limit)
    {
        $this->connect();
        $sql = "SELECT ip.img_id, ip.img_path, ip.pro_id, p.pro_name
                FROM img_pro ip
                JOIN products p ON ip.pro_id = p.pro_id
                LIMIT ?;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
        $stmt->close();
        $this->closeConnection();
        return $images;
    }
    public function getProductById($productId)
    {
        $this->connect();
        $sql = "SELECT * FROM products WHERE pro_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        $this->closeConnection();
        return $product;
    }
    public function getProductImages($productId)
    {
        $this->connect();
        $sql = "SELECT img_id, img_path, pro_id FROM img_pro WHERE pro_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
        $stmt->close();
        $this->closeConnection();
        return $images;
    }
}
