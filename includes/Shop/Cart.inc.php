<?php

class Cart extends OldHouseDB
{
    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
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
            $_SESSION['cart'][$productId] = $quantity;
        }
    }

    public function removeFromCart($productId)
    {
        unset($_SESSION['cart'][$productId]);
    }

    public function getCartItems()
    {
        $cartItems = [];
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = $this->getProductDetails($productId);
            if ($product) {
                $product['quantity'] = $quantity;
                $product['total_price'] = $product['pro_price'] * $quantity;
                $cartItems[] = $product;
            }
        }
        return $cartItems;
    }

    public function getSubtotal()
    {
        $subtotal = 0;
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = $this->getProductDetails($productId);
            if ($product) {
                $subtotal += $product['pro_price'] * $quantity;
            }
        }
        return $subtotal;
    }
}
