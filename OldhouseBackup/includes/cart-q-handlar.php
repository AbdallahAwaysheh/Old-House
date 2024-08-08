<?php
include("./connect.php");
include("./Shop/Cart.inc.php");
$database = new OldHouseDB();
$conn = $database->conn;

$cart = new Cart($conn);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['product_id'])) {
        $productId = $_POST['product_id'];
        if (isset($_POST['add'])) {
            $cart->addToCart($userID, $productId, 1);
            header("Location: ../cart.php");
            exit();
        }
        if (isset($_POST['remove'])) {
            if ($_POST['product_quantity'] == 1) {
                $cart->removeFromCart($userID, $productId);
                header("Location: ../cart.php");
                exit();
            } else {

                $cart->addToCart($userID, $productId, -1);
            }
        }
    }
}

if (isset($_GET['remove'])) {
    $productId = intval($_GET['remove']);
    $cart->removeFromCart($userID, $productId);
    header('Location: ../cart.php');
    exit();
}
header('Location: ../cart.php');
