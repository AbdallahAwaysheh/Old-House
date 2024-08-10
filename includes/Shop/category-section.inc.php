<?php

?>
<!-- HTML -->
<section class="category-section">
    <div class="container">
        <h1>Categories</h1>
        <div class="all-categories">
            <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="get">
                <input type="hidden" name="category_name" value="All Products">
                <button type="submit" class="category-button-all">All</button>
            </form>
        </div>
        <div class="cats-container">
            <!-- categories loop -->
            <?php if (!empty($categories)) : ?>
                <?php foreach ($categories as $category) : ?>
                    <?php if ($category['delete_status'] == "no") : ?>
                        <div class="cat-item">
                            <form method="GET" action="<?php echo $_SERVER["PHP_SELF"] ?>">
                                <input type="hidden" name="category_id" value="<?php echo $category["cat_id"]; ?>">
                                <input type="hidden" name="category_name" value="<?php echo $category["cat_name"]; ?>">
                                <button type="submit" class="category-button">
                                <img src="./adminDashOldHouse/uploads/<?php echo $category["cat_img"]; ?>" alt="<?php echo $category["cat_id"]; ?>">
                                </button>
                                <p style="text-align: center;"><?php echo $category["cat_name"]; ?></p>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else : ?>
                <h1>There are no Categories</h1>
            <?php endif; ?>
            <!-- categories loop end -->
        </div>
    </div>
    
</section>