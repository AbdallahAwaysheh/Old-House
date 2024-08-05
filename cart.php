<?php
include("./includes/connect.php");
include("./includes/Shop/Cart.inc.php");

$cart = new Cart();
$cartItems = $cart->getCartItems();
$subtotal = $cart->getSubtotal();
$tax = $subtotal * 0.1;
$total = $subtotal + $tax;
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Cart</title>
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
				<?php if (!empty($cartItems)) : ?>
					<?php foreach ($cartItems as $product) : ?>
						<tr class="table-row">
							<td>
								<div class="product-image">
									<img src=".<?php echo htmlspecialchars($product['img_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['pro_name'], ENT_QUOTES, 'UTF-8'); ?>">
								</div>
							</td>
							<td><?php echo htmlspecialchars($product['pro_name'], ENT_QUOTES, 'UTF-8'); ?></td>
							<td><?php echo number_format($product['pro_price'], 2); ?> JOD</td>
							<td>
								<form action="./includes/cart-handler.php" method="POST">
									<input type="hidden" name="product_id" value="<?php echo $product['pro_id']; ?>">
									<input class="quantity" type="number" value="<?php echo $product['quantity']; ?>" min="1" max="99" name="product_quantity">
									<button type="submit">Update</button>
								</form>
							</td>
							<td><?php echo number_format($product['total_price'], 2); ?> JOD</td>
							<td>
								<a href="includes/cart-handler.php?remove=<?php echo $product['pro_id']; ?>"><img class="remove-icon" src="./images/removeIcon.svg" alt="Remove"></a>
							</td>
						</tr>
					<?php endforeach; ?>
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
					<td class="table-cell"><?php echo number_format($subtotal, 2); ?> JOD</td>
				</tr>
				<tr class="table-row">
					<th class="table-header-cell">Tax</th>
					<td class="table-cell"><?php echo number_format($tax, 2); ?> JOD</td>
				</tr>
				<tr class="table-row">
					<th class="table-header-cell">Total</th>
					<td class="table-cell"><?php echo number_format($total, 2); ?> JOD</td>
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