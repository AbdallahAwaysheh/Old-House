<?php
include("./connect.php");
include("./Shop/Cart.inc.php");
$database = new OldHouseDB();
$conn = $database->conn;

$cart = new Cart($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && isset($_POST['product_quantity'])) {
        $productId = intval($_POST['product_id']);
        $quantity = intval($_POST['product_quantity']);
        if ($quantity > 0) {
            $cart->addToCart($productId, $quantity);
        } else {
            $cart->removeFromCart($productId);
        }
        header("Location: ../cart.php");
        exit();
    }
}
if (isset($_POST['product_id_button']) && isset($_POST['product_quantity_button'])) {
    $productId = intval($_POST['product_id']);
    if ($quantity > 0) {
        $cart->addToCart($productId, $quantity);
    } else {
        $cart->removeFromCart($productId);
    }
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
    exit();
}
if (isset($_GET['remove'])) {
    $productId = intval($_GET['remove']);
    $cart->removeFromCart($productId);
    header('Location: ../cart.php');
    exit();
}
header('Location: ../cart.php');
