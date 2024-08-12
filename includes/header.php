<?php
include "./includes/Shop/Cats.php";
include("includes/Shop/readProducts.php");
include("./includes/Shop/Cart.inc.php");
$categoriesClass = new Categories();
$categories = $categoriesClass->readCats();

if (isset($userID)) {
    $cart = new Cart($userID);
    $cartItems = $cart->getCartItems($userID);
    $cartarray = $cart->getCartCount();
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


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="http://localhost/learnphp/Old-House/css/style.css?v<?php echo time(); ?>">
    <link rel="stylesheet" href="http://localhost/learnphp/Old-House/css/sections-styles.css?v<?php echo time(); ?>">
    <link rel="stylesheet" href="http://localhost/learnphp/Old-House/css/aboutUs.css?v<?php echo time(); ?>">
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
                    <a class="nav-link" href="aboutUs.inc.php">About us</a>
                </div>
                <div class="icons-container">
                    <?php if (!isset($userID)) : ?>
                        <a href="http://localhost/learnphp/Old-House/adminDashOldHouse/login/login.php"><img src="http://localhost/learnphp/Old-House/images/login.svg" alt="Profile"></a>
                    <?php else : ?>
                        <a href="http://localhost/learnphp/Old-House/includes/Shop/logout.php"><img src="http://localhost/learnphp/Old-House/images/logout.svg" alt="Profile"></a>
                    <?php endif; ?>
                    <a href="./cart.php"><img src="./images/cart.svg" alt="Shop"></a>
                    <span class="productCounts"><?php
                                                if (isset($cartarray)) {
                                                    echo $cartarray;
                                                }
                                                ?></span>
                </div>
            </div>
        </nav>