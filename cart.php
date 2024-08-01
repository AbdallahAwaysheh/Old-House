<?php
// counters
static $subtotal = 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="./css/style.css?v=<?php echo time(); ?>">
</head>

<body>
	<?php include("./includes/header.php"); ?>
	<div class="location">
		<h1>Cart</h1>
	</div>
	</header>
	<main>
		<div class="order-table">
			<table>
				<tr class="table-header">
					<th class="table-header-cell">Image</th>
					<th class="table-header-cell">Product</th>
					<th class="table-header-cell">Price</th>
					<th class="table-header-cell">Quantity</th>
					<th class="table-header-cell">Total</th>
					<th class="table-header-cell">Remove</th>
				</tr>
				<!-- Example of looping through products -->
				<?php if (!empty($products)) : ?>
					<?php foreach ($products as $product) : ?>
						<?php if (isset($product["cat_id"])) : ?>
							<tr class="table-row">
								<td>
									<div class="product-image">
										<img src="./images/product-1.png" alt="Product 1">
									</div>
								</td>
								<td><?php echo $product["pro_name"]; ?></td>
								<td><?php echo $product["pro_price"]; ?> JOD</td>
								<td>
									<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
										<input class="quantity" type="number" value="1" min="1" max="99" name="quantity">
										<button type="submit">Update </button>
									</form>
								</td>
								<td><?php echo $product["pro_price"] * $_POST["quantity"]; ?> JOD</td>
								<td>
									<a href="#"><img class="remove-icon" src="./images/removeIcon.svg" alt="Remove"></a>
								</td>
							</tr>
						<?php endif; ?>
					<?php endforeach; ?>
					<!-- Repeat similar rows as needed -->
			</table>
		<?php else : ?>
			<p class="red-Color">There are no Products in The cart</p>
		<?php endif; ?>
		</div>
		<div class="check-out">
			<h3>Cart Totals</h3>
			<table>
				<tr class="table-row">
					<th class="table-header-cell">Subtotal</th>
					<td class="table-cell">148.5 JOD</td>
				</tr>
				<tr class="table-row">
					<th class="table-header-cell">Tax</th>
					<td class="table-cell">14.85 JOD</td>
				</tr>
				<tr class="table-row">
					<th class="table-header-cell">Total</th>
					<td class="table-cell">163.35 JOD</td>
				</tr>
			</table>
			<div class="checkout-button-container">
				<button class="checkout-button">Proceed to Checkout</button>
				<a href="./shop.php" class="checkout-button">Back To Shopping</a>
			</div>
		</div>
	</main>
	<?php include("./includes/footer.php"); ?>
	<script src="./js/index.js"></script>
</body>

</html>