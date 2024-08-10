<?php
require "user.inc.php";
$user = new User();
$userID = $user->userID;

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

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (!isset($_SESSION['cart'][$this->userId])) {
            $_SESSION['cart'][$this->userId] = [];
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


        if ($product && $productId) {

            if (!isset($_SESSION['cart'][$this->userId][$productId])) {
                $_SESSION['cart'][$this->userId][$productId] = 0;
            }
            $_SESSION['cart'][$this->userId][$productId] += $quantity;
        }
    }

    public function removeFromCart($productId)
    {
        unset($_SESSION['cart'][$this->userId][$productId]);
    }

    public function getCartItems($userID)
    {
        $cartItems = [];
        foreach ($_SESSION['cart'][$this->userId] as $productId => $quantity) {
            $product = $this->getProductDetails($productId);
            if ($product !== null) {
                if ($product) {
                    $product['quantity'] = $quantity;
                    $product['user_id'] = $this->userId;
                    $product['total_price'] = $product['pro_price'] * $quantity;
                    $cartItems[] = $product;
                }
            }
        }

        return $cartItems;
    }

    public function getCartCount()
    {
        $count = 0;
        foreach ($_SESSION['cart'][$this->userId] as $quantity) {
            $count += $quantity;
        }
        return $count;
    }


    public function getSubtotal($userId)
    {
        $subtotal = 0;
        foreach ($_SESSION['cart'][$userId] as $productId => $quantity) {
            $product = $this->getProductDetails($productId);
            if ($product) {
                $subtotal += $product['pro_price'] * $quantity;
            }
        }
        return $subtotal;
    }
}
