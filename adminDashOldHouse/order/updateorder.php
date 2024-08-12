<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("../includes/connection2.php");
include_once("../includes/header.php");

// Check if user is logged in (assuming you have a session-based authentication)
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit();
// }

$message = '';

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = isset($_POST['new_status']) ? $_POST['new_status'] : '';
    $remark = isset($_POST['remark']) ? $_POST['remark'] : '';

    if ($order_id && $new_status) {
        // Update order status in the orders table
        $update_query = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('si', $new_status, $order_id);

        if ($stmt->execute()) {
            // Add to order_track_history
            $history_query = "INSERT INTO order_track_history (order_id, status, remark, posting_date) VALUES (?, ?, ?, NOW())";
            $hist_stmt = $conn->prepare($history_query);
            $hist_stmt->bind_param('iss', $order_id, $new_status, $remark);
            $hist_stmt->execute();

            $message = "Order status updated successfully.";
        } else {
            $message = "Error updating order status.";
        }
    } else {
        $message = "Invalid input. Please check the status.";
    }
}

// Fetch order details for display
$order_query = "SELECT order_id, order_status FROM orders WHERE order_id = ?";
$order_stmt = $conn->prepare($order_query);
$order_stmt->bind_param('i', $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order = $order_result->fetch_assoc();

?>

<div class="container mt-4">

    <header class="d-flex justify-content-between my-4">
        <h2>Update Order Status</h2>
        <div>
            <a href="order_details.php?order_id=<?= htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary">Back</a>
        </div>
    </header>

    <?php if ($message) : ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($order) : ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="order_id">Order ID:</label>
                <input type="number" class="form-control" id="order_id" name="order_id" value="<?php echo $order['order_id']; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="new_status">New Status:</label>
                <select class="form-control" id="new_status" name="new_status" required>
                    <option value="">Select Status</option>
                    <option value="Pending" <?php if ($order['order_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Processing" <?php if ($order['order_status'] == 'Processing') echo 'selected'; ?>>Processing</option>
                    <option value="Shipped" <?php if ($order['order_status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                    <option value="Delivered" <?php if ($order['order_status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                    <option value="Cancelled" <?php if ($order['order_status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>
            </div>
            <div class="form-group">
                <label for="remark">Remark:</label>
                <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Status</button>
        </form>
    <?php else : ?>
        <div class="alert alert-danger">Order not found.</div>
    <?php endif; ?>
</div>

<?php include_once("../includes/footer.php"); ?>