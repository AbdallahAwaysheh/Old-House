<?php
include "./includes/Shop/Cats.php";

$categoriesClass = new Categories();
$categories = $categoriesClass->readCats();
?>
<!-- HTML -->
<section class="category-section">
    <div class="container">
        <h1>Categories</h1>
        <div class="categories">
            <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="get">
                <input type="hidden" name="category_name" value="All Products">
                <button type="submit" class="category-button">All</button>
            </form>
            <!-- categories loop -->
            <?php if (!empty($categories)) : ?>
                <?php foreach ($categories as $category) : ?>
                    <form method="GET" action="<?php echo $_SERVER["PHP_SELF"] ?>">
                        <input type="hidden" name="category_id" value="<?php echo $category["cat_id"]; ?>">
                        <input type="hidden" name="category_name" value="<?php echo $category["cat_name"]; ?>">
                        <button type="submit" class="category-button">
                            <?php echo $category["cat_name"]; ?>
                        </button>
                    </form>
                <?php endforeach; ?>
            <?php else : ?>
                <h1>There are no Categories</h1>
            <?php endif; ?>
            <!-- categories loop end -->
        </div>
    </div>
</section>