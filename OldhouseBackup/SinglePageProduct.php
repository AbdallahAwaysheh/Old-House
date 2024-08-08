<?php
$productId = $_GET['pro_id'];


?>

<?php include "./includes/header.php"; ?>
<div class="product-container">
    <div class="images-container">
        <div class="product-main-image">
            <img id="mainImage" src=".<?php echo htmlspecialchars($productImages[0]['img_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['pro_name'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="product-images">
            <?php
            unset($productImages[0]);
            foreach ($productImages as $index => $image) : ?>
                <img class="thumbnail" data-index="<?php echo $index; ?>" src=".<?php echo htmlspecialchars($image['img_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['pro_name'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php endforeach; ?>
        </div>
    </div>


    <div class="product-details">
        <h1><?php echo htmlspecialchars($product['pro_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="product-price">$<?php echo htmlspecialchars($product['pro_price'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p class="product-description">
            <?php echo $product['pro_desc']; ?>
        </p>

        <!-- Add to Cart Form -->
        <form action="includes/cart-handler.php" method="post">
            <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['pro_name'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($product['pro_price'], ENT_QUOTES, 'UTF-8'); ?>">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" class="quantity" name="product_quantity" value="1" min="1">
            <button type="submit" class="add-to-cart-button">Add to Cart</button>
        </form>
    </div>
</div>
<?php include "./includes/footer.php"; ?>
<script src="http://localhost/learningPHP/Old-House/js/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>