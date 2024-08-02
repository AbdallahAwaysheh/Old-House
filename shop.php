<?php include("./includes/header.php"); ?>
<div class="location">
	<h1>Our Products</h1>
</div>
</header>
<main>
	<section class="category-section">
		<div class="container">
			<h1>Categories</h1>
			<div class="categories">
				<button class="category-button">All</button>
				<!-- categories loop -->
				<?php if (!empty($categories)) : ?>
					<?php foreach ($categories as $category) : ?>
						<button class="category-button">
							<?php echo $category["cat_name"]; ?>
						</button>
					<?php endforeach; ?>
				<?php else : ?>
					<h1>There is no Categories</h1>
				<?php endif; ?>
				<!-- categories loop end-->
			</div>
		</div>
	</section>
	<section class="product-listing">
		<div class="grid-container">
			<h1>Our Products</h1>
			<div class="products">
				<?php if (!empty($products)) : ?>
					<!--  product cards -->
					<!-- Loop Over products -->
					<?php foreach ($products as $product) : ?>
						<div class="product-card">
							<div class="product-image">
								<img src="<?php echo $product[" pro_img"]; ?>" alt="Product 1">
							</div>
							<div class="product-info">
								<h2>
									<?php echo $product["pro_name"]; ?>
								</h2>
								<p class="product-price">
									<?php echo $product["pro_des"]; ?>
								</p>
								<p class="product-price">
									<?php echo $product["pro_price"]; ?>
								</p>
								<a href="#" class="add-to-cart-button">Add to Cart</a>
							</div>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<h1>There is no Products</h1>
				<?php endif; ?>
			</div>
		</div>
	</section>
</main>
<?php include("./includes/footer.php"); ?>
<script src="./js/index.js"></script>
</body>

</html>