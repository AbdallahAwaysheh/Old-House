<?php
include("../includes/connection2.php");
include("../includes/header.php");

// Fetch orders
$sql = "
SELECT 
    o.order_id,
    o.order_status,
    o.order_date,
    COUNT(od.order_details_id) AS product_count,
    CONCAT(c.cus_fname, ' ', c.cus_lname) AS customer_name,
    o.total_amount
FROM 
    shopping2.orders o
JOIN 
    shopping2.customers c ON o.cus_id = c.cus_id
LEFT JOIN 
    shopping2.order_details od ON o.order_id = od.order_id
GROUP BY 
    o.order_id
ORDER BY 
    o.order_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="row">
    <div class="col-md-12">
        <!-- DATA TABLE -->
        <h3 class="title-5 m-b-35">Order List</h3>
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <tr>
                    <th>Order ID</th>
                    <th>Order Status</th>
                    <th>Order Date</th>
                    <th>Product Count</th>
                    <th>Customer Name</th>
                    <th>Total Amount</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) : ?>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row["order_id"], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row["order_status"], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($row['order_date'])), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row["product_count"], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row["customer_name"], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row["total_amount"], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><a href="order_details.php?order_id=<?= htmlspecialchars($row["order_id"], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary btn-sm">View Details</a></td>
                            </tr>
                            <tr class="spacer"></tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center">No orders found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




<?php
$stmt->close();
$conn->close();
include("../includes/footer.php")
?>