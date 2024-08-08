<?php

include("./connect.php");
include("./Shop/Cart.inc.php");

$cart = new Cart($userID);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id_button']) && isset($_POST['product_quantity_button'])) {
        $productId = intval($_POST['product_id_button']);
        $cart->addToCart($productId, 1);
        echo "<pre>";
        var_dump($_SESSION['user_id']);
        echo "</pre>";
        if (isset($_SERVER['HTTP_REFERER'])) {
            // header("Location: " . $_SERVER['HTTP_REFERER']);
        }
    }
} else {
    header("Location: " . $_SERVER['HTTP_REFERER']);
}
