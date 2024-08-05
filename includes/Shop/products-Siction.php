<?php
include("./includes/Shop/readProducts.php");

$productsObject = new Products();
$products = $productsObject->readProducts();
?>

<!-- HTML -->
<section class="product-listing fade">
    <div class="grid-container">
        <h1><?php if (isset($_GET["category_name"])) {
                echo htmlspecialchars($_GET["category_name"]);
            } else {
                echo "All Products";
            } ?></h1>
        <div class="products">

            <?php if (isset($_GET['category_id'])) : ?>

                <?php $categoryProducts = $productsObject->getCatProducts($_GET['category_id']); ?>
                <?php if (!empty($categoryProducts)) : ?>
                    <!-- Loop Over products -->
                    <?php foreach ($categoryProducts as $product) : ?>
                        <form method="POST" action="./includes/add-to-cart-handler.php" class="product-card-form">
                            <input type="hidden" name="product_id_button" value="<?php echo htmlspecialchars($product["pro_id"]); ?>">
                            <input type="hidden" name="product_quantity_button" value="1">
                            <div class="product-card">
                                <div class="product-image">
                                    <a href="SinglePageProduct.php?pro_id=<?php echo htmlspecialchars($product['pro_id']); ?>">
                                        <img src=".<?php
                                                    $productImage = $productsObject->getProductImage($product["pro_id"]);
                                                    echo htmlspecialchars($productImage, ENT_QUOTES, 'UTF-8');
                                                    ?>">
                                    </a>
                                </div>
                                <div class="product-info">
                                    <h2>
                                        <?php echo htmlspecialchars($product["pro_name"], ENT_QUOTES, 'UTF-8'); ?>
                                    </h2>
                                    <p class="product-price">
                                        <?php echo htmlspecialchars($product["pro_price"], ENT_QUOTES, 'UTF-8'); ?>
                                        <b>JOD</b>
                                    </p>
                                    <button type="submit" class="add-to-cart-button">Add to Cart</button>
                                </div>
                            </div>
                        </form>
                    <?php endforeach; ?>
                <?php else : ?>
                    <h1>There are no Products in this category</h1>
                <?php endif; ?>
            <?php else : ?>
                <?php if (!empty($products)) : ?>
                    <!-- Loop Over products -->
                    <?php foreach ($products as $product) : ?>
                        <form method="POST" action="./includes/add-to-cart-handler.php" class="product-card-form">
                            <input type="hidden" name="product_id_button" value="<?php echo htmlspecialchars($product["pro_id"]); ?>">
                            <input type="hidden" name="product_quantity_button" value="1">
                            <div class="product-card">
                                <div class="product-image">
                                    <a href="SinglePageProduct.php?pro_id=<?php echo htmlspecialchars($product['pro_id']); ?>">
                                        <img src=".<?php
                                                    $productImage = $productsObject->getProductImage($product["pro_id"]);
                                                    echo htmlspecialchars($productImage, ENT_QUOTES, 'UTF-8');
                                                    ?>">
                                    </a>
                                </div>
                                <div class="product-info">
                                    <h2>
                                        <?php echo htmlspecialchars($product["pro_name"], ENT_QUOTES, 'UTF-8'); ?>
                                    </h2>
                                    <p class="product-price">
                                        <?php echo htmlspecialchars($product["pro_price"], ENT_QUOTES, 'UTF-8'); ?>
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