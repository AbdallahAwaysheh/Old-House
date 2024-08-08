<?php
include "./includes/Shop/Cats.php";
include("includes/Shop/readProducts.php");
include("./includes/Shop/Cart.inc.php");
$categoriesClass = new Categories();
$categories = $categoriesClass->readCats();

if (isset($userID)) {
    $cart = new Cart($userID);
    $cartItems = $cart->getCartItems($userID);
    $cartarray = $cart->getCartArray($userID);
    var_dump($cartarray);
    $subtotal = $cart->getSubtotal($userID);
    $tax = $subtotal * 0.1;
    $total = $subtotal + $tax;
} else {
    $subtotal = 0;
    $tax = 0;
    $total = 0;
}
$productsClass = new Products();
$images = $productsClass->getProductImagesWithNamesLimited(12);


$imagesPerSlide = 3;
$totalImages = count($images);
$totalSlides = ceil($totalImages / $imagesPerSlide);
if (isset($productId)) {

    $product = $productsClass->getProductById($productId);
    $productImages = $productsClass->getProductImages($productId);
}
// var_dump("<h1> $userID </h1>");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/sections-styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/freeps2/a7rarpress@main/swiper-bundle.min.css">
    <title>landingPage</title>
</head>

<body>
    <header>
        <nav class="bg-green">
            <div class="logo">
                <img src="./images/logo.png" alt="Logo">
            </div>
            <div class="container">
                <button id="toggleNavButton" class="toggle-nav-button">☰</button>
                <div class="nav-links-container" id="navLinks">
                    <a class="nav-link" href="index.php">Home</a>
                    <a class="nav-link" href="shop.php">Shop</a>
                    <a class="nav-link" href="about.html">About us</a>
                    <a class="nav-link" href="services.html">Services</a>
                    <a class="nav-link" href="contact.html">Contact us</a>
                </div>
                <div class="icons-container">
                    <a href="#"><img src="./images/person-1.jpg" alt="Profile"></a>
                    <a href="./cart.php"><img src="./images/cart.svg" alt="Shop"></a>
                    <span><?php
                            if (isset($cartarray)) {
                                echo array_sum($cartarray);
                            }
                            ?></span>
                </div>
            </div>
        </nav>