<?php
include "./includes/Shop/readProducts.php";

$productsObject = new Products();
$products = $productsObject->readProducts();
?>

<!-- HTML -->
<section class="product-listing">
    <div class="grid-container">
        <h1><?php if (isset($_GET["category_name"])) {
                echo $_GET["category_name"];
            } else {
                echo "All Products";
            } ?></h1>
        <div class="products">

            <?php if (isset($_GET['category_id'])) : ?>
                <?php $productNumberInArray = 0; ?>
                <?php $categoryProducts = $productsObject->getCatProducts($_GET['category_id']); ?>
                <?php if (!empty($products)) : ?>
                    <!-- Loop Over products -->
                    <?php foreach ($categoryProducts as $product) : ?>
                        <form method="POST" action="your_add_to_cart_action_page.php" class="product-card-form">
                            <input type="hidden" name="pro_id" value="<?php echo $product["pro_id"]; ?>">
                            <div class="product-card">
                                <div class="product-image">
                                    <a href="">
                                        <img src=".<?php
                                                    $productImage = $productsObject->getProductImage($product["pro_id"], $productNumberInArray);
                                                    echo $productImage['img_path'];
                                                    $productNumberInArray++;
                                                    ?>">
                                    </a>
                                </div>
                                <div class="product-info">
                                    <h2>
                                        <?php echo $product["productName"]; ?>
                                    </h2>
                                    <p class="product-price">
                                        <?php echo $product["productPrice"]; ?>
                                        <b>JOD</b>
                                    </p>
                                    <button type="submit" class="add-to-cart-button">Add to Cart</button>
                                </div>
                            </div>
                        </form>
                    <?php endforeach; ?>
                <?php else : ?>
                    <h1>There are no Products in this cat</h1>
                <?php endif; ?>
            <?php else : ?>
                <?php if (!empty($products)) : ?>
                    <?php $productNumberInArray = 0; ?>

                    <!-- Loop Over products -->
                    <?php foreach ($products as $product) : ?>
                        <form method="POST" action="your_add_to_cart_action_page.php" class="product-card-form">
                            <input type="hidden" name="pro_id" value="<?php echo $product["pro_id"]; ?>">
                            <div class="product-card">
                                <div class="product-image">
                                    <a href="">
                                        <img src=".<?php
                                                    $productImage = $productsObject->getProductImage($product["pro_id"], $productNumberInArray);
                                                    echo $productImage['img_path'];
                                                    $productNumberInArray++;
                                                    ?>">
                                    </a>
                                </div>
                                <div class="product-info">
                                    <h2>
                                        <?php echo $product["productName"]; ?>
                                    </h2>
                                    <p class="product-price">
                                        <?php echo $product["productPrice"]; ?>
                                        <b>JOD</b>
                                    </p>
                                    <button type="submit" class="add-to-cart-button">Add to Cart</button>
                                </div>
                            </div>
                        </form>
                    <?php endforeach; ?>
                <?php else : ?>
                    <h1>There are no Products</h1>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
</section>