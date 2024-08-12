<?php
session_start();
include("../includes/connection2.php");
include("../includes/header.php");

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']); // Sanitize input to prevent SQL injection

    function storeTotal($total, $order_id)
    {
        try {
            $conn = new mysqli("localhost", "root", "", "shopping2");

            // Check connection
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }

            // Set charset to utf8mb4 (optional, but recommended)
            $conn->set_charset("utf8mb4");

            // Enable error reporting (similar to PDO::ERRMODE_EXCEPTION)
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        } catch (Exception $e) {
            echo "Connection failed: " . $e->getMessage();
            exit();
        }
        $sql = "UPDATE `orders` SET `total_amount`=$total WHERE `order_id` = $order_id ;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $stmt->close();
        $conn->close();
    }

    // SQL Query to fetch order details
    $sql = "
    SELECT 
        o.order_id,
        o.order_date,
        CONCAT(c.cus_fname, ' ', c.cus_lname) AS customer_name,
        c.shippingAddress,
        c.shippingCity,
        p.pro_name AS product_name,
        p.pro_price AS price_per_product,
        od.quantity,
        od.total AS total_per_product,
        o.total_amount AS total_order_amount
    FROM 
        shopping2.orders o
    JOIN 
        shopping2.customers c ON o.cus_id = c.cus_id
    JOIN 
        shopping2.order_details od ON o.order_id = od.order_id
    JOIN 
        shopping2.products p ON od.pro_id = p.pro_id
    WHERE 
        o.order_id = ?
    ORDER BY 
        o.order_date DESC;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $orderDetails = [];
    while ($row = $result->fetch_assoc()) {
        $orderDetails[] = $row;
    }

    // SQL Query to fetch order history
    $sql_history = "
    SELECT 
        track_id,
        status,
        remark,
        posting_date
    FROM 
        shopping2.order_track_history
    WHERE 
        order_id = ?
    ORDER BY 
        posting_date DESC;
    ";

    $stmt_history = $conn->prepare($sql_history);
    $stmt_history->bind_param("i", $order_id);
    $stmt_history->execute();
    $result_history = $stmt_history->get_result();
    $orderHistory = [];
    while ($row = $result_history->fetch_assoc()) {
        $orderHistory[] = $row;
    }

    $stmt->close();
    $stmt_history->close();
    $conn->close();
} else {
    echo "<h3>No order ID specified.</h3>";
    exit;
}
?>


<div class="container my-4">
    <header class="d-flex justify-content-between my-4">
        <h1>Order Details #<?php echo htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8'); ?></h1>
        <div>
            <a href="manage_order.php" class="btn btn-primary">Back</a>
        </div>
    </header>
    <div class="p-5 my-4">
        <?php if (!empty($orderDetails)) : ?>
            <?php
            $subTotal = 0;
            $deliveryFee = 5.00; // Fixed delivery fee
            $firstDetail = $orderDetails[0]; // Get the first detail to display general order info
            ?>
            <h3>Order ID:</h3>
            <p><?php echo htmlspecialchars($firstDetail["order_id"], ENT_QUOTES, 'UTF-8'); ?></p>
            <h3>Order Date:</h3>
            <p><?php echo htmlspecialchars($firstDetail["order_date"], ENT_QUOTES, 'UTF-8'); ?></p>
            <h3>Customer Name:</h3>
            <p><?php echo htmlspecialchars($firstDetail["customer_name"], ENT_QUOTES, 'UTF-8'); ?></p>
            <h3>Shipping Address:</h3>
            <p><?php echo htmlspecialchars($firstDetail["shippingAddress"] . " " . $firstDetail["shippingCity"], ENT_QUOTES, 'UTF-8'); ?></p>

            <div class="table-responsive">
                <table class="table table-data2">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderDetails as $detail) :
                            $sengleProductTotal = number_format($detail['price_per_product'], 2) * htmlspecialchars($detail['quantity'], ENT_QUOTES, 'UTF-8');
                            $subTotal += $sengleProductTotal;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($detail['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($detail['quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>JOD <?php echo number_format($detail['price_per_product'], 2); ?></td>
                                <td>JOD <?php echo $sengleProductTotal; ?></td>
                            </tr>
                            <tr class="spacer"></tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="text-right">Sub-Total</td>
                            <td>JOD <?php echo number_format($subTotal, 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right">2-3 Days Delivery</td>
                            <td>JOD <?php echo number_format($deliveryFee, 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right">Total</td>
                            <td>JOD <?php
                                    $total = $subTotal + $deliveryFee;
                                    echo number_format($total, 2);
                                    storeTotal($total, $order_id);
                                    ?></td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <h3>Order History</h3>
            <div class="table-responsive">
                <table class="table table-data2">
                    <thead>
                        <tr>
                            <th>Remark</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderHistory as $history) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($history['remark'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($history['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($history['posting_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <tr class="spacer"></tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="4"> <a href="updateorder.php?order_id=<?php echo htmlentities($order_id); ?>" title="Update order" class="btn btn-primary">Take Action</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <h3>No order found</h3>
        <?php endif; ?>
    </div>
</div>
<?php include("../includes/footer.php") ?>