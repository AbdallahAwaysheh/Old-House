<?php
require "user.inc.php";
$userID = User::$userID;

class Cart extends OldHouseDB
{
    private $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;

        parent::__construct();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[$this->userId]['cart'])) {
            $_SESSION[$this->userId]['cart'] = [];
        }
    }

    public function getProductDetails($productId)
    {
        $query = $this->conn->prepare("
            SELECT 
                products.pro_id,
                products.pro_name,
                products.pro_price,
                img_pro.img_path
            FROM products
            LEFT JOIN img_pro ON products.pro_id = img_pro.pro_id
            WHERE products.pro_id = ?");
        $query->bind_param("i", $productId);
        $query->execute();
        $result = $query->get_result();

        return $result->fetch_assoc();
    }

    public function addToCart($productId, $quantity)
    {
        $product = $this->getProductDetails($productId);

        if ($product) {
            if (!isset($_SESSION[$this->userId]['cart'][$productId])) {
                $_SESSION[$this->userId]['cart'][$productId] = 0;
            }
            $_SESSION[$this->userId]['cart'][$productId] += $quantity;
        }
    }

    public function removeFromCart($productId)
    {
        unset($_SESSION[$this->userId]['cart'][$productId]);
    }

    public function getCartItems()
    {
        $cartItems = [];
        foreach ($_SESSION[$this->userId]['cart'] as $productId => $quantity) {
            $product = $this->getProductDetails($productId);

            if ($product) {
                $product['quantity'] = $quantity;
                $product['total_price'] = $product['pro_price'] * $quantity;
                $cartItems[] = $product;
            }
        }
        return $cartItems;
    }

    public function getCartArray()
    {
        return $_SESSION[$this->userId]['cart'];
    }

    public function getSubtotal()
    {
        $subtotal = 0;
        foreach ($_SESSION[$this->userId]['cart'] as $productId => $quantity) {
            $product = $this->getProductDetails($productId);
            if ($product) {
                $subtotal += $product['pro_price'] * $quantity;
            }
        }
        return $subtotal;
    }
}
